#!/bin/bash
source ./do_backup_loadGlobals.sh

# !!! EXPERIMENTAL - DO NOT RUN THIS SCRIPT YET!

# $1 = path to backup from ('_' for the default domain folder)
# $2 = path to backup to ('_' for the default NicerApp WebOS backups folder, usually "/home/USERNAME/Documents/Backups naWebOS")

# Backups are split into multiple parts, and are all kept as backward compatible as possible.
# For instance, in .../NicerAppWebOS/3rd-party, you'll find a 3D folder with sub-folders 'libs' and 'models'.
#	These 2 sub-folders need to be treated differently (as many folders in the NA tree would need to be),
#	in order to arrive at the smallest possible github repository sizes, so that the rest of the files can be stored
#	elsewhere in cloudhosting facilities or in landline fiber internet spaces.

TT=$TARGET/3rd-party/
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/NicerAppWebOS/3rd-party/3D $TT

TT=$TARGET/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/decks $TT

TT=$TARGET/apps/NicerAppWebOS/applications/2D/musicPlayer
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/apps/NicerAppWebOS/applications/2D/musicPlayer/music $TT

TT=$TARGET/siteCache
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/siteCache/* $TT

TT=$TARGET/siteData
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/siteData/* $TT

TT=$TARGET/siteData.backups
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/siteData.backups/* $TT

TT=$TARGET/siteLogs
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
echo !!!mv -v $SOURCE/siteLogs/* $TT
zip -9 $TARGET/siteLogs.zip $TARGET/siteLogs

TT=$TARGET/screenshots
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/screenshots/* $TT

TT=$TARGET/siteMedia
mkdir -p $TT
echo --- created folder structure $TT and filling it with the following files :
mv -v $SOURCE/siteMedia/backgrounds $TT

cd $TARGET/scripts.maintenance
echo "What do you want the COMMIT MESSAGE to read on github.com? (You should have the github.com classic repo token ready right now in your copy-and-paste buffer)"
read COMMIT_MSG
./ga.sh "$COMMIT_MSG"

