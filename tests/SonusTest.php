<?php

use Rafasamp\Sonus\Sonus;

class SonusIncorrectInputTest extends PHPUnit_Framework_TestCase
{

    /**
     * Contains object handle of Sonus class
     * 
     * @return void
     */
    var $sonus;

    /**
     * Setup Sonus object
     * 
     * @return Rafasamp\Sonus\Sonus
     */
    function setUp()
    {
        $this->sonus = new Sonus;
    }

    /**
     * Input function must receive a string
     * 
     * @return void
     */
    public function testInputMustBeAString()
    {
        $result     = $this->sonus->input(1);
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Output function must receive a string
     * 
     * @return void
     */
    public function testOutputMustBeAString()
    {
        $result     = $this->sonus->output(1);
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Timelimit function must receive a number
     * 
     * @return void
     */
    public function testTimelimitMustBeNumeric()
    {
        $result     = $this->sonus->timelimit('word');
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Codec function must not receive a null value for codec name
     * 
     * @return void
     */
    public function testCodecNameMustNotBeNull()
    {
        $result     = $this->sonus->codec('', 'test');
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Codec function must receive a type of audio or video
     * 
     * @return void
     */
    public function testCodecTypeMustBeAudioOrVideo()
    {
        $result     = $this->sonus->codec('test', 'test');
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Bitrate function must receive a number
     * 
     * @return void
     */
    public function testBitrateMustBeNumeric()
    {
        $result     = $this->sonus->bitrate('128kbps', 'audio');
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Bitrate function must receive a type of audio or video
     * 
     * @return void
     */
    public function testBitrateTypeMustBeAudioOrVideo()
    {
        $result     = $this->sonus->codec('128', 'sound');
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Channels function must receive a number
     * 
     * @return void
     */
    public function testChannelsMustBeNumeric()
    {
        $result     = $this->sonus->channels('stereo');
        $expected   = false;
        $this->assertTrue($result == $expected);
    }

    /**
     * Frequency function must receive a number
     * 
     * @return void
     */
    public function testFrequencyMustBeNumeric()
    {
        $result     = $this->sonus->frequency('44000hz');
        $expected   = false;
        $this->assertTrue($result == $expected);
    }
}