<?php 

interface wallpaperScraper_plugin {

    // small selections of data are stored as JSON files on the filesystem of the server,
    // large selections of data (like crawl history and search result JSON data) are stored in couchdb.apache.org, accessed via the https://github.com/NicerAppWebOS/sag PHP library
    
    public function readDB();
    
    public function saveDB();
    
    public function doDailyDownload();
    
    public function addFileToFailedDownloadsList($it, $save_file_loc, $thumb_file_loc, $descriptionFilepath);
    
    public function retryFailedDownloads();
}

?>
