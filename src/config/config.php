<?php

return array(

    /*
	|--------------------------------------------------------------------------
	| FFMPEG System Path
	|--------------------------------------------------------------------------
	|
	| We need to know the fully qualified system path to where ffmpeg
	| lives on this server. This is the same path you would time in
	| terminal to access FFMPEG.
	|
	*/
   
   #'ffmpeg'        => 'C:/ffmpeg/bin/ffmpeg.exe',
   'ffmpeg'		   => '/Applications/XAMPP/xamppfiles/htdocs/laravel/public/ffmpeg',

    /*
	|--------------------------------------------------------------------------
	| FFPROBE System Path
	|--------------------------------------------------------------------------
	|
	| We need to know the fully qualified system path to where ffprobe
	| lives on this server. This is the same path you would time in
	| terminal to access FFPROBE.
	|
	*/

   #'ffprobe'       => 'C:/ffmpeg/bin/ffprobe.exe',
   'ffprobe'        => '/Applications/XAMPP/xamppfiles/htdocs/laravel/public/ffprobe',

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

   'progress'      => true,

   /*
	|--------------------------------------------------------------------------
	| Temporary Directory
	|--------------------------------------------------------------------------
	|
	| In order to monitor the progress of running tasks Sonus will need to write
	| temporary files during the encoding progress. Please set a directory where
	| these can be written to, but make sure PHP is able to read and write to it.
	|
	*/

   'tmp_dir'      => '/Applications/XAMPP/xamppfiles/htdocs/laravel/public/'
);