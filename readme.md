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
7. Run `php artisan migrate`
8. Set `'mar_path' => '<MAR DIRECTORY>'` in app/config/app.php
9. Set `'print_server_name' => '<Network Printer Server Path(with slash included)>'` in app/config/app.php


### Reseting Passwords

There is a script that can be used to reset a user's password. The script can be run using the following command.

`php reset_password.php`

### Watching Locations JSON

A location information generated from CERNER can be watched for changes when it is placed in the `app/config/locations.json`.

### Running Tests

Use the command `vendor/bin/phpunit` to run the unit tests.

### Deployment

Deployment instructions can be found in [deploy.md](deploy.md).

### Releases

Release names follow http://en.wikipedia.org/wiki/List_of_national_parks_of_the_United_States.
