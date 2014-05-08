<?php namespace Rafasamp\Sonus;

class Helpers
{
    /**
     * Extracts seconds from HH:MM:SS string
     * @param  string HH:MM:SS formatted value
     * @return string
     */
    public static function timestampToSeconds($string)
    {
        // Extract hour, minute, and seconds
        $time = explode(":", $string);
        
        // Convert to seconds (round up to nearest second)
        $secs = ($time[0] * 3600) + ($time[1] * 60) + (ceil($time[2]));
        return $secs;
    }

    /**
     * Converts seconds to HH:MM:SS string
     * @param  integer $int seconds
     * @return string
     */
    public static function secondsToTimestamp($int)
    {
        // Set default timezone to UTC avoiding mktime errors
        date_default_timezone_set('UTC');

        $output = date('H:i:s', mktime(0, 0, $int));
        return $output;
    }

    /**
     * Returns percent completion of current conversion task
     * @param  integer $current current time in seconds
     * @param  integer $total   total time in seconds
     * @return integer
     */
    public static function progressPercentage($current, $total)
    {
        // Round to the nearest percent
        $output = ceil(($current / $total) * 100);
        return $output;
    }
}