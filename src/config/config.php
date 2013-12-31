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
   
   'ffmpeg'        => '/Applications/XAMPP/xamppfiles/htdocs/laravel/public/ffmpeg',

   /*
	|--------------------------------------------------------------------------
	| Supported Operating Systems
	|--------------------------------------------------------------------------
	|
	| As FFMPEG requires extensive shell_exec I am including a list of systems
	| these functions are tested with. You may add your own system but
	| it is not guaranteed that it will work.
	|
	| The application compares the value of strtoupper(substr(php_uname(), 0, 3))
	| agains the array below.
	|
	*/
   'supported_os'  => array('WIN', 'LIN', 'DAR')

);