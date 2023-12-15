<?php

namespace App\Http\Controllers;

use App\Contracts\RestController;
use App\Messages\TicketActivity;
use App\Models\Activity;
use App\Models\Project;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use App\Tools;
use Alograg\StrTools;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Activities extends RestController
{
    const MODEL = Activity::class;
    const ticket = Ticket::class;
    const project = Project::class;

    /**
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function allOf($targetType, $targetId)
    {
        $m = get_called_class()::MODEL;
        $target = constant(self::class . '::' . $targetType);
        $all = $m::whereTargetType($target)->whereTargetId($targetId)
            ->orderBy('created_at', 'DESC')->get();

        return $this->respond(Response::HTTP_OK, $all);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function satisfactions()
    {
        $m = get_called_class()::MODEL;
        $all = $m::selectRaw("id, user_id, CAST(JSON_VALUE(data , '$.satisfaction') as int) as satisfaction")
            ->whereRaw("JSON_VALUE(data , '$.satisfaction')")->get();

        return $this->respond(Response::HTTP_OK, $all);
    }

    /**
     * @param  Request  $request
     *
     * @return \Illuminate\Http\JsonResponse|Response|\Laravel\Lumen\Http\ResponseFactory
     */
    public function addTo(Request $request, $targetType, $targetId)
    {
        $targetClass = constant(self::class . '::' . $targetType);
        $m = get_called_class()::MODEL;
        $this->validate($request, $m::$rules);
        $activity = new $m();
        $getFillable = $activity->getFillable();
        foreach ($getFillable as $key) {
            if ($key == 'message')
                $activity->{$key} = $request->get($key);
            else
                $activity->{$key} = json_decode($request->get($key), true);
        }
        $target = $targetClass::find($targetId);
        $data = $activity->data;
        $files = $request->file('files');
        if ($files) {
            $destination = storage_path('private/guce/');
            $destination .= Str::lower(class_basename($targetClass)) . DIRECTORY_SEPARATOR;
            $destination .= $targetId . DIRECTORY_SEPARATOR;
            foreach ($files as $file) {
                $fileName = Str::snake($file->getClientOriginalName());
                if (file_exists($destination . $fileName)) {
                    $fileName = date('Ymd-his-') . $fileName;
                }
                $file->move($destination, $fileName);
                $data['files'][] = $fileName;
            }
        }
        $participants = $target->participants;
        $newStatus = Arr::get($data, 'toStatus', $target->status);
        if ($target->status != $newStatus)  $activity->type = $activity->type | Activity::TYPE_STATUS;
        if ($activity->type & Activity::TYPE_STATUS) {
            Arr::set($data, 'fromStatus', $target->status);
            $target->status = $newStatus;
            if ($target instanceof Ticket && Arr::get($data, 'takeItBy')) {
                $target->take_by = Arr::get($data, 'takeItBy', Auth::user()->id);
                $participants->push($target->take_by);
            }
        }
        $uniqueParticipants = $participants->push(Auth::user()->id)->unique();
        if ($uniqueParticipants->diff($target->participants)->count()) {
            $target->participants = $uniqueParticipants;
        }
        $activity->data = $data;
        //if ($activity->type & Activity::TYPE_MESSAGE) {}
        // if ($activity->type & Activity::TYPE_CLIENT) {}
        // if ($activity->type & Activity::TYPE_OTHER) {}
        if ($target->isDirty()) $activity->type = $activity->type | Activity::TYPE_UPDATE;
        $target->activities()->save($activity);
        $target->stats = self::updateTimers($target);
        if ($activity->type & Activity::TYPE_UPDATE) {
            if ($target->isDirty()) $target->save();
            else $target->touch();
        }
        if ($activity->type & Activity::TYPE_MESSAGE) {
            try {
                $users = User::find($target->participants);
                Mail::to($users)->send(new TicketActivity('messages', $activity->user, $target, $activity));
                $ccTo = $target->activities->reduce(function (Collection $carry, Activity $item) {
                    return $carry->merge(Arr::get($item->data, 'ccTo', []))->unique();
                }, collect(Arr::get($data, 'ccTo', [])));
                if (count($ccTo)) {
                    $ccToMails = $ccTo->reduce(function ($carry, $item) {
                        $email = StrTools::extractEmails($item);
                        if ($email) $carry[] = ['email' => $email[0]];
                        return $carry;
                    }, []);
                    if (count($ccToMails)) Mail::to($ccToMails)
                        ->send(new TicketActivity('notification-tier', $activity->user, $target, $activity));
                }
            } catch (\Exception $exception) {
                Tools::teamsAlert('Server Mail error: ' . $exception->getMessage());
            }
        }

        return $this->respond(Response::HTTP_CREATED, $activity);
    }

    static protected function addToCrono(&$crono, $type, $start, $end, $businessHours)
    {
        $times = Tools::openHourCalculation($start, $end, $businessHours);
        Arr::set($crono, $type, Arr::get($crono, $type, 0) + $times->open);
    }

    static public function updateTimers($target)
    {
        Tools::loadHolyDays();
        /**@var Ticket|Project $targetClass */
        $targetClass = get_class($target);
        /**@var Ticket|Project $target */
        $activities = $target->activities->sortBy('created_at');
        /**@var Service $service */
        $service = $target->service;
        $businessHours = $service->settings['opening'];
        date_default_timezone_set(Arr::get($service->settings, 'timezone', 'UTC'));
        $take_it = 0;
        $resolved = 0;
        $workAround = 0;
        $closed = 0;
        $crono = [0, 0];
        $lastActivityAt = Carbon::now();
        Log::debug("Ticket at: " . $target->created_at);
        Log::debug("Business Hours: " . json_encode($businessHours));
        /**@var Activity|null $lastActivity */
        $lastActivity = null;
        /**@var Activity $activity */
        foreach ($activities as $activity) {
            Log::debug("---------------------------------------");
            if ($lastActivity) Log::debug("Last Activity " . $lastActivity->id . " created_at : " . $lastActivityAt);
            Log::debug("Activity id : " . $activity->id);
            $activityAt = $activity->created_at;
            Log::debug("Activity created_at : " . $activityAt);
            $fromStatus = Arr::get($activity->data, 'fromStatus');
            $toStatus = Arr::get($activity->data, 'toStatus');
            $isStatusChange = ($activity->type & Activity::TYPE_STATUS && $activity->type & Activity::TYPE_UPDATE);
            Log::debug("isStatusChange : " . json_encode([$isStatusChange, $activity->type]));
            Log::debug("fromStatus : " . json_encode($fromStatus));
            Log::debug("toStatus : " . json_encode($toStatus));
            if ($isStatusChange && $fromStatus & $targetClass::STATUS_OPEN) {
                $take_it_all = Tools::openHourCalculation($target->created_at, $activityAt, $businessHours);
                Log::debug("take_it_all: " . json_encode($take_it_all));
                $take_it = $take_it_all->open;
                if (!$take_it) {
                    $take_it = $take_it_all->real;
                }
                Log::debug("take_it tcp : " . $target->created_at . " -> " . $activityAt . " = " . json_encode($take_it));
                $lastActivity = $activity;
                $lastActivityAt = $lastActivity->created_at;
                continue;
            }
            if ($isStatusChange) {
                Log::debug("Change status");
                $cronoType = (int)!!($activity->type & Activity::TYPE_CLIENT);
                Log::debug("Is client: " . $activity->type . " ; " . $cronoType);
                Log::debug("Crono is : " . json_encode($crono));
                self::addToCrono($crono, $cronoType, $lastActivityAt, $activityAt, $businessHours);
                Log::debug("Crono: " . $lastActivityAt . " -> " . $activityAt . " = " . json_encode($crono));
                Log::debug("Crono is : " . json_encode($crono));
                $times = Tools::openHourCalculation($target->created_at, $activityAt, $businessHours);
                if ($fromStatus & $targetClass::STATUS_COURS_CT && $toStatus & $targetClass::STATUS_COURS_CR) {
                    $workAround = $times->open - $crono[1];
                    Log::debug("workAround tct : " . $times->open . " - " . $crono[1] . " = " . json_encode($workAround));
                } else if ($fromStatus & $targetClass::STATUS_COURS_CR && $toStatus & $targetClass::STATUS_RESOLVED) {
                    $resolved = $times->open - $crono[1];
                    Log::debug("resolved tcr : " . $times->open . " - " . $crono[1] . " = " . json_encode($resolved));
                } else if ($fromStatus & $targetClass::STATUS_RESOLVED && $toStatus & $targetClass::STATUS_CLOSED) {
                    $closed = $times->open - $crono[1];
                }
                //self::addToCrono($crono, 0, $lastActivityAt, $activityAt, $businessHours);
            }
            $lastActivity = $activity;
            $lastActivityAt = $lastActivity->created_at;
            Log::debug("---------------------------------------");
        }
        date_default_timezone_set(env('APP_TIMEZONE', 'UTC'));
        return [
            "take_it" => $take_it,
            "in_hours" => !!Tools::openHourCalculation($target->created_at, $target->created_at->clone()->addMillisecond(), $businessHours)->open,
            "crono" => [
                'supplier' => $crono[0],
                'client' => $crono[1],
            ],
            "work_around" => $workAround,
            "resolved" => $resolved,
            "close" => $closed ?: $resolved
        ];
    }
}
