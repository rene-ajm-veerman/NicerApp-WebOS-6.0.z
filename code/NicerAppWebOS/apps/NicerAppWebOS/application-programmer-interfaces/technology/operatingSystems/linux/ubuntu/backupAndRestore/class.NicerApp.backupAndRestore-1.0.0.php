 <?php 
 class class_NicerApp_backupAndRestore {
    public $meID = 'class_NicerApp_backupAndRestore';
    public $about = array(
        'whatsThis' => 'NicerApp Content Management System Backup-and-Restore PHP code class',
        'version' => '1.0.0',
        'history' => array (
            '1.0.z' => 'Initial versions'
        ),
        'created' => 'Saturday, 7 May 2022 02:00 CET',
        'lastModified' => 'Saturday, 7 May 2022 04:00 CET',
        'copyright' => 'Copyright (c) 2022 by Rene A.J.M. Veerman <rene.veerman.netherlands@gmail.com>',
        'license' => 'https://nicer.app/LICENSE.txt'
    );
  
  
    public function __construct ($factorySettings=null) {
    }
    
    public function throwError ($errorMessage, $errorLevel) {
        echo '<pre class="nicerapp_bar_error">$errorMessage='.$errorMessage.', $errorLevel='.$errorLevel.'</pre>';
        trigger_error ($errorMessage, $errorLevel);
    }
    
    public function createTaskDocument ($operationType, $settings) {
        $fncn = $this->meID.'::createTaskDocument($operationType, $settings)';
        global $naIP; // from .../NicerAppWebOS/boot.php
        global $userID_username; // from ./boot.php
        
        // check task validity
        $operationType = strtolower($operationType);
        if (
            $operationType !== 'backup'
            && $operationType !== 'restore'
        ) $this->throwError ($fncn.' : Invalid $operationType ("'.$operationType.'"). Valid values : "backup" and "restore", judged case-insensitively.', E_USER_ERROR);

        if (!is_array($settings)) $this->throwError ($fncn.' : Expecting an array for $settings.', E_USER_ERROR);
        
        
        // create $task and return it to the calling function.
        $id = [
            'sourceAddress' => $naIP,
            'User-Agent' => $_SERVER['HTTP_USER_AGENT'],
            'browser' => get_browser(),
            'UserID' => $userID_username
        ];
        $task = [
            'operationType' => $operationType,
            'id' => $id,
            'settingsForProcessingOfData' => $settings
        ];
        return $task;
    }
    
    
  
}
?>
