#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/local/bin:/usr/local/sbin

source /var/www/nicer.app-5.10.z/domains/nicer.app/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh
NA_ROOT_PATH="$NA_MAIN_HTDOCS_RELATIVE_PATH/$NA_MAIN_SITE_FOLDER"
if [ ! -d $NA_ROOT_PATH ]; then
        NA_ROOT_PATH="/var/www/nicer.app-5.8.0-alpha001c"
fi

NA_MAILTO=rene.veerman.netherlands@gmail.com

dateHumanReadable=$(date +%Y-%m\(%B\)-%d\(%A\)\ %H:%M:%S\ Amsterdam.NL\ timezone)
dateJS=$(date +%Y-%m\(%B\)-%d\(%A\)\ %H:%M:%S\ Amsterdam.NL\ timezone)

dateYear=$(date +%Y)
dateMonth=$(date +%m)
dateDay=$(date +%d-%A)
dateHour=$(date +%H)
dateMinutesSeconds=$(date +%M-%S)
NA_LOGS_PATH=$NA_ROOT_PATH/NicerAppWebOS/siteLogs/$dateYear/$dateMonth/$dateDay/$dateHour
mkdir -p "$NA_LOGS_PATH"
pwd=$(pwd)
cd $NA_ROOT_PATH/NicerAppWebOS/siteLogs/
sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP *
sudo chmod -R $NA_MAIN_PERMISSIONS *
sudo find $NA_ROOT_PATH -type d -exec chmod u+x,g+x {} \;
cd $pwd
