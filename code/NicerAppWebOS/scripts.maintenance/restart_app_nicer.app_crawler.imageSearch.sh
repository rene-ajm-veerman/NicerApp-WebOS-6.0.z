#!/bin/sh 
if [ ! -f "../RAM_disk/exists_true" ]; then
	if [ ! -d "../RAM_disk" ]; then
		sudo mkdir ../RAM_disk
		sudo mount -t tmpfs -o rw,size=10M tmpfs ../RAM_disk
	fi
	touch ../RAM_disk/exists_true
else
rm -rf ../RAM_disk/*
fi

sudo chown -R rene:www-data ../RAM_disk
sudo chmod -R 770 ../RAM_disk

#./setPermissions.sh
#sudo nice -n -19 php nicerapp/selfHealer/run.php &

#sudo rm nicerapp/apps/nicer.app/crawler.imageSearch/crontabEntry_manageDatabase.lock.txt
nice -n 19 php nicerapp/apps/nicer.app/crawler.imageSearch/crontabEntry_doDailyDownload.php & 
