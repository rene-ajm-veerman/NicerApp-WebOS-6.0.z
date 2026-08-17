<?php
$myPath_BLfsdb = realpath(dirname(__FILE__).'/../../../../..');
require_once ($myPath_BLfsdb.'/NicerAppWebOS/boot.php');

class class_NicerAppWebOS_database_API_fileSystemDB_version1 {
    public $cn = 'class_NicerAppWebOS_database_API_fileSystemDB';
    public $version = '1.0.0';

    // i'm already thinking of the proper routing in the upward level's code :
    //  how to get data for a specific 'table' into a specifict connectorType's database architecure call-distribution code.
    // .. .. .

    // now i'm thinking about how to get the initial data stored properly in this file, so that those function calls even have data to store (on the file-system).
    public function __construct ($naWebOS, $username = 'Guest', $cRec = null) {
        global $dbConfigFile_couchdb;

        if (is_null($naWebOS)) $this->throwError('__construct($naWebOS) : invalid $naWebOS', E_USER_ERROR);
        $this->cms = $naWebOS;

        $this->connectionSettings = $cRec;
    }

    public function addLogEntries($entries) {
        $fncn = $this->cn.'->addLogEntries($entries)';

        global $filePerms_ownerUser;
        global $filePerms_ownerGroup;
        global $filePerms_perms_publicWriteableExecutable;
        global $filePerms_perms_readonly;
        global $filePerms_perms_readWrite;

        $txtRelPath = date('Y/m/d/H/i-s').'-nicerapp__fileSystemDB_version1.log.txt';
        $htmlRelPath = date('Y/m/d/H/i-s').'-nicerapp__fileSystemDB_version1.log.html';

        $txtPath = $this->connectionSettings['rsyncRootPath'].'/'.$txtRelPath;
        $htmlPath = $this->connectionSettings['rsyncRootPath'].'/'.$htmlRelPath;
        createDirectoryStructure (
            dirname($txtPath),
            $filePerms_ownerUser,
            $filePerms_ownerGroup,
            $filePerms_perms_publicWriteableExecutable
        );

        if (is_array($entries)) {
            foreach ($entries as $idx => $rec) {
                foreach ($rec as $k => $rec2) {
                    $txtMsg = $rec2['txt'];
                    $htmlMsg = $rec2['html'];
                };

                if (is_string($txtMsg) && $txtMsg!=='') {
                    $fopenParam = file_exists($txtPath) ? 'a' : 'w';
                    $f = fopen ($txtPath, $fopenParam);
                    fwrite ($f, $txtMsg.PHP_EOL);
                    fclose ($f);
                    //chown ($txtPath, $filePerms_ownerUser);
                    //chmod ($txtPath, $filePerms_perms_readWrite);
                }

                if (is_string($htmlMsg) && $htmlMsg!=='') {
                    $fopenParam = file_exists($htmlPath) ? 'a' : 'w';
                    $f = fopen ($htmlPath, $fopenParam);
                    fwrite ($f, $htmlMsg.PHP_EOL);
                    fclose ($f);
                    //chown ($htmlPath, $filePerms_ownerUser);
                    //chmod ($htmlPath, $filePerms_perms_readWrite);
                }
            }
        }
    }

    public function testDBconnection() {
    }

}

?>
