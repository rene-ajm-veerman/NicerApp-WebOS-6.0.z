#!/bin/bash

myRoot=/var/www/nicer.app-5.10.z/nicer.app-5.10.0/

echo "START OF $myRoot/NicerAppWebOS/scripts.maintenance/setPermissions.sh"

echo "INCLUDING $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh"
source $myRoot/NicerAppWebOS/scripts.maintenance/do_upgrade_globals_manufacturer.sh

ROOT_PATH="/home/$NA_MAIN_USER/$NA_MAIN_HTDOCS_RELATIVE_PATH/$NA_MAIN_SITE_FOLDER"
if [ ! -d $ROOT_PATH ]; then
	ROOT_PATH=$myRoot
fi

for f in $(ls $ROOT_PATH/NicerAppWebOS/scripts.maintenance/do_upgrade_globalsClient_*.sh)
do
    echo "BEGIN INCLUDING $f"
    source $f
    echo "DONE INCLUDING $f"
done

ROOT_PATH="/home/$NA_MAIN_USER/$NA_MAIN_HTDOCS_RELATIVE_PATH/$NA_MAIN_SITE_FOLDER"
if [ ! -d $ROOT_PATH ]; then
	ROOT_PATH=$myRoot
fi
echo "cd $ROOT_PATH"
cd $ROOT_PATH

echo "SETTING PERMISSIONS BASELINE (this may take a short while)"
sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP /var/www/nicer.app-5.10.z
sudo chmod 755 /var/www/nicer.app-5.10.z
sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP *
sudo chmod -R $NA_MAIN_PERMISSIONS *

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

CURRENT_PATH="$ROOT_PATH/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/crawlers/imagesMetaInfo/output"
echo "SETTING PERMISSIONS FOR $CURRENT_PATH"
if [ ! -d $CURRENT_PATH ]; then
	sudo mkdir -p $CURRENT_PATH
fi



CURRENT_PATH="$ROOT_PATH/NicerAppWebOS/siteCache"
echo "SETTING PERMISSIONS FOR $CURRENT_PATH"
if [ ! -d $CURRENT_PATH ]; then
	sudo mkdir $CURRENT_PATH
fi
if [ ! -d $ROOT_PATH/NicerAppWebOS/siteCache/hashValues ]; then
	sudo mkdir $ROOT_PATH/NicerAppWebOS/siteCache/hashValues
fi
sudo echo "<Files \"key-*.txt\">Require all denied</Files>" > $ROOT_PATH/NicerAppWebOS/siteCache/hashValues/.htaccess
if [ ! -d $ROOT_PATH/NicerAppWebOS/siteCache/tokens ]; then
	sudo mkdir $ROOT_PATH/NicerAppWebOS/siteCache/tokens
fi
sudo echo "<Files \"*.txt\">Require all denied</Files>" > $ROOT_PATH/NicerAppWebOS/siteCache/tokens/.htaccess
chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $ROOT_PATH/NicerAppWebOS/siteCache
sudo chmod -R $NA_USERDATA_PERMISSIONS $ROOT_PATH/NicerAppWebOS/siteCache
sudo find $ROOT_PATH/NicerAppWebOS/siteCache -type d -exec chmod u+x,g+x {} \;
sudo rm $ROOT_PATH/NicerAppWebOS/siteCache/getPageCSS*



echo "SETTING PERMISSIONS FOR $ROOT_PATH/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite"
chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $ROOT_PATH/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/*.json
sudo chmod -R $NA_USERDATA_PERMISSIONS $ROOT_PATH/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/*.json
#sudo find $ROOT_PATH/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent -type d -exec chmod g+x {} \;



echo "SETTING PERMISSIONS FOR $ROOT_PATH/NicerAppWebOS/siteLogs"
if [ ! -d $ROOT_PATH/NicerAppWebOS/siteLogs ]; then
	sudo mkdir $ROOT_PATH/NicerAppWebOS/siteLogs
fi
sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $ROOT_PATH/NicerAppWebOS/siteLogs
sudo chmod -R $NA_USERDATA_PERMISSIONS $ROOT_PATH/NicerAppWebOS/siteLogs
sudo find $ROOT_PATH/NicerAppWebOS/siteLogs -type d -exec chmod u+x,g+x {} \;

echo "SETTING PERMISSIONS FOR $ROOT_PATH/NicerAppWebOS/siteData"
if [ ! -d $ROOT_PATH/NicerAppWebOS/siteData ]; then
	sudo mkdir $ROOT_PATH/NicerAppWebOS/siteData
fi
sudo chmod -R $NA_USERDATA_PERMISSIONS $ROOT_PATH/NicerAppWebOS/siteData
sudo find $ROOT_PATH/NicerAppWebOS/siteData -type d -exec chmod u+x,g+x {} \;

echo "SETTING PERMISSIONS FOR $ROOT_PATH/NicerAppWebOS/apps/nicer.app/application-programmer-interfaces/technology/crawlers/imageSearch"
if [ ! -d $ROOT_PATH/NicerAppWebOS/apps/nicer.app/application-programmer-interfaces/technology/crawlers/imageSearch/output ]; then
	sudo mkdir -p $ROOT_PATH/NicerAppWebOS/apps/nicer.app/application-programmer-interfaces/technology/crawlers/imageSearch/output
fi
sudo chown -R $NA_MAIN_USER:$NA_MAIN_GROUP $ROOT_PATH/NicerAppWebOS/apps/nicer.app/application-programmer-interfaces/technology/crawlers/imageSearch/output
sudo chmod -R $NA_USERDATA_PERMISSIONS $ROOT_PATH/NicerAppWebOS/apps/nicer.app/application-programmer-interfaces/technology/crawlers/imageSearch/output
sudo find $ROOT_PATH/NicerAppWebOS/apps/nicer.app/application-programmer-interfaces/technology/crawlers/imageSearch/output -type d -exec chmod u+x,g+x {} \;

sudo chmod u+x,g+x $ROOT_PATH/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/technology/crawlers/imageSearch/doDailyDownload.sh
sudo chmod u+x,g+x $ROOT_PATH/NicerAppWebOS/3rd-party/imapsync-2.178/imapsync

echo "END OF ./setPermissions.sh"
