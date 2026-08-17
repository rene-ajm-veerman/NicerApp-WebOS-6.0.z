<?php 
$rootPath_cts_na5yz = realpath(dirname(__FILE__).'/../../../../../..'); global $rootPath_cts_na5yz;

class class_api_codeLanguageInterpreter {
    public $config;
    
    public function __construct() {
        global $rootPath_cts_na5yz;
        //$fn = $rootPath_cts_na5yz.'/NicerAppWebOS/apps/nicer.app/application-programmer-interfaces/technology/codeTranslationSystems/class.api.codeTranslationSystem.for.NicerAppWebOS-5.y.z.php.json';
        //$this->config = safeJSONload ($fn);
    }

    public function interpret ($languageType, $sourceAsTxt) {
        switch ($languageType) {
            case 'php' :
            case 'PHP' :
                return interpret_PHP ($sourceAsTxt);
            case 'js' :
            case 'javascript' :
            case 'Javascript' :
                return interpret_Javascript ($sourceAsTxt);
        }
    }
}


?>
