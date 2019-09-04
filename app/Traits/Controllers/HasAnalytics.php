<?php

namespace App\Traits\Controllers;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

trait HasAnalytics
{
    protected function getDateIntervals($startTimestamp, $endTimestamp, $timezone, $datePoint)
    {
        $startDay = Carbon::createFromTimestamp($startTimestamp, $timezone)->startOfDay();
        $endDay = Carbon::createFromTimestamp($endTimestamp, $timezone);

        if (($endDay->diffInDays($startDay) > 31) && $datePoint === 'day') {
            abort(400, 'Can\'t calculate daily analytics for date range bigger than 31 days.');
        }

        $datePeriod = CarbonPeriod::since($startDay)->{$datePoint}()->until($endDay)->toArray();
        // bring back dates to UTC for db queries, as in db timezone is set to utc
        $startDay->setTimezone('UTC');
        $endDay->setTimezone('UTC');

        return [
            'period' => $datePeriod,
            'intervals' => end_dates($datePeriod, $endDay),
            'start_day' => $startDay,
            'end_day'   => $endDay,
        ];
    }
}