#!/bin/bash

ROOT_PATH=/var/www/nicer.app-5.10.z/code
DOMAIN_ROOTS=( "/var/www/nicer.app-5.10.z/domains/nicer.app" "/var/www/nicer.app-5.10.z/domains/said.by" )
echo "START OF $ROOT_PATH/NicerAppWebOS/scripts.maintenance/setPermissions.sh"

echo "Nicerapp versions present on this machine :"
for rp in /var/www/nicer.app*/; do echo $rp; done

source $ROOT_PATH/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh

if [ -f $ROOT_PATH/NicerAppWebOS/scripts.maintenance/do_upgrade_globalsClient_*.sh ]; then
	for f in $ROOT_PATH/NicerAppWebOS/scripts.maintenance/do_upgrade_globalsClient_*.sh;
	do 
		echo "BEGIN INCLUDING $f"; source $f; echo "DONE INCLUDING $f"
	done
fi

echo "cd $ROOT_PATH"
cd $ROOT_PATH

echo "SETTING PERMISSIONS BASELINE (this may take a short while)"
sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP *
sudo chown $NA_MAIN_USER:$NA_MAIN_GROUP /var
sudo chmod 750 /var
sudo chown $NA_MAIN_USER:$NA_MAIN_GROUP /var/www
sudo chmod 750 /var/www
sudo chmod -R 750 /var/www/
sudo chmod -R $NA_MAIN_PERMISSIONS /var/www

chmod -R 644 .git .gitignore .htaccess .htaccess.gzipping 

sudo find $ROOT_PATH -type d -exec chmod u+x,g+x {} \;

sudo find $ROOT_PATH/NicerAppWebOS/scripts.install -type f -iname "*.sh" -exec chmod $NA_SHELLSCRIPTS_PERMISSIONS {} \;
sudo find $ROOT_PATH/NicerAppWebOS/scripts.maintenance -type f -iname "*.sh" -exec chmod $NA_SHELLSCRIPTS_PERMISSIONS {} \;

echo "SETTING PERMISSIONS FOR /var/log/apache2/NicerEnterprises-NicerApp-WebOS.log"
sudo touch /var/log/apache2/NicerEnterprises-NicerApp-WebOS.log
sudo chown www-data:www-data /var/log/apache2/NicerEnterprises-NicerApp-WebOS.log
sudo chmod 640 /var/log/apache2/NicerEnterprises-NicerApp-WebOS.log

#echo "SETTING PERMISSIONS FOR $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads"
#if [ ! -d $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads ]; then
#	sudo mkdir $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads
#fi
#if [ ! -d $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads/GeoLite2-City ]; then
#	sudo mkdir $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads/GeoLite2-City
#fi
#if [ ! -d $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads/GeoLite2-Country ]; then
#	sudo mkdir $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads/GeoLite2-Country
#fi
#sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads
#sudo chmod -R $NA_USERDATA_PERMISSIONS $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads
#sudo find $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/downloads -type d -exec chmod u+x,g+x {} \;
#sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/*.mmdb
#sudo chmod -R $NA_USERDATA_PERMISSIONS $ROOT_PATH/NicerAppWebOS/3rd-party/geoLite2/*.mmdb

for CURRENT_PATH in "${DOMAIN_ROOTS[@]}"; do
	echo "SETTING PERMISSIONS FOR $CURRENT_PATH"

	mkdir $CURRENT_PATH/siteCache
	sudo chown $NA_MAIN_USER:$NA_MAIN_GROUP $CURRENT_PATH/siteCache
	sudo chmod 770 $CURRENT_PATH/siteCache
	mkdir $CURRENT_PATH/siteData
	sudo chown $NA_MAIN_USER:$NA_MAIN_GROUP $CURRENT_PATH/siteData
	sudo chmod 770 $CURRENT_PATH/siteData

	chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $CURRENT_PATH/siteCache 
	sudo chmod -R $NA_USERDATA_PERMISSIONS $CURRENT_PATH/siteCache
	sudo find $CURRENT_PATH/siteCache -type d -exec chmod u+x,g+x {} \;

	chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $CURRENT_PATH/siteData
	sudo chmod -R $NA_USERDATA_PERMISSIONS $CURRENT_PATH/siteData
	sudo find $CURRENT_PATH/siteData -type d -exec chmod u+x,g+x {} \;
done

echo "END OF ./setPermissions.sh"
