<?php 
$rootPath = realpath(dirname(__FILE___).'/../../../..');
$myPath = '/NicerAppWebOS/apps/nicer.app/api.backgroundsManagement';
require_once ($rootPath.$myPath.'/class.backgroundsManagement_engine.php');

$naBackgroundsManagement_engine = new class_NicerAppWebOS_backgroundsManagement_engine();
global $naBackgroundsManagement_engine;


// written < 2022-05-27 :

// still on my 1st smokes of the morning, and figuring out how to do many tasks reliably and with progress bar, without having to do N rewrites of the code..

// tasks like order N backgrounds input folders per photo resolution, and fetching keywords from clarifai.com (for as many as i'm allowed to by clarifai per day).

// these won't complete at the same speed of course

// current thinking : have a cronjob_*.php in api.backgroundsManagement do the work

// and a app.dialog.siteContent.php for progress reports that fetches summary progress reports via the filesystem from the cronjob_*.php

// keywords will be counted, and the ones with the most background file hits will be floated into the menu

// portrait and landscape photos will be seperated from 'tiled' hits, then analyzed for actual width and height properties and shoved into .../NicerAppWebOS/siteData/backgrounds/portrait or .../NicerAppWebOS/siteData/backgrounds/landscape

// i currently see no reason to specify more sub-folders under those 2 folders in siteData

// all meta data will be kept as large JSON files on the filesystem, in api.backgroundsManagement/tasks/YOURDOMAIN_TLD/*
// and from there fed into couchdb on regular intervals, per photo, and per keyword worthy of listing in the menu
// RAM is cheap, couchdb queries are NOT.

// ETA until first version of code can be tested : about 3 to 5 days.
?>
