#!/bin/bash
# bap.sh = backup and publish.sh
DATE=$(date +'%Y-%m-%d-%a_%H-%M-%S_%Z')
set -x # echo on
source ./do_upgrade_globals_manufacturer.sh

cd "/var/www/nicer.app-5.10.z/domains/nicer.app/downloads/latest"
zip -x /var/www/nicer.app-5.10.z/nicer.app-musicPlayer-music/\* -y -r -1 NicerApp-WebOS-v5.10.z-latest.zip /var/www/*
chown gavan:www-data Nicer*.zip
chmod 750 Nicer*.zip
