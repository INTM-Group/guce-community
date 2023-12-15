<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class Tools
{
    static $holydays = [];

    static public function openHourCalculation(Carbon $startDate, Carbon $endDate, $businessHours, $debug = false)
    {
        $result = (object)[
            'open' => 0,
            'real' => 0,
            'close' => 0,
            'total' => 0
        ];
        if (!$startDate || !$endDate) return null;
        if ($endDate->isBefore($startDate)) {
            return $result;
        }
        $current = $startDate;
        $result->real = $current->diffInMilliseconds($endDate);
        $startEndIsSame = $startDate->isSameDay($endDate);
        while ($current->isBefore($endDate)) {
            $currentWeekDay = $current->dayOfWeek;
            $businessDay = $businessHours[$currentWeekDay];
            if (!count($businessDay) || self::isHoliday($current)) {
                $nextDay = $current->clone()->addDay()->startOfDay();
                $result->close += $current
                    ->diffInMilliseconds($nextDay);
                if ($debug) print json_encode([
                    'inhabil',
                    $current->diffInMilliseconds($nextDay),
                    'close'
                ]) . PHP_EOL;
                $current = $nextDay;
                continue;
            }
            foreach ($businessDay as $workingHours) {
                $workingStartAt = $current->clone()->setTimeFromTimeString($workingHours[0]);
                $workingEndAt = $current->clone()->setTimeFromTimeString($workingHours[1]);
                if ($startEndIsSame) {
                    $result->open += max(min($endDate, $workingEndAt), $workingStartAt)
                        ->diffInMilliseconds(max($current, $workingStartAt));
                    if ($debug) print json_encode([
                        'startEndIsSame',
                        max(min($endDate, $workingEndAt), $workingStartAt)->diffInMilliseconds(max($current, $workingStartAt)),
                        'open'
                    ]) . PHP_EOL;
                    $result->open = max($result->open, 0);
                    return $result;
                }
                $currentIsBeforeWorkingStartAt = $current->isBefore($workingStartAt);
                $endDateIsAfterWorkingEndAt = $endDate->isAfter($workingEndAt);
                if ($currentIsBeforeWorkingStartAt) {
                    $result->close += $workingStartAt
                        ->diffInMilliseconds($current);
                    if ($debug) print json_encode([
                        'currentIsBeforeWorkingStartAt',
                        $workingStartAt->diffInMilliseconds($current),
                        'close'
                    ]) . PHP_EOL;
                }
                if ($endDateIsAfterWorkingEndAt) {
                    $endCurrentDay = $current->clone()->endOfDay();
                    $result->close += $endCurrentDay
                        ->diffInMilliseconds($workingEndAt);
                    if ($debug) print json_encode([
                        'endDateIsAfterWorkingEndAt',
                        $endCurrentDay->diffInMilliseconds($workingEndAt),
                        'close'
                    ]) . PHP_EOL;
                }
                if ($current->isAfter($workingStartAt) && $endDateIsAfterWorkingEndAt) {
                    $result->open += $workingEndAt->diffInMilliseconds($current);
                    if ($debug) print json_encode([
                        'endDateIsAfterWorkingEndAt and AfterWorkingStart',
                        $workingEndAt->diffInMilliseconds($current),
                        'open'
                    ]) . PHP_EOL;
                }
                if (
                    $workingEndAt->isAfter($workingStartAt) &&
                    $currentIsBeforeWorkingStartAt &&
                    $current->isBefore($workingEndAt) &&
                    $endDateIsAfterWorkingEndAt
                ) {
                    $result->open += $workingEndAt->diffInMilliseconds($workingStartAt);
                    if ($debug) print json_encode([
                        'WorkingEndIsAfterWorkingStart',
                        'CurrentIsBeforeWorkingStart',
                        'CurrentIsBeforeWorkingEnd',
                        'EndIsAfterWorkingEnd',
                        $workingEndAt->diffInMilliseconds($workingStartAt),
                        'open'
                    ]) . PHP_EOL;
                }
                if ($endDate->isBefore($workingEndAt) && !$startEndIsSame) {
                    $result->open += $endDate->diffInMilliseconds($workingStartAt);
                    if ($debug) print json_encode([
                        'EndIsBeforeWorkingEnd',
                        'IsNotSameDay',
                        $endDate->diffInMilliseconds($workingStartAt),
                        $endDate, $workingStartAt,
                        'open'
                    ]) . PHP_EOL;
                }
            }
            $current->addDay()->startOfDay();
        }
        $result->total = $result->open + $result->close;
        return (object)[
            'real' => max($result->real, 0),
            'open' => max($result->open, 0),
            'close' => max($result->close, 0),
            'total' => max($result->total, 0)
        ];
    }

    static public function loadHolyDays()
    {
        if (!count(self::$holydays)) {
            return self::$holydays;
        }
        $holydaysFiles = glob(storage_path('public/holydays/fr/*.json'));
        $holydays = [];
        foreach ($holydaysFiles as $holydaysFile) {
            $fileHolydays = json_decode(file_get_contents($holydaysFile), true);
            foreach ($fileHolydays as $holyDay) {
                if (!Arr::get($holyDay, 'counties'))
                    $holydays[] = Arr::get($holyDay, 'date');
            }
        }
        self::$holydays = $holydays;
        return self::$holydays;
    }

    static public function isHoliday(Carbon $current)
    {
        return in_array($current->format('Y-m-d'), self::$holydays);
    }

    static public function teamsAlert($msg)
    {
        $teamsHook = env('TEAMS_HOOK', false);
        if (!$teamsHook) {
            Log::debug($msg);
            return false;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $teamsHook);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'text' => $msg
        ]));
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            Log::error("TeamsWebHookError: " . curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }
}
