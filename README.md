sonus [![Build Status](https://travis-ci.org/rafasamp/sonus.png?branch=master)](https://travis-ci.org/rafasamp/sonus)
=====

A laravel implementation of the ffmpeg converting engine

Installation
-----
Addition information will be included when the class is in an useful state. Autoloading is [PSR-0](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md) compatible.

Feature Guide
-----

### Namespace Import
Sonus is namespaced, however I suggest you make your life easier and import
the class into your code:

	<?php
	use Rafasamp\Sonus\Sonus as Sonus;

### Examples
Here is a simple example of a file being converted from FLAC to AAC:

	Sonus::convert()->input('foo.flac')->bitrate(128)->output('bar.aac')->go();

Sonus can also convert video files:

	Sonus::convert()->input('foo.avi')->bitrate(300, 'video')->output('bar.flv')->go();

Although Sonus contains several preset parameters, you can also pass your own

	Sonus::convert()->input('foo.flac')->output('bar.mp3')->go('-b:a 64k -ac 1');