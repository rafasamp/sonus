<?php namespace Rafasamp\Sonus;

use Config;

/**
 * Laravel Audio Conversion Package
 *
 * This package is created to handle server-side conversion tasks using FFMPEG (http://www.fmpeg.org)
 *
 * @package    Laravel
 * @category   Bundle
 * @version    0.1
 * @author     Rafael Sampaio <rafaelsampaio@live.com>
 */

class Sonus
{
	
	protected $FFMPEG;

	public function __construct()
	{
		try 
		{
			$this->FFMPEG = Config::get('sonus::ffmpeg');
			if(file_exists($this->FFMPEG) === false)
			{
				throw new FileNotFoundException("Unable to access FFMPEG executable!");
			}

		} catch (Exception $e) {
			return $e;
		}
	}

}