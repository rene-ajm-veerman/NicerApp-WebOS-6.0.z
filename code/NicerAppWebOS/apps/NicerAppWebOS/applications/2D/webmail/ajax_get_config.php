<?php
    require_once(dirname(__FILE__).'/../../../boot.php');
require_once(dirname(__FILE__).'/../../../3rd-party/vendor/autoload.php');
//require_once(dirname(__FILE__).'/../../../3rd-party/vendor/defuse/php-encryption/src/Crypto.php');
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
    
    /*
    $ip = array_key_exists('X-Forwarded-For',apache_request_headers())
        ? apache_request_headers()['X-Forwarded-For'] 
        : $_SERVER['REMOTE_ADDR'];
        
    switch ($ip) {
        case '::1':
        case '127.0.0.1':
        case '192.168.178.30':
        case '80.101.238.137':
            $fn = 'config.'.$_SESSION['cdb_loginName'].'.json';
            echo file_get_contents($fn);
            break;
        default:
            echo '{"ERROR":"IP '.$ip.' is not whitelisted."';
            break;
    }*/
    
    global $naWebOS;
    /*
    $username = $_SESSION['cdb_loginName'];
    $fn = dirname(__FILE__).'/configs/config.'.$username.'.json';
    $fn2 = '...'.str_replace ($naWebOS->codePath,'',$fn);
    if (file_exists($fn)) {
        $jsonText = file_get_contents($fn);
        $json = json_decode ($jsonText, true);
        if (is_null($json)) {
            echo 'Error in "'.$fn2.'" : '.json_last_error_msg();
        } else echo $jsonText;
    } else {
        echo 'File "'.$fn2.'" does not exist.';
    }*/
    global $cdb;
    
    $cdbDomain = str_replace('.','_',$naWebOS->domainFolder);
    $dbName = $cdbDomain.'___webmail_accounts';
    $cdb->setDatabase ($dbName, false);
    
    //$username = $_SESSION['cdb_loginName'];
    //$username = str_replace(' ', '__', $username);
    //$username = str_replace('.', '_', $username);
    $username = $db->translate_plainUserName_to_couchdbUserName ($_SESSION['cdb_loginName']);
    
    try {
        $call = $cdb->get($username);
    } catch (Exception $e) {
        echo '{}';
        exit();
    }
    //echo '<pre>'; var_dump ($call);
    
    $d = $call->body;
    $plaintext_webmailSettingsJSON = Crypto::decryptWithPassword ($d->webmailSettingsJSON_hashCouchDB, $d->hashCouchDB);
    echo $plaintext_webmailSettingsJSON;
?>
