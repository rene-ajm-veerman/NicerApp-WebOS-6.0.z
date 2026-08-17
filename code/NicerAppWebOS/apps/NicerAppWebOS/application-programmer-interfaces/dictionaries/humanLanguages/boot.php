<?php 
require_once(realpath(dirname(__FILE__).'/../../../../..').'/boot.php'); // nicerapp boot.php


require_once(dirname(__FILE__).'/interface.human-languages.plugin.data-provider.php');
require_once(dirname(__FILE__).'/class.human-languages--English.plugin.data-provider.princetonWordnet.php');
require_once(dirname(__FILE__).'/class.human-languages--English.plugin.data-provider.bighugelabsDotCom.php');

global $hl_english_plugin__princeton;
$hl_english_plugin__princeton = new class_nicerappHumanLanguages_english_plugin_princetonWordnet();
$hl_english_plugin__princeton->readRawData();

global $hl_english_plugin__bigHugeLabs;
$hl_english_plugin__bigHugeLabs = new class_nicerappHumanLanguages_english_plugin_bighugelabsDotCom();

?>
