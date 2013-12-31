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

	protected $_FFMPEG;
	protected $_OS;

	public function __construct()
	{
		// Assign FFMPEG path
		$this->_FFMPEG = Config::get('sonus::ffmpeg');

		// Return server operating system
		$this->_OS = strtoupper(substr(php_uname(), 0, 3));

		// Return supported operating systems
		$supported = Config::get('sonus::supported_os');

		// Check if OS is supported
		if (!in_array($this->_OS, $supported)) {
			// This OS is unsupported
			die('Unsupported operating system');
		}
	}

	/**
	 * Returns full path of FFMPEG
	 * @return string
	 */
	public function getConverterPath()
	{
		return $this->_FFMPEG;
	}

	/**
	 * Returns installed FFMPEG version
	 * @return array
	 */
	public function getConverterVersion()
	{
		// Run terminal command to retrieve version
		$command = $this->_FFMPEG.' -version';
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

	/**
	 * Returns all formats FFMPEG supports
	 * @return array
	 */
	public function getSupportedFormats()
	{
		// Run terminal command
		$command = $this->_FFMPEG.' -formats';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/(?P<mux>(D\s|\sE|DE))\s(?P<format>\S{3,11})\s/", $output, $parsed);

		// Combine the format and mux information into an array
		$formats = array_combine($parsed['format'], $parsed['mux']);

		return $formats;
	}

	/**
	 * Returns all audio formats FFMPEG can encode
	 * @return array
	 */
	public function getSupportedAudioEncoders()
	{
		// Run terminal command
		$command = $this->_FFMPEG.' -encoders';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/[A]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\S{3,20})\s/", $output, $parsed);

		return $parsed['format'];
	}

	/**
	 * Returns all video formats FFMPEG can encode
	 * @return array
	 */
	public function getSupportedVideoEncoders()
	{
		// Run terminal command
		$command = $this->_FFMPEG.' -encoders';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/[V]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\S{3,20})\s/", $output, $parsed);

		return $parsed['format'];
	}

	/**
	 * Returns all audio formats FFMPEG can decode
	 * @return array
	 */
	public function getSupportedAudioDecoders()
	{
		// Run terminal command
		$command = $this->_FFMPEG.' -decoders';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/[A]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\w{3,20})\s/", $output, $parsed);

		return $parsed['format'];
	}

	/**
	 * Returns all video formats FFMPEG can decode
	 * @return array
	 */
	public function getSupportedVideoDecoders()
	{
		// Run terminal command
		$command = $this->_FFMPEG.' -decoders';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/[V]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\w{3,20})\s/", $output, $parsed);

		return $parsed['format'];
	}

	/**
	 * Returns boolean if FFMPEG is able to encode to this format
	 * @param  string $format FFMPEG format name
	 * @return boolean
	 */
	public static function canEncode($format)
	{
		// Get an array with all supported encoding formats
		$app     = new Sonus;
		$formats = array_merge($app->getSupportedAudioEncoders(), $app->getSupportedVideoEncoders());

		// Return boolean if they can be encoded or not
		if(!in_array($format, $formats)) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Returns boolean if FFMPEG is able to decode to this format
	 * @param  string $format FFMPEG format name
	 * @return boolean
	 */
	public static function canDecode($format)
	{
		// Get an array with all supported encoding formats
		$app     = new Sonus;
		$formats = array_merge($app->getSupportedAudioDecoders(), $app->getSupportedVideoDecoders());

		// Return boolean if they can be encoded or not
		if(!in_array($format, $formats)) {
			return false;
		} else	{
			return true;
		}
	}

	/**
	 * Returns array with file information
	 * @param  string $filepath Full path to the file
	 * @return array
	 */
	public static function getMediaInfo($filepath)
	{
		$app	 = new Sonus;
		$command = $app->_FFMPEG.' -i '.$filepath.' 2>&1';
		$output  = shell_exec($command);

		$metadata 	= self::_extractFromString($output, 'Metadata:', 'encoder');  // TODO: Test with a file that has metadata
		$duration 	= self::_extractFromString($output, 'Duration:', ', start:'); // TODO: Return hh:mm:ss.mm as array
		$bitrate 	= self::_extractFromString($output, 'bitrate:', ' Stream');
		$video 		= self::_extractFromString($output, 'Video:', ' Stream', true);
		$audio 		= self::_extractFromString($output, 'Audio:', 'At least', true);

		$output 	= array(
			"Metadata" => $metadata,
			"Duration" => $duration,
			"Bitrate"  => $bitrate,
			"Video"    => $video,
			"Audio"    => $audio);

		return $output;
	}

	/**
	 * Extracts information from a string when given a beggining and end needle
	 * @param  string  $string    Haystack
	 * @param  string  $start     Needle for starting extraction
	 * @param  string  $end       Needle to stop extraction
	 * @param  boolean $array     Item should be returned as an array
	 * @param  string  $delimiter Delimiter
	 * @return string             Retrieved information from string
	 * @return array 			  Array with exploded elements from string
	 */
	private static function _extractFromString($string, $start, $end, $array = false, $delimiter = ',')
	{
		// Get lenght of start string
		$startLen  	= strlen($start);

		// Return piece of string requested
		$output 	= strstr(strstr($string, $start), $end, true);

		// Trim whitespace and remove start parameter
		$output 	= trim(substr($output, $startLen));

		// If requested, process output to array
		if($array === true) {
			// Explode string using given delimiter
			$explode = explode($delimiter, $output);

			// Set output as array
			$output  = array();

			// Loop through each item and trim whitespaces
			foreach($explode as $item) {
				$output[] = trim($item);
			}
		}

		return $output;

	}

}