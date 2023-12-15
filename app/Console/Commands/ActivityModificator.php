<?php

namespace App\Console\Commands;

use App\Http\Controllers\Activities;
use App\Models\Activity;
use App\Models\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class ActivityModificator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = <<<text
    guce:activity:modificator
        {ticket : Ticket ID}
text;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Modifier les activités de tickets';

    protected static $width = 90;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public static function getScreenSize()
    {
        preg_match_all("/rows.([0-9]+);.columns.([0-9]+);/", strtolower(exec('stty -a |grep columns')), $output);
        if (count($output)) {
            self::$width = $output[2][0];
        }
    }

    /**
     * Clear console
     *
     * @return void
     */
    protected static function clearTerminal()
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            system('cls');
        } else {
            system('clear');
        }
    }

    /**
     * Output Ticket info
     *
     * @param Ticket $name
     * @return int
     */
    protected function displayTicketInfo(Ticket $ticket)
    {
        self::getScreenSize();
        $this->output->title('Ticket : ' . $ticket->id . ' - ' . $ticket->title);
        $activitiesCount = $ticket->activities->count();
        $this->output->section('Activities : ' . $activitiesCount);
        return $activitiesCount;
    }

    protected static function getDataForTable($activity)
    {
        $displayStatus = [];
        $displayStatus[] = $activity->type & Activity::TYPE_UNKNOWN ? 'UNKNOWN' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_UPDATE ? 'UPDATE' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_STATUS ? 'STATUS' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_MESSAGE ? 'MESSAGE' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_CLIENT ? 'CLIENT' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_16 ? '16' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_32 ? '32' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_64 ? '64' : '';
        $displayStatus[] = $activity->type & Activity::TYPE_OTHER ? 'OTHER' : '';
        $fromStatus = [];
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_DISABLED ? 'DISABLED' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_VALID ? 'VALID' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_OPEN ? 'OPEN' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_COURS_CT ? 'COURS_CT' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_ATTENTE_CT ? 'ATTENTE_CT' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_COURS_CR ? 'COURS_CR' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_ATTENTE_CR ? 'ATTENTE_CR' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_RESOLVED ? 'RESOLVED' : '';
        $fromStatus[] = Arr::get($activity, 'data.fromStatus') & Ticket::STATUS_CLOSED ? 'CLOSED' : '';
        $toStatus = [];
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_DISABLED ? 'DISABLED' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_VALID ? 'VALID' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_OPEN ? 'OPEN' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_COURS_CT ? 'COURS_CT' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_ATTENTE_CT ? 'ATTENTE_CT' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_COURS_CR ? 'COURS_CR' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_ATTENTE_CR ? 'ATTENTE_CR' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_RESOLVED ? 'RESOLVED' : '';
        $toStatus[] = Arr::get($activity, 'data.toStatus') & Ticket::STATUS_CLOSED ? 'CLOSED' : '';
        return [
            $activity->id,
            $activity->created_at,
            $activity->type . ' : ' . implode(', ', array_filter($displayStatus)),
            $activity->user->email,
            Str::limit(strip_tags($activity->message ?: '-'), (self::$width - 20) / 2),
            json_encode(Arr::only($activity->data, ['toStatus', 'fromStatus'])),
            Arr::get($activity, 'data.fromStatus', '-') . ' : ' . implode(', ', array_filter($fromStatus)),
            Arr::get($activity, 'data.toStatus', '-') . ' : ' . implode(', ', array_filter($toStatus)),
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        self::clearTerminal();
        $ticket = Ticket::with('activities.user')->find($this->argument('ticket'));
        $this->bar = $this->output->createProgressBar($this->displayTicketInfo($ticket));
        $this->bar->start();
        $this->newLine();
        $ticket->activities->each(function (Activity $activity) use ($ticket) {
            $row = [];
            $row[] = self::getDataForTable($activity);
            $this->output->horizontalTable([
                'id',
                'Created',
                'Type',
                'User',
                'Message',
                'data',
                'fromStatus',
                'toStatus',
            ], $row);
            $actionsOptions = [
                'Next',
                'Change activity type',
                'Change from and to status'
            ];
            $actions = $this->choice(
                'Actions: (# separated by commas)',
                $actionsOptions,
                null,
                null,
                true
            );
            if(in_array($actionsOptions[0], $actions)) return true;
            if (in_array($actionsOptions[1], $actions)) {
                $this->info(implode(' | ', [
                    'UPDATE :' . Activity::TYPE_UPDATE,
                    'STATUS: ' . Activity::TYPE_STATUS,
                    'MESSAGE: ' . Activity::TYPE_MESSAGE,
                    'CLIENT: ' . Activity::TYPE_CLIENT,
                    '16: ' . Activity::TYPE_16,
                    '32: ' . Activity::TYPE_32,
                    '64: ' . Activity::TYPE_64,
                    'OTHER: ' . Activity::TYPE_OTHER,
                ]));
                $activity->type = array_sum($this->choice(
                    'Types: (value separated by commas)',
                    [
                        Activity::TYPE_UPDATE,
                        Activity::TYPE_STATUS,
                        Activity::TYPE_MESSAGE,
                        Activity::TYPE_CLIENT,
                        Activity::TYPE_16,
                        Activity::TYPE_32,
                        Activity::TYPE_64,
                        Activity::TYPE_OTHER,
                    ],
                    null,
                    null,
                    true
                ));
            }
            if (in_array($actionsOptions[2], $actions)) {
                $this->info(implode(' | ', [
                    'VALID :' . Ticket::STATUS_VALID,
                    'OPEN : ' . Ticket::STATUS_OPEN,
                    'COURS_CT : ' . Ticket::STATUS_COURS_CT,
                    'ATTENTE_CT : ' . Ticket::STATUS_ATTENTE_CT,
                    'COURS_CR : ' . Ticket::STATUS_COURS_CR,
                    'ATTENTE_CR : ' . Ticket::STATUS_ATTENTE_CR,
                    'RESOLVED : ' . Ticket::STATUS_RESOLVED,
                    'CLOSED : ' . Ticket::STATUS_CLOSED,
                ]));
                $fromStatusNewVal = array_sum($this->choice(
                    'from Status: (value separated by commas)',
                    [
                        Ticket::STATUS_VALID,
                        Ticket::STATUS_OPEN,
                        Ticket::STATUS_COURS_CT,
                        Ticket::STATUS_ATTENTE_CT,
                        Ticket::STATUS_COURS_CR,
                        Ticket::STATUS_ATTENTE_CR,
                        Ticket::STATUS_RESOLVED,
                        Ticket::STATUS_CLOSED,
                    ],
                    null,
                    null,
                    true
                ));
                $toStatusNewVal = array_sum($this->choice(
                    'to Status: (value separated by commas)',
                    [
                        Ticket::STATUS_VALID,
                        Ticket::STATUS_OPEN,
                        Ticket::STATUS_COURS_CT,
                        Ticket::STATUS_ATTENTE_CT,
                        Ticket::STATUS_COURS_CR,
                        Ticket::STATUS_ATTENTE_CR,
                        Ticket::STATUS_RESOLVED,
                        Ticket::STATUS_CLOSED,
                    ],
                    null,
                    null,
                    true
                ));
                var_dump($fromStatusNewVal);
                var_dump($toStatusNewVal);
                $activityData = $activity->data;
                Arr::set($activityData, 'fromStatus', $fromStatusNewVal);
                Arr::set($activityData, 'toStatus', $toStatusNewVal);
                $activity->data = $activityData;
            }
            if (count($actionsOptions)) {
                $row[] = self::getDataForTable($activity);
                $this->output->horizontalTable([
                    'id',
                    'Created',
                    'Type',
                    'User',
                    'Message',
                    'data',
                    'fromStatus',
                    'toStatus',
                ], $row);
                if ($this->confirm('Valid changes?')) $activity->save() ? $this->output->success('SAVED') : $this->output->error('SAVE');
                else $activity->refresh();
            }
            if (!$this->confirm('Next?', true)) return false;
            self::clearTerminal();
            $this->displayTicketInfo($ticket);
            $this->bar->advance();
            $this->newLine();
        });
        self::clearTerminal();
        $this->displayTicketInfo($ticket);
        $this->bar->finish();
        $this->newLine();
        $this->info('Before');
        echo json_encode($ticket->stats, JSON_PRETTY_PRINT) . PHP_EOL;
        $ticket->stats = Activities::updateTimers($ticket);
        $this->info('After');
        echo json_encode($ticket->stats, JSON_PRETTY_PRINT) . PHP_EOL;
        $ticket->save();
    }
}
