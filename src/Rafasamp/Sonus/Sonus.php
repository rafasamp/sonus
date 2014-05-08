<?php namespace Rafasamp\Sonus;

use Config;
use Rafasamp\Sonus\Helpers;

/**
 * Laravel Audio Conversion Package
 *
 * This package is created to handle server-side conversion tasks using FFMPEG (http://www.fmpeg.org)
 *
 * @author     Rafael Sampaio <rafaelsampaio@live.com>
 */

class Sonus
{
    /**
     * Returns full path of ffmpeg
     * @return string
     */
    protected static function getConverterPath()
    {
        return Config::get('sonus::ffmpeg');
    }

    /**
     * Returns full path of ffprobe
     * @return string
     */
    protected static function getProbePath()
    {
        return Config::get('sonus::ffprobe');
    }

    /**
     * Returns full path for progress temp files
     * @return [type] [description]
     */
    protected static function getTempPath()
    {
        return Config::get('sonus::tmp_dir');
    }

    /**
     * Returns installed ffmpeg version
     * @return array
     */
    public static function getConverterVersion()
    {
        // Run terminal command to retrieve version
        $command = self::getConverterPath().' -version';
        $output  = shell_exec($command);

        // PREG pattern to retrive version information
        $ouput = preg_match("/ffmpeg version (?P<major>[0-9]{0,3}).(?P<minor>[0-9]{0,3}).(?P<revision>[0-9]{0,3})/", $output, $parsed);

        // Verify output
        if ($output === false || $output == 0)
        {
            return false;
        }

        // Assign array with variables
        $version = array(
            'major' => $parsed['major'],
            'minor' => $parsed['minor'],
            'rev'   => $parsed['revision']
            );

        return $version;
    }

    /**
     * Returns all formats ffmpeg supports
     * @return array
     */
    public static function getSupportedFormats()
    {
        // Run terminal command
        $command = self::getConverterPath().' -formats';
        $output  = shell_exec($command);

        // PREG pattern to retrive version information
        $output = preg_match_all("/(?P<mux>(D\s|\sE|DE))\s(?P<format>\S{3,11})\s/", $output, $parsed);

        // Verify output
        if ($output === false || $output == 0)
        {
            return false;
        }

        // Combine the format and mux information into an array
        $formats = array_combine($parsed['format'], $parsed['mux']);

        return $formats;
    }

    /**
     * Returns all audio formats ffmpeg can encode
     * @return array
     */
    public static function getSupportedAudioEncoders()
    {
        // Run terminal command
        $command = self::getConverterPath().' -encoders';
        $output  = shell_exec($command);

        // PREG pattern to retrive version information
        $output = preg_match_all("/[A]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\S{3,20})\s/", $output, $parsed);

        // Verify output
        if ($output === false || $output == 0)
        {
            return false;
        }

        return $parsed['format'];
    }

    /**
     * Returns all video formats ffmpeg can encode
     * @return array
     */
    public static function getSupportedVideoEncoders()
    {
        // Run terminal command
        $command = self::getConverterPath().' -encoders';
        $output  = shell_exec($command);

        // PREG pattern to retrive version information
        $output = preg_match_all("/[V]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\S{3,20})\s/", $output, $parsed);

        // Verify output
        if ($output === false || $output == 0)
        {
            return false;
        }

        return $parsed['format'];
    }

    /**
     * Returns all audio formats ffmpeg can decode
     * @return array
     */
    public static function getSupportedAudioDecoders()
    {
        // Run terminal command
        $command = self::getConverterPath().' -decoders';
        $output  = shell_exec($command);

        // PREG pattern to retrive version information
        $output = preg_match_all("/[A]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\w{3,20})\s/", $output, $parsed);

        // Verify output
        if ($output === false || $output == 0)
        {
            return false;
        }

        return $parsed['format'];
    }

    /**
     * Returns all video formats ffmpeg can decode
     * @return array
     */
    public static function getSupportedVideoDecoders()
    {
        // Run terminal command
        $command = self::getConverterPath().' -decoders';
        $output  = shell_exec($command);

        // PREG pattern to retrive version information
        $output = preg_match_all("/[V]([.]|\w)([.]|\w)([.]|\w)([.]|\w)([.]|\w)\s(?P<format>\w{3,20})\s/", $output, $parsed);

        // Verify output
        if ($output === false || $output == 0)
        {
            return false;
        }

        return $parsed['format'];
    }

