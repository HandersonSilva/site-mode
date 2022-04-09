#!/bin/bash
chown -R www-data:www-data /var/www/html  && chmod -R 775 /var/www/html

exec "$@"