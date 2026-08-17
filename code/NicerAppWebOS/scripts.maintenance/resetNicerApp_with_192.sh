#!/bin/bash 
root="/var/www"
dest="/media/reneajmveerman/74c63245-6599-4bda-99d6-5b30ca0717e4/home/rene/data18"
source="192.168.178.29"
target="nicer.app"

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

archive $root/$target $root/$target $3
cp -r $root/$source $root/$target
cp $root/$source/domainConfig/$source/index.javascripts.json $root/$target/domainConfig/$target
cp $root/$source/domainConfig/$source/index.css $root/$target/domainConfig/$target
cp $root/$source/domainConfig/$source/index.dark.css $root/$target/domainConfig/$target
cp /home/reneajmveerman/Documents/pageHeader.php $root/$target/NicerAppWebOS/documentation

