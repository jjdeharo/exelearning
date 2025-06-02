<?php

namespace App\Util\net\exelearning\Util;

/**
 * DateUtil.
 *
 * Utility functions for working with dates
 */
class DateUtil
{
    /**
     * Returns a DateInterval created from the number of seconds passed as param.
     *
     * @param int $seconds
     *
     * @return \DateInterval
     */
    public static function getSecondsDateInterval($seconds)
    {
        $interval = new \DateInterval('PT'.$seconds.'S');

        return $interval;
    }
}
