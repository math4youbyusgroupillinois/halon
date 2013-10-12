## Halon

[![Build Status](https://travis-ci.org/NUBIC/halon.png)](https://travis-ci.org/NUBIC/halon)

Halon is a tool to print out Medicine Administration Records for NMH Clinical IT

### Installation

1. Add new site in IIS and point it to halon's public folder (no mapped drives unfortunately)
2. Grant the IIS user (usualy Users) read & execute and write permissions to the halon folder
3. Grant the IIS user (usualy Users) modify permissions to the app/database folder
4. Grant the IIS user (usually Users) read permissions to to the directory where the postscript files are located
5. Run `composer install -d workbench/northwestern/printer`
6. Run `composer install`
7. Import the rewrite rules
  1. Install the URL Rewrite moudle for IIS using the Web Platform Installer
  2. Import the public/.htacess rule (http://stackoverflow.com/questions/15018538/laravel-htaccess-rewrite-rule-convertion-to-iis)
8. Install the PHP window printer extension
  1. The printer extension you download depends on php version (ex. 5.3), thread safety (ts/nts), and architecture (x86)
  2. Download the printer extension from http://windows.php.net/downloads/pecl/snaps/printer/0.1.0-dev/
  3. Extract php_printer.dll into `<PHP_ROOT>/ext`
  4. Add a line `extension=php_printer.dll` in php.ini
  5. Set `fastcgi.impersonate = 0` in php.ini and restart Microsoft IIS
9. Set `'mar_path' => '<MAR DIRECTORY>'` in app/config/app.php

### Releases

Release names follow http://en.wikipedia.org/wiki/List_of_national_parks_of_the_United_States.
