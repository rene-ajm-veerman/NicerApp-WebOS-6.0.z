<?php
$myPath_xjkdf = realpath(dirname(__FILE__).'/../../../../..');
require_once ($myPath_xjkdf.'/NicerAppWebOS/3rd-party/adodb5/adodb.inc.php');

// provide access to the following adodb5 drivers :
    // 1 : ADO connection 'msqli' = databases MySQL, MariaDB, Percona
    // Docs : https://adodb.org/dokuwiki/doku.php?id=v5:database:mysql
    /*--- example connection code :
    $db = newAdoConnection('mysqli')
    $db->ssl_key    = "key.pem";
    $db->ssl_cert   = "cert.pem";
    $db->ssl_ca     = "cacert.pem";
    $db->ssl_capath = null;
    $db->ssl_cipher = null;
    $db->connect($host, $user, $password, $database);
    ---*/

    // 2 : ADO connection 'postgresql9' = CURRENTLY SUPPORTED
    // Docs : https://adodb.org/dokuwiki/doku.php?id=v5:database:postgresql
    // Docs used in this example (because this supports both Windows and Linux PostgreSQL installations) : https://adodb.org/dokuwiki/doku.php?id=v5:database:pdo#pdo_pgsql
    /*--- example connection code                    :
    $db = newAdoConnection('pdo');
    $dsn  = 'pgsql:host=192.168.0.212;dbname=dvdrental';
    $user = 'someuser';
    $pass = 'somepass';
    $db->connect($dsn,$user,$pass);
    ---*/


class class_NicerAppWebOS_database_API_adodb {
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
}

?>
