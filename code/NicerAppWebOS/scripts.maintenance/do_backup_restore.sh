#!/bin/bash

# !!! EXPERIMENTAL - DO NOT RUN THIS SCRIPT YET!

source ./do_backup_loadGlobals.sh

# $1 = path to backup from ('_' for the default domain folder)
# $2 = path to backup to ('_' for the default NicerApp WebOS backups folder, usually "/home/USERNAME/Documents/Backups naWebOS")

# Backups are split into multiple parts, and are all kept as backward compatible as possible.
# For instance, in .../NicerAppWebOS/3rd-party, you'll find a 3D folder with sub-folders 'libs' and 'models'.
#	These 2 sub-folders need to be treated differently (as many folders in the NA tree would need to be),
#	in order to arrive at the smallest possible github repository sizes, so that the rest of the files can be stored
#	elsewhere in cloudhosting facilities or in landline fiber internet spaces.

mv -v $TARGET/3rd-party/3D $SOURCE/3rd-party

mv -v $TT/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/decks $SOURCE/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite

mv -v $TARGET/apps/NicerAppWebOS/applications/2D/musicPlayer/music $SOURCE/apps/NicerAppWebOS/applications/2D/musicPlayer/music

mkdir -p $SOURCE/NicerAppWebOS/siteCache
mv -v $TARGET/siteCache/* $SOURCE/NicerAppWebOS/siteCache

mkdir -p $SOURCE/NicerAppWebOS/siteData
mv -v $TARGET/siteData/* $SOURCE/NicerAppWebOS/siteData

mkdir -p $SOURCE/siteData.backups
mv -v $TARGET/siteData.backups/* $SOURCE/siteData.backups

mkdir -p $SOURCE/siteLogs
unzip $TARGET/siteLogs.zip $SOURCE/siteLogs

mkdir -p $SOURCE/screenshots
mv -v $TARGET/screenshots/* $SOURCE/screenshots

mkdir -p $SOURCE/siteMedia
mv -v $TARGET/siteMedia/backgrounds $SOURCE/siteMedia
