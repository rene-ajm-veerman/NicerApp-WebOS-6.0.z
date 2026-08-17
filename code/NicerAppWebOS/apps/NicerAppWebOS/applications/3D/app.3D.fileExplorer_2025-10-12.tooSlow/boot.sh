#!/bin/bash
killall php
cd /var/www/nicer.app/NicerAppWebOS/apps/nicer.app/app.3D.fileExplorer
php app.dialog.siteContent.php > boot_output.html

