#!/bin/bash 
root="/var/www"

function today() {
	today="$1.`date +%F--%H%M%S`--$2"
}

function archive() {
	today $1 $2
	mv $1 $today
}

cp $root/$2/NicerAppWebOS/documentation/pageHeader.php /home/reneajmveerman/Documents
archive $root/$2 $3
cp -r $root/$1 $root/$2

cp $root/$1/domainConfig/$1/index.javascripts.json $root/$2/domainConfig/$2
cp $root/$1/domainConfig/$1/index.css $root/$2/domainConfig/$2
cp $root/$1/domainConfig/$1/index.dark.css $root/$2/domainConfig/$2
cp /home/reneajmveerman/Documents/pageHeader.php $root/$2/NicerAppWebOS/documentation

