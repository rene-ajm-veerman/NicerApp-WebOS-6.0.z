<?php

interface interface_nicerappHumanLanguages_plugin {
    public function readRawData();
    public function retrieveWordData();
    
    public function getRawAPIdata();
    public function doDailyDownload();
}

?>
