<?php

use Rafasamp\Sonus\Helpers;

class HelpersTest extends PHPUnit_Framework_TestCase
{

    /**
     * Timestamp to Seconds function
     *
     * @return void
     */
    public function testTimestampToSeconds()
    {
        $result     = Helpers::timestampToSeconds('01:01:01');
        $expected   = 3661;
        $this->assertTrue($result == $expected);
    }

    /**
     * Seconds to Timestamp function
     *
     * @return void
     */
    public function testSecondsToTimestamp()
    {
        $result     = Helpers::secondsToTimestamp(3661);
        $expected   = '01:01:01';
        $this->assertTrue($result == $expected);
    }

    /**
     * Progress Percentage function
     *
     * @return void
     */
    public function testProgressPercentage()
    {
        $result     = Helpers::progressPercentage(3660, 3660);
        $expected   = 100;
        $this->assertTrue($result == $expected);
    }
}