#!/bin/bash
chown -R www-data:www-data /tine20

source /etc/apache2/envvars
tail -F /tine20/log/* &
exec apache2 -D FOREGROUND