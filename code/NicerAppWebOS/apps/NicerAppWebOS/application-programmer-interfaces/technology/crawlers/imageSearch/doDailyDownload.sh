php /var/www/nicer.app/NicerAppWebOS/apps/nicer.app/api.crawler.imageSearch/crontabEntry_doDailyDownload.php
#rm /var/www/nicer.app/NicerAppWebOS/siteCache/*
#php /var/www/nicer.app/domainConfig/nicer.app/ajax_backgrounds.php
#php /var/www/nicer.app/domainConfig/nicer.app/ajax_backgrounds_recursive.php
rm /var/www/nicer.app/NicerAppWebOS/siteCache/*
php /var/www/nicer.app/domainConfig/nicer.app/ajax_backgrounds.php > /dev/null
php /var/www/nicer.app/domainConfig/nicer.app/ajax_backgrounds_recursive.php > /dev/null
