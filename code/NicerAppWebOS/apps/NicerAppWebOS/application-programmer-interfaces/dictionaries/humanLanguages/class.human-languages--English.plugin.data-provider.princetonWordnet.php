<?php

class class_nicerappHumanLanguages_english_plugin_princetonWordnet implements interface_nicerappHumanLanguages_plugin {
    private $db;
    public $count = 0;

    public function readRawData() {
        $fncn = 'class_nicerappHumanLanguages_english_plugin_princetonWordnet->readRawData()';
        $debug = false;
        
        $filepath = dirname(__FILE__).'/raw-data.human-languages--English.Princeton-wordnet.json';
        if (!file_exists($filepath)) trigger_error ($fncn.' : ERROR : file "'.$filepath.'" is missing.', E_USER_ERROR);
        
        $this->db = json_decode(file_get_contents($filepath), true);
        if (json_last_error()!==0) trigger_error ($fncn.' : JSON DECODING ERROR WHILE DECODING FILE "'.$filepath.'" : '.json_last_error_msg(), E_USER_ERROR);
    }
    
    public function retrieveWordData ($word=null) {
        $fncn = 'class_nicerappHumanLanguages_english_plugin_princetonWordnet->retrieveWordData($word)';
        $debug = false;
        
        if (is_null($word)) trigger_error ($fncn.' : ERROR $word must be a string.', E_USER_ERROR);
        
        $ret = [];
        $ret = $this->retrieveWordAnatomy ($ret, $word);
        $this->count = 0;


        
        /*
        foreach ($this->db['synset'] as $synID => $synRec) {
            foreach ($synRec['word'] as $dbWordID => $dbWord) {
                if (strtolower($dbWord) === strtolower($word)) {
                
                    switch ($synRec['pos']) {
                        case 'a' : $ret['type'] = 'adjective'; break;
                        case 'n' : $ret['type'] = 'noun'; break;
                        case 'r' : $ret['type'] = 'adverb'; break;
                        case 's' : $ret['type'] = 'satellite'; break;
                        case 'v' : $ret['type'] = 'verb'; break;
                    }
                
                    if (!array_key_exists('words',$ret)) $ret['words']=[];
                    $ret['words'] = array_merge ($ret['words'], $synRec['word']);
                    
                    if (!array_key_exists('descriptions',$ret)) $ret['descriptions']=[];
                    if (!array_key_exists('usageExamples',$ret)) $ret['usageExamples']=[];
                    
                    $gl = explode (';', $synRec['gloss']);
                    $gl2 = [];
                    foreach ($gl as $glIdx => $glEntry) {
                        $glEntry2 = trim($glEntry);
                        if (substr($glEntry2,0,1)=='"') {
                            $ret['usageExamples'][] = str_replace('"','',$glEntry2);
                        } else {
                            $ret['descriptions'][] = $glEntry2;
                        }                        
                    }
                }
            }
        }*/
        
        //$ret['words'] = array_values(array_unique($ret['words']));
        //if (is_array($ret['words'])) $ret['words'] = array_values($ret['words']);
        
        return $ret;
    }

    public function retrieveWordAnatomy (&$ret=null, $word=null) {
        $this->count++;
        //if ($this->count > 15) return $ret;
        foreach ($this->db['synset'] as $synID => $synRec) {
            foreach ($synRec['word'] as $dbWordID => $dbWord) {
                if (strtolower($dbWord) === strtolower($word)) {
                    //echo 'retrieveWordAnatomy() : $word = '.$word.PHP_EOL;
                    //$this->retrieveUpstreamWord ($ret, $synRec['pointer']);
                    $word2 = $this->retrieveUpstreamWord ($ret, $synRec['pointer']);
                    if (in_array($word2, $ret))
                        return $ret;
                    else
                        return array_merge ($ret, $word2);
                }
            }
        }
        return $ret;
    }

    public function retrieveUpstreamWord (&$ret=null, $pointer=null) {
        //$this->count++;
        //if ($this->count > 15) return $ret;
//return [json_encode($pointer)];
        foreach ($pointer as $idx=>$prec) {
            if ($prec['symbol']=='@') {
                $word = $this->db['synset'][$prec['synset']]['word'][0];
                //echo 'retrieveUpstreamWord() : $word = '.$word.PHP_EOL;
                if (in_array($word, $ret))
                    return $ret;
                $ret = array_merge ($ret, [$word]);
                $this->retrieveWordAnatomy ($ret, $word);
                //return array_merge ($ret, $this->retrieveWordAnatomy ($ret, $word));
            }
        }
        return $ret;
    }

    
    public function getRawAPIdata() {
        try {
            $this->readRawData();
        } catch (Throwable $e) {
        } catch (Exception $e) {
        }

    }
    public function doDailyDownload() {
    }
    
}

?>
