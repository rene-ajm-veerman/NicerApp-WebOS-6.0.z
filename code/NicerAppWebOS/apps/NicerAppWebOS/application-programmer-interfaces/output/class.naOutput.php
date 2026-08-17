<?php
require_once (realpath(dirname(__FILE__)).'/boot.php');

class naOutput_html implements interface_class_naOutput {

    private $cn = 'naOutput_html';

    public function out ($jsonData, $phpHtmlTemplate) {
        $fncn = 'out ($jsonData, $phpHtmlTemplate)';
        $debug = false;
        $replacements = [];
        $this->fillReplacements ($jsonData);
      
        $search = array_keys($replacements);
        $replace = array_values($replacements);
        $html = execPHP($phpHtmlTemplate);
        $html = str_replace ($search, $replace, $html);
        if ($debug) {
            echo '<pre>'; 
            echo '$search='; var_dump ($search); echo PHP_EOL.PHP_EOL;
            echo '$replace=';var_dump ($replace); echo PHP_EOL.PHP_EOL;
            echo '</pre>';
        }
        return $html;    
    }

    private function fillReplacements ($jsonData, &$replacements) {
        foreach ($jsonData as $k => $v) {
        if (!is_array($v)) {
            if (array_key_exists ($k, $replacements)) trigger_error ($this->cn.'::'.$fncn.' : WARNING : $replacements["'.$k.'"]='.$replacements[$k].', overwriting with '.$v, E_USER_WARNING);
            $replacements[$k] = $v;
        } else {
            $replacements[$k] = [];
            $this->fillReplacements ($v, &$replacements[$k]);
        }
    }
    
}

class naOutput_cli implements interface_class_naOutput {

}
?>
