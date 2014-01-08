<?php

return array(

    /*
	|--------------------------------------------------------------------------
	| ffmpeg System Path
	|--------------------------------------------------------------------------
	|
	| We need to know the fully qualified system path to where ffmpeg
	| lives on this server. If you paste this path into your shell or
	| command prompt you should get output from ffmpeg.
	|
	| Examples:
	| Windows: 'C:/ffmpeg/bin/ffmpeg.exe'
	| Mac OSX: '/Applications/MAMP/ffmpeg/ffmpeg'
	| Linux:   '/usr/bin/ffmpeg'
	|
	*/
   
   'ffmpeg'		   => '',

    /*
	|--------------------------------------------------------------------------
	| ffprobe System Path
	|--------------------------------------------------------------------------
	|
	| We need to know the fully qualified system path to where ffprobe
	| lives on this server. If you paste this path into your shell or
	| command prompt you should get output from ffprobe.
	|
	*/

   'ffprobe'        => '',

   /*
	|--------------------------------------------------------------------------
	| Progress monitoring
	|--------------------------------------------------------------------------
	|
	| FFMPEG supports outputing progress to HTTP. Problem is, PHP can't really
	| handle chunked POST requests. Therefore the solution is to output progress
	| to a text file and track the job by reading it live.
	|
	| If you would like to let your users know of the progress on active running
	| conversions set this flag to true.
	|
	*/

   'progress'      => false,

   /*
	|--------------------------------------------------------------------------
	| Temporary Directory
	|--------------------------------------------------------------------------
	|
	| In order to monitor the progress of running tasks Sonus will need to write
	| temporary files during the encoding progress. Please set a directory where
	| these can be written to, but make sure PHP is able to read and write to it.
	|
	| Make sure that your path has a trailing slash!
	|
	| Examples:
	| Windows: 'C:/ffmpeg/tmp/'
	| Mac OSX: '/Applications/MAMP/ffmpeg/tmp/'
	| Linux:   '/var/www/tmp/'
	|
	*/

   'tmp_dir'      => ''
);