<?php
global $useRememberMe; $useRememberMe = true;

$rootPathNA = realpath(dirname(__FILE__).'/../../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

$debug = false;
if ($debug) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600 * 24 * 7);
    session_start();
} else session_regenerate_id();

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
/*if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '80.101.238.137'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}*/

global $naWebOS;
$cdbDomain = $naWebOS->domainFolderForDB;//str_replace('.','_',$naWebOS->domainFolder);

$db = $naWebOS->dbs->findConnection('couchdb');
$cdb = $db->cdb;

$username = $db->translate_plainUserName_to_couchdbUserName ($_POST['loginName']);
$username1 = preg_replace('/.*___/', '', $username);

//echo $cdbDomain.'___'.$username.'<br/>';
$_SESSION['cdb_pw'] = $_POST['pw'];

try {
    //$cdb_authSession_cookie = $cdb->login($username, $_POST['pw'], Sag::$AUTH_COOKIE);

    $xec = 'curl -vD - -X POST http://'.$username.':'.$_POST['pw'].'@127.0.0.1:5984/_session -d \'name='.$username.'&password='.$_POST['pw'].'\'';
    exec ($xec, $output, $result);
    if ($debug) { echo '<pre>'; var_dump ($xec); echo PHP_EOL; var_dump ($output); echo '</pre>'; };
    preg_match_all('/=(.*?);/', $output[6], $output2);
    if (count($output2[1])>0) {
        //echo '<pre>'; var_dump ($output2); echo '</pre>'.PHP_EOL;
        setcookie ('cdb_authSession_cookie', $output2[1][0], strtotime($output2[1][2]), '/', $naWebOS->domain);
        setcookie ('cdb_loginName', $username, strtotime($output2[1][2]), '/', $naWebOS->domain);
        $_SESSION['cdb_loginName'] = $username;
    } else {
        // failed; re-create user rec and try again
        $xecA = 'curl -vD - -X POST https://'.$naWebOS->domain.'/NicerAppWebOS/businessLogic/ajax/ajax_register.php -d "loginName='.$username.'" -d "pw='.$_POST['pw'].'" -d "email=no-reply@hotmail.com"';
        exec ($xecA, $outputA, $resultA);
        //echo '<pre>'; var_dump ($xecA); echo PHP_EOL; var_dump ($outputA); echo '</pre>';
    }

} catch (Exception $e) {
    echo 'status : Failed.<br/>'.PHP_EOL;
    echo '$cdbDomain."___".$username='.$username.', $e->getMessage() = '.$e->getMessage();
    exit();
}

/*
$dbName = $cdbDomain.'___cms_tree___user___'.strtolower($username1);
//var_dump ($dbName); var_dump($_POST);

$cdb->setDatabase($dbName, false);
try {
    //var_dump ($cdb->getAllDocs());
    $rows = $cdb->getAllDocs()->body->rows;
    $callOK = is_array($rows) && count($rows) >= 1;
} catch (Exception $e) {
    echo 'status : Failed'.PHP_EOL;
    $dbg = [
        'database' => $dbName,
        'username' => $username,
        'username1' => $username1,
        'password' => 'HIDDEN',
        '$e->getMessage()' => $e->getMessage()
    ];

    echo '<pre style="color:red">'; var_dump ($dbg); echo '</pre>';
    exit();
}
//var_dump ($cdb->getAllDocs());
//var_dump ($callOK);
if ($callOK) {


    //echo '<pre>'; var_dump($_POST); echo '</pre>'; exit();

    $_SESSION['cdb_loginName'] = $_POST['loginName'];
    //var_dump ($_SESSION); exit();
    //setcookie("cbd_loginName", "", time() - 3600);
    //usleep(50);
    setcookie('cdb_loginName', $_POST['loginName'], time() + 604800, '/');

    /*
    ENCRYPTED DATA IS ALWAYS STORED IN 2 FLAVORS :
    - [1] encrypted with plaintext password
    - [2] encrypted with salted hash from $_COOKIE['cdb_authSession_cookie']

    The default is to use [2] to decrypt encrypted data for apps, because the end-user's
    plaintext password is NEVER stored on the server in any way.

    But if the cookies are lost somehow (most likely due to a user deleting all his/her cookies),
    the requird data (per app) can still be decrypted with the plaintext password based hash value[1],
    and then re-encrypted with the new $_COOKIE['cdb_authSession_cookie'] that is the result of
    a call to ajax_login.php with the plaintext password.
    * /
    if (array_key_exists('REMEMBERME',$_COOKIE)) { //echo 1;
        global $rememberMe;
        //if (!empty($_POST['rememberme'])) { echo 'a';
        /*
            $rememberMe->createCookie($_POST['loginName']);
            unset($_COOKIE['cdb_loginName']);
            setcookie('cdb_loginName', $_POST['loginName'], time() + 604800, '/');
            unset($_COOKIE['cdb_authSession_cookie']);
            setcookie('cdb_authSession_cookie', null, -1, '/');
            unset($_COOKIE['AuthSession']);
            setcookie('AuthSession', null, -1, '/');
            //echo '2status : Success';
        //} else { echo 'b';
            $rememberMe->clearCookie();

            recrypt ($_POST['loginName'], $_POST['pw'], $cdb_authSession_cookie);
        * /
            $_SESSION['cdb_authSession_cookie'] = $cdb_authSession_cookie;
            setcookie('cdb_authSession_cookie', $cdb_authSession_cookie, time() + 604800, '/'); // 604800 = 7 days measured in seconds (cookie timeout length)
        //}

    } else { //echo 2;

        //recrypt ($_POST['loginName'], $_POST['pw'], $cdb_authSession_cookie);

        $_SESSION['cdb_authSession_cookie'] = $cdb_authSession_cookie;
        setcookie('cdb_authSession_cookie', $cdb_authSession_cookie, time() + 604800, '/'); // 604800 = 7 days measured in seconds (cookie timeout length)
    }
    //$_SESSION['cdb_pw'] = $_POST['pw'];
    //var_dump (empty($_POST['rememberme'])); echo PHP_EOL;
    //var_dump ($_COOKIE);
*/
    echo 'status : Success';
//} else {
  //  echo 'status : Failed';
//}
?>
