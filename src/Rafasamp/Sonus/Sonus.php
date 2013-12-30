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

	/**
	 * Returns full path of FFMPEG
	 * @return string
	 */
	public function getConverterPath()
	{
		return $this->FFMPEG;
	}

	/**
	 * Returns installed FFMPEG version
	 * @return array
	 */
	public function getConverterVersion()
	{
		// Run terminal command to retrieve version
		$command = $this->FFMPEG.' -version';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match("/ffmpeg version (?P<major>[0-9]{0,3}).(?P<minor>[0-9]{0,3}).(?P<revision>[0-9]{0,3})/", $output, $parsed);

		// Assign array with variables
		$version = array(
			'major' => $parsed['major'],
			'minor' => $parsed['minor'],
			'rev'   => $parsed['revision']
			);

		return $version;
	}

}