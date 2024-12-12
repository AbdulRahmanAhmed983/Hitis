#!/bin/sh
#cd /home/u280714611/domains/ahi-egypt.com/blog && php artisan database:backup >> /dev/null 2>&1
#* * * * * php /home/u280714611/domains/ahi-egypt.com/blog/artisan database:backup 1>> /dev/null 2>&1
# /usr/local/bin/php /home/u280714611/domains/ahi-egypt.com/blog/artisan schedule:run >> /dev/null 2>&1
php /home/u280714611/domains/ahi-egypt.com/blog/artisan schedule:run >> /dev/null 2>&1