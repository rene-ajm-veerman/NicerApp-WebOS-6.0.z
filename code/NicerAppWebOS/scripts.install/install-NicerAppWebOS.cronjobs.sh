#!/bin/bash

myRoot=/var/www/nicer.app
if [ -f $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh ]; then
    echo "source $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh"
    source $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh
else
    echo "FATAL ERROR : NOT FOUND : '$myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh'"
    exit
fi

# Add the possibility to place the NA_ROOT_PATH under /home/SOME_USER
NA_ROOT_PATH="/home/$NA_MAIN_USER/$NA_MAIN_HTDOCS_RELATIVE_PATH/$NA_MAIN_SITE_FOLDER"
if [ ! -d $NA_ROOT_PATH ]; then
	NA_ROOT_PATH="/var/www/nicer.app"
fi
echo "cd $NA_ROOT_PATH"
cd $NA_ROOT_PATH

echo "Removing nicerapp scripts in /etc/cron.d"
for f in $(ls /etc/cron.d/nicerapp__*)
do
    echo rm $f
    rm $f
done

echo "Adding nicerapp scripts to /etc/cron.d"
# Add the necessary NicerAppWebOS-related daily/hourly automated scripts to the linux operating system
for f in $(ls $NA_ROOT_PATH/NicerAppWebOS/scripts.install/install.cron.d/nicerapp__*)
do
    #echo "NOW INSTALLING CRONJOB $f"
    bf="$(basename -- $f)"
    echo cp $f /etc/cron.d/$bf
    sudo cp $f /etc/cron.d/$bf
done
# requires https://linuxconfig.org/how-to-setup-the-rsync-daemon-on-linux :
echo cp $NA_ROOT_PATH/NicerAppWebOS/scripts.maintenance/crontab_server_root /var/spool/cron/crontab/root
sudo mkdir -p /var/spool/cron/crontab
sudo cp $NA_ROOT_PATH/scripts.maintenance/crontab_server_root /var/spool/cron/crontab/root
