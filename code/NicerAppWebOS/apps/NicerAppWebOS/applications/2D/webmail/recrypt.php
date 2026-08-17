<?php 
global $rootPath_cyslw;
$rootPath_cyslw = realpath(dirname(__FILE__).'/../../../../../../');
//echo $rootPath_cyslw; exit();

require_once($rootPath_cyslw.'/NicerAppWebOS/3rd-party/vendor/autoload.php');
//require_once(dirname(__FILE__).'/../../../3rd-party/vendor/defuse/php-encryption/src/Crypto.php');
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;


function nicer_app__webmail__recrypt ($username, $plaintextPassword, $newHash_couchDB) {
// called from .../NicerAppWebOS/businessLogic/ajax/ajax_login.php -> .../NicerAppWebOS/functions.php:recrypt()

// requirements :
// https://github.com/defuse/php-encryption/blob/master/docs/Tutorial.md
// https://github.com/skeyby/sag

    global $naWebOS;
    global $rootPath_cyslw;
    $naWebOS = new NicerAppWebOS();
    $naWebOS->init();
    $cdbDomain = str_replace('.','_',$naWebOS->domainFolder);

    $couchdbConfigFilepath = $rootPath_cyslw.'/domainConfig/'.$naWebOS->domainFolder.'/couchdb.json';
    $cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);
    //var_dump ($cdbConfig); echo PHP_EOL;

    
    $cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
    $cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
    $cdb->useSSL($cdbConfig['useSSL']);

    $usernameCouchDB = $username;
    $usernameCouchDB = str_replace(' ', '__', $usernameCouchDB);
    $usernameCouchDB = str_replace('.', '_', $usernameCouchDB);
    
    //var_dump ([$usernameCouchDB, $plaintextPassword]);
    $cdb->login($usernameCouchDB, $plaintextPassword);
    
    $dbName = $cdbDomain.'___webmail_accounts';
    //var_dump ($dbName); var_dump($_POST);
    $cdb->setDatabase($dbName, false);
    
    $newDoc = array (
        '_id' => $username,
        'hashPassword' => password_hash ($plaintextPassword, PASSWORD_DEFAULT, ['cost'=>12]),
        'hashCouchDB' => $newHash_couchDB
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
    
    if (!array_key_exists(0, $call->body->docs)) {
        //$call = $cdb->put ($usernameCouchDB, $newDoc);
        //$newDoc['_rev'] = $call->body->rev;
        
        // IGNORE this case, because .../NicerAppWebOS/apps/nicer.app/webmail/ajax_editConfig.php will write the webmailSettingsJSON values into the db.
    } else {
        $call = $cdb->get ($usernameCouchDB);
        $d = $call->body;
        $newDoc['_rev'] = $d->_rev;
        
        $plaintext_webmailSettingsJSON = Crypto::decryptWithPassword ($d->webmailSettingsJSON_hashCouchDB, $d->hashCouchDB);
        
        $newHash_password = password_hash ($plaintextPassword, PASSWORD_DEFAULT, ['cost'=>12]);
        
        $newDoc['webmailSettingsJSON_hashPassword'] = Crypto::encryptWithPassword ($plaintext_webmailSettingsJSON, $newHash_password);
        $newDoc['webmailSettingsJSON_hashCouchDB'] = Crypto::encryptWithPassword ($plaintext_webmailSettingsJSON, $newHash_couchDB);
        
        $call = $cdb->put ($usernameCouchDB, $newDoc);
    }
    
    
    //echo '<pre>'; var_dump ($call); exit();
}
?>
