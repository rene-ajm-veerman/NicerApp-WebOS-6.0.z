<?php
require_once(dirname(__FILE__).'/boot.php');
//require_once(realpath(dirname(__FILE__).'/..').'/crawler.imageSearch/functions.php');

class class_nicerappHumanLanguages_english_plugin_bighugelabsDotCom implements interface_nicerappHumanLanguages_plugin {
    private $rootPath;
    private $db;
    
    public function __construct() {
        $this->rootPath = dirname(__FILE__);
    }
    
    public function readRawData() {
    }
    
    public function retrieveWordData() {
    }
    
    
    
    private function readConfig() {
        $this->db = [
            'config' => safeLoadJSONfile($this->rootPath.'/settings.naturalLanguageEngine_plugin_bighugelabsDotCom.json')
        ];            
    }

    public function getRawAPIdata($formatReturnValue=true) {
        $fncn = 'class_nicerappHumanLanguages_english_plugin_bighugelabsDotCom->doDailyDownload()';
        
        $this->readConfig();
        $config = $this->db['config'];
        
        $word = 'home';
        $url = 'https://words.bighugelabs.com/api/2/'.$config['APIkey'].'/'.$word.'/json';
        
        // fetch results via curl (curl is the data transport mechanism)
        $curlCh = curl_init();
        curl_setopt($curlCh, CURLOPT_URL, $url);
        curl_setopt($curlCh, CURLOPT_RETURNTRANSFER, 1);
        $jsonText = curl_exec($curlCh);
        curl_close($curlCh);         
        
        // data integrity check
        $curlResults = json_decode ($jsonText, true);
        if (json_last_error()!==JSON_ERROR_NONE) {
            //trigger_error ($fncn.' : Could not decode JSON result from URL='.$url.', ERROR='.json_last_error_msg(), E_USER_ERROR);
            $logstr = $fncn.' : WARNING : Could not decode JSON result from URL='.$url.', ERROR='.json_last_error_msg().', JSON='.$jsonText.PHP_EOL;
            echo $logstr; error_log ($logstr);
            return false;
        } else {
            //$crawlHistory[] = $url;
            if ($formatReturnValue) $result = json_encode($curlResults, JSON_PRETTY_PRINT); else $result = $jsonText;
            return $result;
        }

    }
    
    public function doDailyDownload() {
        
    }
}
?>
