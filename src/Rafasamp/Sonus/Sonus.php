<?php namespace Rafasamp\Sonus;

use Config;

/**
 * Laravel Audio Conversion Package
 *
 * This package is created to handle server-side conversion tasks using FFMPEG (http://www.fmpeg.org)
 *
 * @package    Laravel
 * @category   Bundle
 * @version    0.2
 * @author     Rafael Sampaio <rafaelsampaio@live.com>
 */

class SonusBase {

	/**
	 * Returns operating system FFMPEG is running
	 * @return string  Server name (WIN => Windows, LIN => Linux, DAR => Mac OSX)
	 * @return boolean False if server is not supported
	 */
	protected static function _serverOS()
	{
		// Server operating system
		$server_os    = strtoupper(substr(php_uname(), 0, 3));

		// Return supported operating systems
		$supported_os = Config::get('sonus::supported_os');

		// Check if OS is supported
		if (!in_array($server_os, $supported_os)) {
			return false;
		} else {
			return $server_os;
		}
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
	protected static function _extractFromString($string, $start, $end, $array = false, $delimiter = ',')
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

class Sonus extends SonusBase
{
	/**
	 * Returns full path of FFMPEG
	 * @return string
	 */
	protected static function getConverterPath()
	{
		return Config::get('sonus::ffmpeg');
	}

	/**
	 * Returns full path of FFPROBE
	 * @return string
	 */
	protected static function getProbePath()
	{
		return Config::Get('sonus::ffprobe');
	}

	/**
	 * Returns installed FFMPEG version
	 * @return array
	 */
	public static function getConverterVersion()
	{
		// Run terminal command to retrieve version
		$command = self::getConverterPath().' -version';
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
	public static function getSupportedFormats()
	{
		// Run terminal command
		$command = self::getConverterPath().' -formats';
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
	public static function getSupportedAudioEncoders()
	{
		// Run terminal command
		$command = self::getConverterPath().' -encoders';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/[A]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\S{3,20})\s/", $output, $parsed);

		return $parsed['format'];
	}

	/**
	 * Returns all video formats FFMPEG can encode
	 * @return array
	 */
	public static function getSupportedVideoEncoders()
	{
		// Run terminal command
		$command = self::getConverterPath().' -encoders';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/[V]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\S{3,20})\s/", $output, $parsed);

		return $parsed['format'];
	}

	/**
	 * Returns all audio formats FFMPEG can decode
	 * @return array
	 */
	public static function getSupportedAudioDecoders()
	{
		// Run terminal command
		$command = self::getConverterPath().' -decoders';
		$output  = shell_exec($command);

		// PREG pattern to retrive version information
		preg_match_all("/[A]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\w{3,20})\s/", $output, $parsed);

		return $parsed['format'];
	}

	/**
	 * Returns all video formats FFMPEG can decode
	 * @return array
	 */
	public static function getSupportedVideoDecoders()
	{
		// Run terminal command
		$command = self::getConverterPath().' -decoders';
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
		$formats = array_merge(self::getSupportedAudioEncoders(), self::getSupportedVideoEncoders());

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
		$formats = array_merge(self::getSupportedAudioDecoders(), self::getSupportedVideoDecoders());

		// Return boolean if they can be encoded or not
		if(!in_array($format, $formats)) {
			return false;
		} else	{
			return true;
		}
	}

	/**
	 * Returns array with file information
	 * @param  string $input file input
	 * @return array
	 */
	public static function getMediaInfo($input)
	{
		if (substr($input, 0, 1) !== '-i') {
			$input = '-i '.$input;
		}
		$command  = self::getProbePath().' -v quiet -print_format json -show_format -show_streams '.$input;
		$output   = shell_exec($command);
		$output   = json_decode($output, true);

		return $output;
	}

	/**
	 * Input files
	 * @var array
	 */
	protected $input = array();

	/**
	 * Output files
	 * @var array
	 */
	protected $output = array();

	/**
	 * Contains the combination of all parameters set by the user
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * Contains the job progress id
	 * @var string
	 */
	protected $progress;

	/**
	 * Returns object instance for chainable methods
	 * @return object
	 */
	public static function convert() {
		$sonus = new Sonus;
		return $sonus;
	}

	/**
	 * Adds an input file
	 * @param  string $var filename
	 * @return boolean
	 */
	public function input($var)
	{
		if (!is_string($var)) {
			return false;

		} else {
			array_push($this->input, '-i '.$var);
			return $this;
		}
	}

	/**
	 * Adds an output file
	 * @param  string $var filename
	 * @return boolean
	 */
	public function output($var)
	{
		if (!is_string($var)) {
			return false;

		} else {
			array_push($this->output, $var);
			return $this;
		}
	}

	/**
	 * Overwrite output file if it exists
	 * @param  boolean $var
	 * @return boolean
	 */
	public function overwrite($var = true)
	{
		switch ($var) {
			case true:
				array_push($this->parameters, '-y');
				return $this;
				break;

			case false:
				array_push($this->parameters, '-n');
				return $this;
				break;

			default:
				return false;
				break;
		}
	}

	/**
	 * Stop running FFMPEG after X seconds
	 * @param  int $var seconds
	 * @return boolean
	 */
	public function timelimit($var)
	{
		if (!is_numeric($var)) {
			return false;

		} else {
			array_push($this->parameters, '-timelimit '.$var);
			return $this;
		}
	}

	/**
	 * Sets the codec used for the conversion
	 * https://trac.ffmpeg.org/wiki/AACEncodingGuide
	 * https://trac.ffmpeg.org/wiki/Encoding%20VBR%20(Variable%20Bit%20Rate)%20mp3%20audio
	 * @param   string $var ffmpeg codec name
	 * @return  boolean
	 */
	public function codec($var, $type = 'audio')
	{
		if (is_null($var)) {
			return false;

		} else {
			switch($type) {
				case 'audio':
					array_push($this->parameters, '-c:a '.$var);
					return $this;
					break;

				case 'video':
					array_push($this->parameters, '-c:v '.$var);
					return $this;
					break;

				default:
					return false;
					break;
			}
		}
	}

	/**
	 * Sets the constant bitrate
	 * @param int $var bitrate
	 * @return boolean
	 */
	public function bitrate($var, $type = 'audio')
	{
		// Value must be numeric
		if (!is_numeric($var)) {
			return false;

		} else {
			switch ($type) {
				case 'audio':
					array_push($this->parameters, '-b:a '.$var.'k');
					return $this;
					break;

				case 'video':
					array_push($this->parameters, '-b:v '.$var.'k');
					return $this;
					break;

				default:
					return false;
					break;
			}			
		}
	}

	/**
	 * Sets the number of audio channels
	 * https://trac.ffmpeg.org/wiki/AudioChannelManipulation
	 * @param string $var
	 * @return boolean
	 */
	public function channels($var)
	{
		if (!is_numeric($var)) {
			return false;

		} else {
			array_push($this->parameters, '-ac '.$var);
			return $this;
		}
	}

	/**
	 * Sets audio frequency rate
	 * http://ffmpeg.org/ffmpeg.html#Audio-Options
	 * @param int $var frequency
	 * @return boolean
	 */
	public function frequency($var)
	{
		// Value must be numeric
		if (!is_numeric($var)) {
			return false;

		} else {
			array_push($this->parameters, '-ar:a '.$var);
			return $this;
		}
	}

	/**
	 * Performs conversion
	 * @param  string $arg user arguments
	 * @return string      tracking code
	 * @return boolean     false on error
	 */
	public function go($arg = null)
	{
		// Assign converter path
		$ffmpeg = self::getConverterPath();

		// Check if user provided raw arguments
		if (is_null($arg)) {
			// If not, use the prepared arguments
			$arg = implode(" ", $this->parameters);
		}

		// Return input and output files
		$input  = implode(" ", $this->input);
		$output = implode(" ", $this->output);

		// Prepare the command
		$cmd    = escapeshellcmd($ffmpeg.' '.$input.' '.$arg.' '.$output);

		// Get OS version
		$os     = self::_serverOS();

		// Check if progress reporting is enabled
		if (Config::get('sonus::progress') === true) {
			// Publish progress to this ID
			$this->progress = md5($cmd);
			$cmd            = $cmd.' -progress '.$this->progress.'.txt';
		}

		// Initiate a command compatible with each OS
		switch ($os) {
			case 'WIN':
				return shell_exec($cmd);
				break;
			
			case 'DAR':
				return shell_exec($cmd.' 2>&1');
				break;

			case 'LIN':
				return shell_exec($cmd.' 2>&1');
				break;

			default:
				return false;
				break;
		}
	}
}