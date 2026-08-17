#!/bin/bash 
root="/var/www"
dest="/media/reneajmveerman/74c63245-6599-4bda-99d6-5b30ca0717e4/home/rene/data18"

function today() {
	today="$1.`date +%F--%H%M%S`--$2"
}

function todayDest() {
	todayDest="`date +%F--%H%M%S`-BACKUPS OF ALL"
}

function archive() {
	today $2 $3
	echo "mv $1 $today"
	mv $1 $2
}




todayDest
mkdir -p "$dest/$todayDest/www"
cp -r $root/nicer.app "$dest/$todayDest/www"
cp -r $root/said.by "$dest/$todayDest/www"
mkdir -p "$dest/$todayDest/Pictures"
cp -r /home/reneajmveerman/Pictures/* "$dest/$todayDest/Pictures"

chown -R reneajmveerman:reneajmveerman "$dest/$todayDest"
chmod -R 775 $dest/$todayDest