    /**
     * Returns boolean if ffmpeg is able to encode to this format
     * @param  string $format ffmpeg format name
     * @return boolean
     */
    public static function canEncode($format)
    {
        $formats = array_merge(self::getSupportedAudioEncoders(), self::getSupportedVideoEncoders());

        // Return boolean if they can be encoded or not
        if(!in_array($format, $formats)) 
        {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns boolean if ffmpeg is able to decode to this format
     * @param  string $format ffmpeg format name
     * @return boolean
     */
    public static function canDecode($format)
    {
        // Get an array with all supported encoding formats
        $formats = array_merge(self::getSupportedAudioDecoders(), self::getSupportedVideoDecoders());

        // Return boolean if they can be encoded or not
        if(!in_array($format, $formats)) 
        {
            return false;
        } else  {
            return true;
        }
    }

    /**
     * Returns array with file information
     * @param  string $input file input
     * @param  string $type output format
     * @return array, json, xml, csv
     */
    public static function getMediaInfo($input, $type = null)
    {
        // Just making sure everything goes smooth
        if (substr($input, 0, 2) == '-i') 
        {
            $input = substr($input, 3);
        }

        switch ($type) 
        {
            case 'json':
                $command = self::getProbePath().' -v quiet -print_format json -show_format -show_streams -pretty -i '.$input.' 2>&1';
                $output  = shell_exec($command);
                $output  = json_decode($output, true);
                break;

            case 'xml':
                $command = self::getProbePath().' -v quiet -print_format xml -show_format -show_streams -pretty -i '.$input.' 2>&1';
                $output  = shell_exec($command);
                break;

            case 'csv':
                $command = self::getProbePath().' -v quiet -print_format csv -show_format -show_streams -pretty -i '.$input.' 2>&1';
                $output  = shell_exec($command);
                break;
            
            default:
                $command = self::getProbePath().' -v quiet -print_format json -show_format -show_streams -pretty -i '.$input.' 2>&1';
                $output  = shell_exec($command);
                $output  = json_decode($output, true);
                break;
        }

        return $output;
    }

    /**
     * Retrieves video thumbnails
     * @param  string  $input  video input
     * @param  string  $output output filename
     * @param  integer $count  number of thumbnails to generate
     * @param  string  $format thumbnail format
     * @return boolean
     */
    public static function getThumbnails($input, $output, $count = 5, $format = 'png')
    {
        // Round user input
        $count = round($count);

        // Return false if user requests 0 frames or round function fails
        if ($count < 1) 
        {
            return false;
        }

        // Execute thumbnail generator command
        $command = self::getConverterPath().' -i '.$input.' -vf "select=gt(scene\,0.5)" -frames:v '.$count.' -vsync vfr '.$output.'%02d.png';
        shell_exec($command);
        return true;
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
    public static function convert() 
    {
        $sonus = new Sonus;
        return $sonus;
    }

    /**
     * Sets the progress ID
     * @param  string $var progress id
     * @return null
     */
    public function progress($var)
    {
        // If value is null pass current timestamp
        if (is_null($var)) 
        {
            $this->progress = date('U');
            return $this;
        } else {
            $this->progress = $var;
            return $this;
        }
    }

    /**
     * Adds an input file
     * @param  string $var filename
     * @return boolean
     */
    public function input($var)
    {
        // Value must be text
        if (!is_string($var)) 
        {
            return false;
        }

        array_push($this->input, '-i '.$var);
        return $this;
    }

    /**
     * Adds an output file
     * @param  string $var filename
     * @return boolean
     */
    public function output($var)
    {
        // Value must be text
        if (!is_string($var)) 
        {
            return false;
        }

        array_push($this->output, $var);
        return $this;
    }

    /**
     * Overwrite output file if it exists
     * @param  boolean $var
     * @return boolean
     */
    public function overwrite($var = true)
    {
        switch ($var) 
        {
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
        // Value must be numeric
        if (!is_numeric($var)) 
        {
            return false;
        }

        array_push($this->parameters, '-timelimit '.$var);
        return $this;
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
        // Value must not be null
        if (is_null($var)) 
        {
            return false;
        }

        switch($type) 
        {
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

    /**
     * Sets the constant bitrate
     * @param int $var bitrate
     * @return boolean
     */
    public function bitrate($var, $type = 'audio')
    {
        // Value must be numeric
        if (!is_numeric($var)) 
        {
            return false;
        }

        switch ($type) 
        {
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

    /**
     * Sets the number of audio channels
     * https://trac.ffmpeg.org/wiki/AudioChannelManipulation
     * @param string $var
     * @return boolean
     */
    public function channels($var)
    {
        // Value must be numeric
        if (!is_numeric($var)) 
        {
            return false;
        }

        array_push($this->parameters, '-ac '.$var);
        return $this;
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
        if (!is_numeric($var)) 
        {
            return false;
        }

        array_push($this->parameters, '-ar:a '.$var);
        return $this;
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
        if (is_null($arg)) 
        {
            // If not, use the prepared arguments
            $arg = implode(' ', $this->parameters);
        }

        // Return input and output files
        $input  = implode(' ', $this->input);
        $output = implode(' ', $this->output);

        // Prepare the command
        $cmd = escapeshellcmd($ffmpeg.' '.$input.' '.$arg.' '.$output);

        // Check if progress reporting is enabled
        if (Config::get('sonus::progress') === true) 
        {
            // Get temp dir
            $tmpdir = self::getTempPath();

            // Get progress id
            if (empty($this->progress)) 
            {
                // Create a default (unix timestamp)
                $progress = date('U');
            } else {
                // Assign if it exists
                $progress = $this->progress;
            }

            // Publish progress to this ID
            $cmd = $cmd.' 1>"'.$tmpdir.$progress.'.sonustmp" 2>&1';

            // Execute command
            return shell_exec($cmd);
        } else {
            // Execute command
            return shell_exec($cmd);
        }
    }

    /**
     * Returns given job progress
     * @param  string $job id
     * @param  string $format format to output data
     * @return array
     */
    public static function getProgress($job, $format = null)
    {
        // Get the temporary directory
        $tmpdir = self::getTempPath();

        // The code below has been adapted from Jimbo
        // http://stackoverflow.com/questions/11441517/ffmpeg-progress-bar-encoding-percentage-in-php
        $content = @file_get_contents($tmpdir.$job.'.sonustmp');

        if($content)
        {
            // Get duration of source
            preg_match("/Duration: (.*?), start:/", $content, $matches);

            $rawDuration = $matches[1];

            // rawDuration is in 00:00:00.00 format. This converts it to seconds.
            $ar = array_reverse(explode(":", $rawDuration));
            $duration = floatval($ar[0]);
            if (!empty($ar[1])) $duration += intval($ar[1]) * 60;
            if (!empty($ar[2])) $duration += intval($ar[2]) * 60 * 60;

            // Get the time in the file that is already encoded
            preg_match_all("/time=(.*?) bitrate/", $content, $matches);

            $rawTime = array_pop($matches);

            // This is needed if there is more than one match
            if (is_array($rawTime))
            {
                $rawTime = array_pop($rawTime);
            }

            // rawTime is in 00:00:00.00 format. This converts it to seconds.
            $ar = array_reverse(explode(":", $rawTime));
            $time = floatval($ar[0]);
            if (!empty($ar[1])) $time += intval($ar[1]) * 60;
            if (!empty($ar[2])) $time += intval($ar[2]) * 60 * 60;

            // Calculate the progress
            $progress = round(($time/$duration) * 100);

            // Output to array
            $output = array(
                'Duration' => $rawDuration,
                'Current'  => $rawTime,
                'Progress' => $progress
                );

            // Return data
            switch ($format) 
            {
                case 'array':
                    return $output;
                    break;
                
                default:
                    return json_encode($output);
                    break;
            }
        } else {
            return null;
        }
    }

    /**
     * Deletes job temporary file
     * @param  string $job id
     * @return boolean
     */
    public static function destroyProgress($job) 
    {
        // Get temporary file path
        $file = $tmpdir.$job.'.sonustmp';

        // Check if file exists
        if (is_file($file)) 
        {
            // Delete file
            $output = unlink($tmpdir.$job.'.sonustmp');
            return $output;
        } else {
            return false;
        }
    }

    /**
     * Deletes all temporary files
     * @return boolean
     */
    public static function destroyAllProgress()
    {
        // Get all filenames within the temporary folder
        $files = glob($tmpdir.'*');

        // Iterate through files
        $output = array();
        foreach ($files as $file) 
        {
            if (is_file($file)) 
            {
                // Return result to array
                $result = unlink($file);
                array_push($output, var_export($result, true));
            }
        }

        // If a file could not be deleted, return false
        if (array_search('false', $output)) 
        {
            return false;
        }

        return true;
    }
}