<?php 
require_once(dirname(__FILE__).'/boot.php');

interface interface_nicerappImageDescriberEngine_plugin {
    public function getRawAPIdata();
    public function doDailyDownload();
}
?>
