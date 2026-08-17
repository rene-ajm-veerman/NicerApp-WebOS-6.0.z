<?php 
$rootPath = realpath(dirname(__FILE__).'/../../../../../..');
$myPath = '/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/tasks/manager';

class class_NicerAppWebOS_tasksManager_engine {
    public $about = array(
        'whatsThis' => 'NicerApp WCS backgrounds management PHP class',
        'version' => '1.0.0',
        'history' => array (
            '1.y.z' => 'Initial versions',
        ),
        'created' => '2022-06jun-01(Wednesday) 07:01 CEST',
        'lastModified' => '2022-06jun-01(Wednesday) 07:01 CEST',
        'copyright' => 'Copyright (c) 2022 by Rene A.J.M. Veerman <rene.veerman.netherlands@gmail.com>'
    );
    
    public $cn = 'class_NicerAppWebOS_tasksManager_engine($paths, $cdbUser, $cdbAdmin)';
    public $initialized = false;
    public $plugins;
    public $paths;
    public $cdbUser;
    public $cdbAdmin;
    
    public function __construct ($paths, $cdbUser=null, $cdbAdmin=null) {
        global $naWebOS;
        $this->paths = $paths;
        $this->paths['my'] = [
            'path' => 
                realpath(dirname(__FILE___).'/../../../..')
                .'/NicerAppWebOS/apps/NicerAppWebOS/tasks/manager'
        ];

        if (!is_null($cdbUser))
            $this->cdbUser = $cdbUser;
        else
            $this->cdbUser = $naWebOS->dbs->findConnection('couchdb')->cdb;

        if (!is_null($cdbAdmin))
            $this->cdbAdmin = $cdbAdmin;
        else
            $this->cdbAdmin = $naWebOS->dbsAdmin->findConnection('couchdb')->cdb;
    }
    
    public function setPlugins ($plugins) {
        $this->plugins = $plugins;
        $this->initialized = true;
    }
    
    public function cleanlinessChecks($fncn) {
        if (!array_key_exists('init',$this->plugins)) {
            $dbg = [
                'where' => $fncn,
                'errorDescription' => 'no "init" plugin found'
            ];
            $this->reportError ($dbg);
            return false;
        }
        return true;
    }
    
    public function start() {
        $fncn = $this->cn.'->start()';
        if ($this->cleanlinessChecks($fncn)) {
            $tInit = $this->plugins['init'];
            $tInit->start();
        }            
    }
    
    //*** helper functions
    public function reportError ($dbg) {
        trigger_error (
            $dbg['where'].' : '.$dbg['errorDescription'],
            E_USER_ERROR
        );
    }
    
    public function reportStatus ($dbg) {
        $msg = $dbg['where'].' : '.$dbg['errorDescription'];
        if (array_key_exists('REQUEST_URI', $_SERVER)) {
            $html = 
                PHP_EOL.PHP_EOL
                .'<span class="naTaskManager_statusMsg">'.$msg
                .'</span><br/>'
                .PHP_EOL.PHP_EOL;
            echo $html;
        } else {
            echo $msg.PHP_EOL;
        }
    }
}

?>
