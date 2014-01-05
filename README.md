# Sonus (Laravel 4 Package)
[![Build Status](https://travis-ci.org/rafasamp/sonus.png?branch=master)](https://travis-ci.org/rafasamp/sonus)
[![ProjectStatus](http://stillmaintained.com/rafasamp/sonus.png)](http://stillmaintained.com/rafasamp/sonus)
=====

Sonus is an attempt to integrate FFMPEG with **Laravel 4** and was born out of my frustration when trying to perform simple conversion tasks from within my project.

Although Sonus supports video conversion, the project focus is to fully implement audio conversion before enhancing video compatibility.

**Warning:**

This package is being actively developed and may break at any time. Integration on a production environment is discouraged at this point.

## Features

## Quick Start

### Setup

Update your `composer.json` file and add the following under the `require` key:

	"rafasamp/sonus": "dev"

Run the composer update command:

	$ composer update

In your `config/app.php` add `'rafasamp\Sonus\SonusServiceProvider'` to the end of the `$providers` array:

    'providers' => array(

        'Illuminate\Foundation\Providers\ArtisanServiceProvider',
        'Illuminate\Auth\AuthServiceProvider',
        ...
        'rafasamp\Sonus\SonusServiceProvider',

    ),

Still under `config/app.php` add `'Sonus' => 'Rafasamp\Sonus\Facade'` to the `$aliases` array:

    'aliases' => array(

        'App'        => 'Illuminate\Support\Facades\App',
        'Artisan'    => 'Illuminate\Support\Facades\Artisan',
        ...
        'Sonus'      => 'rafasamp\Sonus\Facade',

    ),

Run the `artisan` command below to publish the configuration file:

	$ php artisan config:publish rafasamp/Sonus

And update the `FFMPEG` key to point at the __full path__ to FFMPEG:

	'ffmpeg'        => '/Applications/FFMPEG/ffmpeg'

**Attention!**

Make sure your webserver has full read/write rights to the FFMPEG executable.

### Examples

Here is a simple example of a file being converted from FLAC to AAC:

	Sonus::convert()->input('foo.flac')->bitrate(128)->output('bar.aac')->go();

Sonus can also convert video files:

	Sonus::convert()->input('foo.avi')->bitrate(300, 'video')->output('bar.flv')->go();

Although Sonus contains several preset parameters, you can also pass your own

	Sonus::convert()->input('foo.flac')->output('bar.mp3')->go('-b:a 64k -ac 1');

## License

Sonus is free software distributed under the terms of the MIT license.

## Aditional information

Any questions, feel free to contact me.

Any issues, please [report here](https://github.com/rafasamp/sonus/issues)