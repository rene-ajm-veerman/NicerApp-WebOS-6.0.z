#!/bin/sh
chown -R rene:www-data /home/rene/data1/htdocs/said.by
chmod -R 750 /home/rene/data1/htdocs/said.by

cp -rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/apps/nicer.app/naThemeViewer/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/naThemeViewer/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/naThemeViewer/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/naThemeViewer/*

cp -rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/apps/nicer.app/cms/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cms/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cms/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cms/*

cp -rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/apps/nicer.app/cmsText/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cmsText/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cmsText/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cmsText/*

cp -rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/apps/nicer.app/cmsViewMedia/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cmsViewMedia/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cmsViewMedia/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/cmsViewMedia/*

cp -rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/apps/nicer.app/diskText/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/diskText/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/diskText/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/diskText/*

cp -rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/apps/nicer.app/analytics/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/analytics/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/analytics/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/apps/nicer.app/analytics/*

cp -Rf /home/rene/data1/htdocs/nicer.app/domainConfig/* /home/rene/data1/htdocs/said.by/domainConfig/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/domainConfig/*
chmod -R 750 /home/rene/data1/htdocs/said.by/domainConfig/*
rm /home/rene/data1/htdocs/said.by/domainConfig/said.by/index.combined.*
touch /home/rene/data1/htdocs/said.by/domainConfig/said.by/index.combined.js
touch /home/rene/data1/htdocs/said.by/domainConfig/said.by/index.combined.cssTheme.dark.css
touch /home/rene/data1/htdocs/said.by/domainConfig/said.by/index.combined.cssTheme.light.css
touch /home/rene/data1/htdocs/said.by/domainConfig/said.by/index.combined.css
chown rene:www-data /home/rene/data1/htdocs/said.by/domainConfig/said.by/index.combined.*
chmod 770 /home/rene/data1/htdocs/said.by/domainConfig/said.by/index.combined.*

cp -Rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/logic.userInterface/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/logic.userInterface/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/logic.userInterface/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/logic.userInterface/*

cp -Rf /home/rene/data1/htdocs/nicer.app/siteMedia/*.png /home/rene/data1/htdocs/said.by/siteMedia
chown -R rene:www-data /home/rene/data1/htdocs/said.by/siteMedia/*.png
chmod -R 750 /home/rene/data1/htdocs/said.by/siteMedia/*.png

cp /home/rene/data1/htdocs/nicer.app/siteMedia/*.jpg /home/rene/data1/htdocs/said.by/siteMedia
chown rene:www-data /home/rene/data1/htdocs/said.by/siteMedia/*.jpg
chmod 750 /home/rene/data1/htdocs/said.by/siteMedia/*.jpg

#cp /home/rene/data1/htdocs/nicer.app/siteMedia/*.gif /home/rene/data1/htdocs/said.by/siteMedia
#chown rene:www-data /home/rene/data1/htdocs/said.by/siteMedia/*.gif
#chmod 750 /home/rene/data1/htdocs/said.by/siteMedia/*.gif

cp /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/jsEngineeringMath/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/jsEngineeringMath/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/jsEngineeringMath/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/jsEngineeringMath/*

cp -Rf /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/selfHealer/* /home/rene/data1/htdocs/said.by/NicerAppWebOS/selfHealer/
chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/selfHealer/*
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/selfHealer/*

cp /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/*.php /home/rene/data1/htdocs/said.by/nicerapp
chown rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/*.php
chmod 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/*.php

cp /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/*.js /home/rene/data1/htdocs/said.by/nicerapp
chown rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/*.js
chmod 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/*.js

#cp /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/*.css /home/rene/data1/htdocs/said.by/nicerapp
#chown rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/*.css
#chmod 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/*.css

cp /home/rene/data1/htdocs/nicer.app/*.php /home/rene/data1/htdocs/said.by
chown rene:www-data /home/rene/data1/htdocs/said.by/*.php
chmod 750 /home/rene/data1/htdocs/said.by/*.php

cd /home/rene/data1/htdocs/said.by/NicerAppWebOS/3rd-party/sag
git pull

cp -R /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/3rd-party/plupload*.* /home/rene/data1/htdocs/said.by/NicerAppWebOS/3rd-party
cp -R /home/rene/data1/htdocs/nicer.app/NicerAppWebOS/3rd-party/vendor /home/rene/data1/htdocs/said.by/NicerAppWebOS/3rd-party

chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/3rd-party
chmod -R 750 /home/rene/data1/htdocs/said.by/NicerAppWebOS/3rd-party

chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/siteData
chmod -R 770 /home/rene/data1/htdocs/said.by/NicerAppWebOS/siteData

chown -R rene:www-data /home/rene/data1/htdocs/said.by/NicerAppWebOS/siteCache
chmod -R 770 /home/rene/data1/htdocs/said.by/NicerAppWebOS/siteCache

cd /home/rene/data1/htdocs/nicer.app
