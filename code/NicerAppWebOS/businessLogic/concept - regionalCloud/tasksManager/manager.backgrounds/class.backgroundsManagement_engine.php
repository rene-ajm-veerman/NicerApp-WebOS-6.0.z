<?php 
$rootPath = realpath(dirname(__FILE___).'/../../../..');
$myPath = '/NicerAppWebOS/apps/nicer.app/api.backgroundsManagement';
require_once ($rootPath.'/NicerAppWebOS/boot.php');
require_once ($rootPath.$myPath.'/class.backgroundsManagement_plugin_init_tasks.php');
require_once ($rootPath.$myPath.'/class.backgroundsManagement_plugin_task_getWidthsHeights.php');
require_once ($rootPath.$myPath.'/class.backgroundsManagement_plugin_task_getKeywords.php');

class class_NicerAppWebOS_backgroundsManagement_engine {
    public $cn = 'class NicerAppWebOS';
    public $about = array(
        'whatsThis' => 'NicerApp WCS backgrounds management engine PHP class',
        'version' => '1.0.0',
        'history' => array (
            '1.y.z' => 'Initial versions'
        ),
        'created' => '2022-06jun-01(Wednesday) 07:01 CEST',
        'lastModified' => '2022-06jun-01(Wednesday) ??:?? CEST',
        'copyright' => 'Copyright (c) 2022 by Rene A.J.M. Veerman <rene.veerman.netherlands@gmail.com>',
        'license' => 'https://nicer.app/LICENSE.txt'
    );
    
    public $initialized = false;
    public $tm = null;
    
    public function __construct () {
        $paths = [
            'roots' => [
                'naWCS' => realpath(dirname(__FILE___).'/../../../..'),
                'naApps' => '/NicerAppWebOS/apps/nicer.app',
                'tm' => '/api.businessLogic.tasksManager',
                'bgm' => '/api.businessLogic.backgroundsManagement'
            ]
        ];
        // convert all entries in $paths to full paths :
        $paths['roots']['naApps'] = $paths['roots']['naWCS'].$paths['roots']['naApps'];
        
        $paths['roots']['tm'] = $paths['roots']['naApps'].$paths['roots']['tm'];
        $paths['roots']['bgm'] = $paths['roots']['naApps'].$paths['roots']['bgm'];
        
        
        
        global $naWebOS; // NicerApp WCS main class
        $tm = new class_NicerAppWebOS_tasksManager_engine($paths);
        
        $tasks_plugins = [
            'init' => new class_NicerAppWebOS_backgroundsManagement__plugin_init_tasks($tm),
            'getWidthsHeights' => new class_NicerAppWebOS_backgroundsManagement__plugin_task_getWidthsHeights($tm),
            'getKeywords' => new class_NicerAppWebOS_backgroundsManagement__plugin_task_getKeywords($tm)
        ];
        $tm->setPlugins ($tasks_plugins);
        
        $this->initialized = true;
        
        $this->tm->start();
    }
}

?>
