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

cp $root/said.by/NicerAppWebOS/documentation/pageHeader.php /home/reneajmveerman/Documents
archive $root/said.by $root/said.by $3
cp -r $root/nicer.app $root/said.by
cp $root/nicer.app/domainConfig/nicer.app/index.javascripts.json $root/said.by/domainConfig/said.by
cp $root/nicer.app/domainConfig/nicer.app/index.css $root/said.by/domainConfig/said.by
cp $root/nicer.app/domainConfig/nicer.app/index.dark.css $root/said.by/domainConfig/said.by
cp /home/reneajmveerman/Documents/pageHeader.php $root/said.by/NicerAppWebOS/documentation

