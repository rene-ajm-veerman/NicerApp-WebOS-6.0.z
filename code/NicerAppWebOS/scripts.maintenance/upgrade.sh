#!/bin/bash

# if you're new to the bash programming language, 
# just look up things like :
#   bash variables
#   bash if statements
#   bash for loop
# on google or whatever searchengine you prefer :)


# prevent alarms in .../NicerAppWebOS/selfHealer/index.php going off
echo "sudo killall php"
sudo killall php

myRoot=/var/www/nicer.app
if [ -f $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh ]; then
    echo "source $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh"
    source $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh
else
    echo "source $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh"
fi


ROOT_PATH="/home/$NA_MAIN_USER/$NA_MAIN_HTDOCS_RELATIVE_PATH/$NA_MAIN_SITE_FOLDER"
if [ ! -d $ROOT_PATH ]; then
	ROOT_PATH="/var/www/nicer.app"
fi
echo "cd $ROOT_PATH"
cd $ROOT_PATH




if [ -f $ROOT_PATH/do_upgrade_globalsClient_*.sh ]; then
    for f in $(ls $ROOT_PATH/do_upgrade_globalsClient_*.sh)
    do
        echo "BEGIN INCLUDING $f"
        source $f
        echo "DONE INCLUDING $f"
    done
else
    echo "not created yet : $ROOT_PATH/do_upgrade_globalsClient_*.sh"
fi


##sudo git pull https://github.com/NicerAppWebOS/nicerapp main
echo "-- NOW FETCHING LATEST SOURCES AS PACKAGE CONTAINING ONLY LATEST CHANGES"
sudo git fetch --all
echo "-- NOW APPLYING LATEST CHANGES TO LOCAL FILES of https://github.com/NicerAppWebOS/nicerapp"
sudo git reset --hard origin/main

echo "sudo rm $ROOT_PATH/lastModified.*"
sudo rm $ROOT_PATH/lastModified.*
echo "sudo date +%Y-%m\(%B\)-%d\(%A\)\ %H:%M:%S\ Amsterdam.NL\ timezone > $ROOT_PATH/lastModified.humanReadable.txt"
sudo date +%Y-%m\(%B\)-%d\(%A\)\ %H:%M:%S\ Amsterdam.NL\ timezone > $ROOT_PATH/lastModified.humanReadable.txt
echo "sudo date +%Y-%m\(%B\)-%d\(%A\)\ %H:%M:%S\ Amsterdam.NL\ timezone > $ROOT_PATH/lastModified.JS.txt"
sudo date +%Y-%m\(%B\)-%d\(%A\)\ %H:%M:%S\ Amsterdam.NL\ timezone > $ROOT_PATH/lastModified.JS.txt

sudo $ROOT_PATH/scripts.maintenance/setPermissions.sh

if [ -f $ROOT_PATH/scripts.maintenance/do_upgradeSite__*.sh ]; then
    for f in $(ls $ROOT_PATH/scripts.maintenance/do_upgradeSite__*.sh)
    do
        echo "NOW UPGRADING VIA $f"
        sudo $f
        echo "DONE UPGRADING VIA $f"
    done
else
    echo '$ROOT_PATH/scripts.maintenance/do_upgradeSite__*.sh files found.'
fi
# (?!^ABC$)


# start the apps (selfHealer only for now)
#for f in "$ROOT_PATH/scripts.maintenance/${NA_SITE_APPS[@]}"
#do
#    echo "NOW STARTING $f"
#    sudo $f
#done

for f in $(ls $ROOT_PATH/NicerAppWebOS/scripts.install/install.cron.d/nicerapp__*)
do
    #echo "NOW INSTALLING CRONJOB $f"
    bf="$(basename -- $f)"
    echo cp $f /etc/cron.d/$bf
    sudo cp $f /etc/cron.d/$bf
done
# requires https://linuxconfig.org/how-to-setup-the-rsync-daemon-on-linux :
echo cp $ROOT_PATH/NicerAppWebOS/scripts.maintenance/crontab_server_root /var/spool/cron/crontab/root
sudo mkdir -p /var/spool/cron/crontab
sudo cp $ROOT_PATH/scripts.maintenance/crontab_server_root /var/spool/cron/crontab/root


source $ROOT_PATH/NicerAppWebOS/scripts.maintenance/LAN_passwords.sh

echo "NOW RE-INITIALIZING .../NicerAppWebOS/siteCache/*.json"
sudo rm -v $ROOT_PATH/NicerAppWebOS/siteCache/*.json
echo "NOW RE-INITIALIZING .../NicerAppWebOS/siteCache/backgrounds_recursive.json"
curl https://$DOMAIN_TLD/domainConfig/$DOMAIN_TLD/ajax_backgrounds_recursive.php > /dev/null
echo "NOW RE-INITIALIZING .../NicerAppWebOS/siteCache/backgrounds.json"
curl https://$DOMAIN_TLD/domainConfig/$DOMAIN_TLD/ajax_backgrounds.php > /dev/null


echo "NOW RE-INTIALIZING .../NicerAppWebOS/apps/$DOMAIN_TLD/applications/2D/news/crontabEntry_manageDatabase.php"
php .../NicerAppWebOS/apps/$DOMAIN_TLD/applications/2D/news/crontabEntry_manageDatabase.php

echo "NOW RUNNING : php $ROOT_PATH/htaccess.build.php"
sudo php $ROOT_PATH/htaccess.build.php
