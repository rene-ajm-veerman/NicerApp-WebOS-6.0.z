<?php 
$rootPath = realpath(dirname(__FILE___).'/../../../..');
$myPath = '/NicerAppWebOS/apps/nicer.app/api.backgroundsManagement';
require_once ($rootPath.$myPath.'/class.backgroundsManagement_plugin_init_tasks.php');
require_once ($rootPath.$myPath.'/class.backgroundsManagement_plugin_task_getWidthsHeights.php');
require_once ($rootPath.$myPath.'/class.backgroundsManagement_plugin_task_getKeywords.php');

class class_NicerAppWebOS_backgroundsManagement__init_tasks {
    public $cn = 'class NicerAppWebOS';
    public $about = array(
        'whatsThis' => 'NicerApp WCS backgrounds management engine --init-tasks-- PHP class',
        'version' => '1.0.0',
        'history' => array (
            '1.y.z' => 'Initial versions',
        ),
        'created' => '2022-06jun-01(Wednesday) 08:32 CEST',
        'lastModified' => '2022-06jun-01(Wednesday) ??:?? CEST',
        'copyright' => 'Copyright (c) 2022 by Rene A.J.M. Veerman <rene.veerman.netherlands@gmail.com>'
    );
    
    public $initialized = false;
    
    public function __construct () {
        $this->initialized = true;
    }
    
    public function start() {
        
    }
}

?>
