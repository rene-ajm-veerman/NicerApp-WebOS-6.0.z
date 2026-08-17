<?php 
// called via AJAX from browser javascript

// requirements :
// https://github.com/defuse/php-encryption/blob/master/docs/Tutorial.md
// https://github.com/skeyby/sag
require_once (dirname(__FILE__).'/../../../boot.php');
require_once(dirname(__FILE__).'/../../../3rd-party/vendor/autoload.php');
//require_once(dirname(__FILE__).'/../../../3rd-party/vendor/defuse/php-encryption/src/Crypto.php');
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;


    global $naWebOS;
    $naWebOS = new NicerAppWebOS();
    $naWebOS->init();
    $cdbDomain = str_replace('.','_',$naWebOS->domainFolder);

    $couchdbConfigFilepath = realpath(dirname(__FILE__)).'/../../../domainConfigs/'.$naWebOS->domainFolder.'/couchdb.json';
    $cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);
    //var_dump ($cdbConfig); echo PHP_EOL;

    
    $cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
    $cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
    $cdb->useSSL($cdbConfig['useSSL']);

    $plaintext_webmailSettingsJSON = $_POST['config'];
    
    $usernameCouchDB = $_COOKIE['cdb_loginName'];
    $usernameCouchDB = str_replace(' ', '__', $usernameCouchDB);
    $usernameCouchDB = str_replace('.', '_', $usernameCouchDB);

    login ($cdb);
    $hash_couchDB = $_COOKIE['cdb_authSession_cookie'];
    
    $dbName = $cdbDomain.'___webmail_accounts';
    //var_dump ($dbName); var_dump($_POST);
    $cdb->setDatabase($dbName, false);
    
    $newDoc = array (
        '_id' => $_COOKIE['cdb_loginName'],
        'hashPassword' => '',//password_hash ($plaintextPassword, PASSWORD_DEFAULT, ['cost'=>12]),
        'hashCouchDB' => $hash_couchDB
    );
    $findCommand = array (
        'selector' => array (
            '_id' => $usernameCouchDB
        ),
        'fields' => array ( '_id', 'hashPassword', 'hashCouchDB', 'webmailSettingsJSON_hashPassword', 'webmailSettingsJSON_hashCouchDB' )
    );
    try {
        $call = $cdb->find ($findCommand);
    } catch (Exception $e) {
        $cdb->put ($usernameCouchDB, $newDoc);
    }
    //var_dump($call);exit();
    if (array_key_exists(0, $call->body->docs)) {
        $call = $cdb->get ($usernameCouchDB);
        $newDoc['_rev'] = $call->body->_rev;
    }
    $plaintext_webmailSettingsJSON = $_POST['config'];
    
    //$key_couchDB = Key::loadFromAsciiSafeString(ascii2hex($hash_couchDB));
    
    $newDoc['webmailSettingsJSON_hashPassword'] = '';// DONE BY .../NicerAppWebOS/businessLogic/ajax/ajax_login.php!
    $newDoc['webmailSettingsJSON_hashCouchDB'] = Crypto::encryptWithPassword ($plaintext_webmailSettingsJSON, $hash_couchDB);
    
    $call = $cdb->put ($usernameCouchDB, $newDoc);
    if ($call->status=="201") {
        echo 'status : Success';
    } else {
        echo 'status : Failure';
    }
?>
