<?php 
require_once(dirname(__FILE__).'/boot.php');
//require_once(realpath(dirname(__FILE__).'/..').'/api.crawler.imageSearch/functions.php');

use PhpFanatic\clarifAI\ImageClient;

class class_nicerappImageDescriberEngine_plugin_clarifaiDotCom implements interface_nicerappImageDescriberEngine_plugin {
    private $db;
    private $rootPath;
    
    public function __construct() {
        $this->rootPath = dirname(__FILE__);
    }

    private function readConfig() {
        $this->db = [
            'config' => safeLoadJSONfile($this->rootPath.'/settings.imageDescriberEngine_plugin_clarifaiDotCom.json')
        ];
    }
    
    public function getRawAPIdata($testImageURL = false, $formatReturnValue=true) {
        $fncn = 'class_nicerappImageDescriberEngine_plugin_clarifaiDotCom->doDailyDownload()';
        if (!$testImageURL) trigger_error ($fncn.' : USER ERROR : $testImageURL missing from call.', E_USER_ERROR);
        
        $this->readConfig();
        $config = $this->db['config'];
        
        $client = new ImageClient($config['APIkey']);

        $client->AddImage($testImageURL);
        $clientResultsText = $client->Predict();
        //return $clientResultsText;
        $clientResults = json_decode ($clientResultsText, true);
        if (json_last_error()!==0)
            trigger_error ($fncn.' : RUNTIME ERROR : clarifai.com returned NON-JSON results ('.json_last_error_msg().') DATA='.$clientResultsText, E_USER_ERROR);
        if ($clientResults['status']['description']=='Failure')
            trigger_error ($fncn.' : RUNTIME ERROR : clarifai.com processing failed for inputURL='.$testImageURL.'. status.code='.$clientResults['status']['code'].', reason='.$clientResults['outputs'][0]['status']['description'].', details='.$clientResults['outputs'][0]['status']['details'], E_USER_ERROR);
            
        $filteredResults = json_encode($this->filterResults ($clientResults), JSON_PRETTY_PRINT);
        if ($formatReturnValue) 
            $result = [ 
                'testImageURL' => $testImageURL, 
                'rawResults' => json_encode($clientResults, JSON_PRETTY_PRINT),
                'filteredResults' => $filteredResults
            ]; 
        else 
            $result = [ 
                'testImageURL' => $testImageURL, 
                'rawResults' => $clientResultsText,
                'filteredResults' => $filteredResults
            ]; 
            
        return $result;
    }
    
    private function filterResults ($rawAPIdata = false) {
        $className = 'class_nicerappImageDescriberEngine_plugin_clarifaiDotCom';
        $fncn = 'class_nicerappImageDescriberEngine_plugin_clarifaiDotCom->filterResults()';
        
        if (!$rawAPIdata) trigger_error ($fncn.' : USER ERROR : $rawAPIdata is missing from the call to this function.', E_USER_ERROR);
        if (!is_array($rawAPIdata)) 
            trigger_error ($fncn.' : USER ERROR : $rawAPIdata is not an array, should be the output of '.$className.'->getRawAPIdata($testImageURL)', E_USER_ERROR);

        if (!is_array($rawAPIdata['outputs']))
            trigger_error ($fncn.' : RUNTIME ERROR : $rawAPIdata["outputs"] is not an array. The call to '.$className.'->getRawAPIdata() failed - the API at clarifai.com returned incomplete results. Can not use these results.', E_USER_ERROR);
        
        if (!is_array($rawAPIdata['outputs'][0]))
            trigger_error ($fncn.' : RUNTIME ERROR : $rawAPIdata["outputs"] is not an array. The call to '.$className.'->getRawAPIdata() failed - the API at clarifai.com returned incomplete results. Can not use these results.', E_USER_ERROR);
        
        if (!is_array($rawAPIdata['outputs'][0]['data']))
            trigger_error ($fncn.' : RUNTIME ERROR : $rawAPIdata["outputs"][0]["data"] is not an array. The call to '.$className.'->getRawAPIdata() failed - the API at clarifai.com returned incomplete results. Can not use these results.', E_USER_ERROR);
        
        if (!is_array($rawAPIdata['outputs'][0]['data']['concepts']))
            trigger_error ($fncn.' : RUNTIME ERROR : $rawAPIdata["outputs"][0]["data"]["concepts"] is not an array. The call to '.$className.'->getRawAPIdata() failed - the API at clarifai.com returned incomplete results. Can not use these results.', E_USER_ERROR);
        
        $results = [
            'concepts' => []
        ];
        foreach ($rawAPIdata['outputs'][0]['data']['concepts'] as $conceptIdx => $conceptRec) {
            $results['concepts'][] = $conceptRec['name'];
        }
        
        return $results;        
    }
    
    public function doDailyDownload() {
    }
    
}

?>
