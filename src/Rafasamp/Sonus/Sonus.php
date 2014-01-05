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
	 * @param  string $filepath Full path to the file
	 * @return array
	 */
	public static function getMediaInfo($filepath)
	{
		$command  = self::getConverterPath().' -i '.$filepath.' 2>&1';
		$output   = shell_exec($command);

		$metadata = self::_extractFromString($output, 'Metadata:', 'encoder');  // TODO: Test with a file that has metadata
		$duration = self::_extractFromString($output, 'Duration:', ', start:'); // TODO: Return hh:mm:ss.mm as array
		$bitrate  = self::_extractFromString($output, 'bitrate:', ' Stream');
		$video    = self::_extractFromString($output, 'Video:', ' Stream', true);
		$audio    = self::_extractFromString($output, 'Audio:', 'At least', true);

		$output 	= array(
			"Metadata" => $metadata,
			"Duration" => $duration,
			"Bitrate"  => $bitrate,
			"Video"    => $video,
			"Audio"    => $audio);

		return $output;
	}

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

	public function execute($input, $output, $arg = null)
	{
		// Assign converter path
		$ffmpeg = self::getConverterPath();

		// Insert input flag
		$input  = '-i '.$input;

		// Check if user provided raw arguments
		if (is_null($arg)) {
			// If not, use the prepared arguments
			$arg = implode(" ", $this->parameters);
		}

		// Prepare the command
		$cmd    = escapeshellcmd($ffmpeg.' '.$input.' '.$arg.' '.$output);

		// Get OS version
		$os     = self::_serverOS();

		// Initiate a command compatible with each OS
		switch ($os) {
			case 'WIN':
				return $cmd;
				break;
			
			case 'DAR':
				# code...
				break;

			case 'LIN':
				# code...
				break;

			default:
				return false;
				break;
		}
	}

	/**
	 * Sets the codec used for the audio conversion
	 * https://trac.ffmpeg.org/wiki/AACEncodingGuide
	 * https://trac.ffmpeg.org/wiki/Encoding%20VBR%20(Variable%20Bit%20Rate)%20mp3%20audio
	 * @param   string $var ffmpeg codec name
	 * @return  boolean
	 */
	protected function SONUS_AUDIO_CODEC($var)
	{
		if (!is_null($var)) {
			array_push($this->parameters, '-c:a '.$var);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets the constant audio bitrate
	 * https://trac.ffmpeg.org/wiki/AACEncodingGuide
	 * https://trac.ffmpeg.org/wiki/Encoding%20VBR%20(Variable%20Bit%20Rate)%20mp3%20audio
	 * @param int $var bitrate
	 * @return boolean
	 */
	protected function SONUS_AUDIO_CONSTANT_BITRATE($var)
	{
		// Value must be numeric
		if (is_numeric($var)) {
			array_push($this->parameters, '-b:a '.$var.'k');
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Sets the audio channel as stereo or mono
	 * https://trac.ffmpeg.org/wiki/AudioChannelManipulation
	 * @param string $var
	 * @return boolean
	 */
	protected function SONUS_AUDIO_CHANNELS($var)
	{
		switch ($var) {
			case 'stereo':
				array_push($this->parameters, '-ac 2');
				return true;

			case 'mono':
				array_push($this->parameters, '-ac 1');
				return true;

			default:
				return false;
		}
	}

	/**
	 * Sets audio frequency rate
	 * http://ffmpeg.org/ffmpeg.html#Audio-Options
	 * @param int $var frequency
	 * @return boolean
	 */
	protected function SONUS_AUDIO_FREQUENCY($var)
	{
		// Value must be numeric
		if (is_numeric($var)) {
			array_push($this->parameters, '-ar:a '.$var);
			return true;
		} else {
			return false;
		}
	}

}