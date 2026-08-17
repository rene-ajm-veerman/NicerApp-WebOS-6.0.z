#!/bin/bash
set -x # echo on
source ./do_upgrade_globals_manufacturer.sh
TZone=$(date +'%z')
TZone=`echo $TZone | tr + plus`
TZone=`echo $TZone | tr - minus`

OStz=`timedatectl status | grep "Time zone"`
naTZtmp=${OStz#*: }
naTZtmp2=${naTZtmp% *}
naTZtmp3=${naTZtmp2% *}
naTZ=`echo $naTZtmp3 | tr / -`
DATE=$(date +'%Y-A.D./%m-%B--%d-%A/%H-%M-%S')-$TZone-$(date +'%Z')-$naTZ
SOURCE=/var/www/nicer.app
#TARGETBASE=/home/reneav/Documents/NicerApp-WebOS-raw-backups/sourceLocation-$naTZ
TARGETBASE=/home/reneav/Documents/NicerApp-WebOS-raw-backups
TARGET=$TARGETBASE/$DATE
mkdir -p $TARGET
echo created folder structure "$TARGET"


