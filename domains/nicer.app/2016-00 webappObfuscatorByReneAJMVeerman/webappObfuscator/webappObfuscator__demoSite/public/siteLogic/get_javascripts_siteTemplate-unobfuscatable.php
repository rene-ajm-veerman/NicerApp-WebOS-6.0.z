<?php 
require_once (dirname(__FILE__).'/../../globals.php');
global $woDevURL;
global $dfoPUBLIChd;
global $dfoPUBLICurl; 

//require_once ($dfoSecretHD.'siteLogic/functions.php');

echo file_get_contents ('http://code.jquery.com/jquery-2.1.4.js');
echo file_get_contents ('http://code.jquery.com/ui/1.11.4/jquery-ui.js');
echo file_get_contents ($dfoPUBLICurl.'lib/jQuery.jPlayer-2.9.1/dist/jplayer/jquery.jplayer.js');
echo file_get_contents ($dfoPUBLICurl.'lib/jquery.history/jquery.history.js');
?>