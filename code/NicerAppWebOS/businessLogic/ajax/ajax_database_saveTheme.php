<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');
global $naIP;
global $naWebOS;

$useAdminLogin = false; // bugfix when boolean 'true' (NO LONGER NEEDED)
$debug = false;
if ($debug) {
    echo 'info : '.__FILE__.' : $debug = true.<br/>'.PHP_EOL;
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    echo '<pre>';
}

$date = new DateTime();
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

if ($useAdminLogin) {
    $db = $naWebOS->dbsAdmin->findConnection('couchdb');
    $cdb = $db->cdb;
} else {
    $db = $naWebOS->dbs->findConnection('couchdb');
    $cdb = $db->cdb;
}

$dataSetName = $cdbDomain.'___themes';
//echo $dataSetName; die();
$cdb->setDatabase($dataSetName, false);


$findCommand = array (
    'selector' => array(
        //'ip' => $naIP,
        'theme' => $_POST['theme']
    ),
    'fields' => array(
        '_id', '_rev'
    )
);

if (array_key_exists('orientation',$_POST) && !is_null($_POST['orientation'])) $findCommand['selector']['orientation'] = $_POST['orientation'];
if (array_key_exists('app',$_POST) && !is_null($_POST['app'])) $findCommand['selector']['app'] = $_POST['app'];
if (array_key_exists('view',$_POST) && !is_null($_POST['view'])) $findCommand['selector']['view'] = $_POST['view'];
if (array_key_exists('url',$_POST) && !is_null($_POST['url'])) $findCommand['selector']['url'] = $_POST['url'];
if (array_key_exists('role',$_POST) && !is_null($_POST['role'])) $findCommand['selector']['role'] = $_POST['role'];
if (array_key_exists('user',$_POST) && !is_null($_POST['user'])) $findCommand['selector']['user'] = $_POST['user'];
if (array_key_exists('ip',$_POST) && !is_null($_POST['ip'])) $findCommand['selector']['ip'] = $_POST['ip'];

if (array_key_exists('specificityName',$_POST) && !is_null($_POST['specificityName'])) {
    $findCommand['selector']['specificityName'] = $_POST['specificityName'];
    if (
        strpos($_POST['specificityName'], 'current page')!==false
    ) {
        unset ($findCommand['selector']['app']);
        unset ($findCommand['selector']['view']);
    }
    if (
        strpos($_POST['specificityName'], 'app \'')!==false
    ) {
        unset ($findCommand['selector']['view']);
        unset ($findCommand['selector']['url']);
    }
    if (
        strpos($_POST['specificityName'], 'site ')!==false
    ) {
        unset ($findCommand['selector']['view']);
        unset ($findCommand['selector']['app']);
        unset ($findCommand['selector']['url']);
    }
    if (
        strpos($_POST['specificityName'], 'user ')!==false
    ) {
        unset ($findCommand['selector']['role']);
    }
    if (
        strpos($_POST['specificityName'], 'group ')!==false
    ) {
        unset ($findCommand['selector']['user']);
    }
}

/*
$proceed = $cdb->havePermission ($dataSetName, $findCommand);
if (!$proceed) {
    cdb_error (503, null, 'Status : Failed. (You don\'t have permission to access this data).');
    exit();
}
*/


if ($debug) { echo 't1 $findCommand='; var_dump ($findCommand); echo PHP_EOL.PHP_EOL; }
$call = $cdb->find ($findCommand);
if ($debug) { echo 't1 $call='; var_dump ($call); echo PHP_EOL.PHP_EOL; }

if (!$call->headers->_HTTP->status===200) { 
    $id = cdb_randomString(20); 
    $rec = array('id'=>$id);
} else {
    //echo '<pre>'; var_dump ($call->body); die();
    if (array_key_exists(0, $call->body->docs)) {
        $id = $call->body->docs[0]->_id;
        $call = $cdb->get($id);
        //echo json_encode($call, JSON_PRETTY_PRINT).'<br/>'.PHP_EOL; exit();
        $rec = (array)$call->body;
    } else {
        $id = cdb_randomString(20); 
        $rec = array('_id'=>$id);
    }
}

$rec2 = array (
    'theme' => $_POST['theme'],
    'textBackgroundOpacity' => floatval($_POST['textBackgroundOpacity']),
    'themeSettings' => json_decode($_POST['themeSettings'], true),
    'view' => json_decode($_POST['view'], true),
    'apps' => json_decode($_POST['apps'], true),
    'backgroundSearchKey' => $_POST['backgroundSearchKey'],
    'background' => $_POST['background'],
    'vdSettings_show' => $_POST['vdSettings_show'],
    'backgroundChange_hours' => $_POST['backgroundChange_hours'],
    'backgroundChange_minutes' => $_POST['backgroundChange_minutes'],
    'changeBackgroundsAutomatically' => $_POST['changeBackgroundsAutomatically'],
    'menusFadingSpeed' => $_POST['menusFadingSpeed'],
    'menusUseRainbowPanels' => $_POST['menusUseRainbowPanels'],
    'ip' => $naIP,
    'ua' => $_SERVER['HTTP_USER_AGENT'],
    'lastUsed' => date('U')
);
if (array_key_exists('specificityName',$_POST) && !is_null($_POST['specificityName'])) $rec2['specificityName'] = $_POST['specificityName'];
if (array_key_exists('orientation',$_POST) && !is_null($_POST['orientation'])) $rec2['orientation'] = $_POST['orientation'];
if (array_key_exists('app',$_POST) && !is_null($_POST['app'])) $rec2['app'] = $_POST['app'];
if (array_key_exists('view',$_POST) && !is_null($_POST['view'])) $rec2['view'] = $_POST['view'];
if (array_key_exists('url',$_POST) && !is_null($_POST['url'])) $rec2['url'] = $_POST['url'];
if (array_key_exists('role',$_POST) && !is_null($_POST['role'])) $rec2['role'] = $_POST['role'];
if (array_key_exists('user',$_POST) && !is_null($_POST['user'])) $rec2['user'] = $_POST['user'];

if (preg_match('/at the client/', $rec2['specificityName'])!==1) {
    unset ($rec2['ip']);
    unset ($rec2['ua']);
};

if (array_key_exists('specificityName',$_POST) && !is_null($_POST['specificityName'])) {
    if (
        strpos($_POST['specificityName'], 'current page')!==false
    ) {
        unset ($rec2['app']);
        unset ($rec2['view']);
    }
    if (
        strpos($_POST['specificityName'], 'app \'')!==false
    ) {
        unset ($rec2['view']);
        unset ($rec2['url']);
    }
    if (
        strpos($_POST['specificityName'], 'site ')!==false
    ) {
        unset ($rec2['view']);
        unset ($rec2['app']);
        unset ($rec2['url']);
    }
    if (
        strpos($_POST['specificityName'], 'user ')!==false
    ) {
        unset ($rec2['role']);
    }
    if (
        strpos($_POST['specificityName'], 'group ')!==false
    ) {
        unset ($rec2['user']);
    }
}
$dbg = [
    '1' => preg_match('/at the client/', $rec2['specificityName']),
    '2' => $rec2
];

/*if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.gc_maxlifetime', 3600);
    session_start();
};*/
//echo '<pre>'; var_dump ($dbg); die();
if (!isset($_SESSION) || !is_array($_SESSION) || !array_key_exists('selectors',$_SESSION)) {
    echo 'Session does not contain required "selectors" data.'; exit();
} else {
        $d1 = $naWebOS->getPageCSS_permissionsList(false);
        $d2 = $naWebOS->getPageCSS_checkPermissions($d1);
        $selectors = $d2['selectors'];


    //$selectors = json_decode ($naWebOS->selectors, true); //$_SESSION['selectors'],true);


    foreach ($selectors as $idx => $selector) {
        $dbg2 = [
            '1' => $selector['specificityName'],
            '2' => $_POST['specificityName'],
            'selectors' => $selectors
        ];
        //if ($debug) { echo '<pre style="margin:5px;border-radius:10px;padding:5px;background:navy;color:lime">'; var_dump ($dbg2); echo '</pre>'; }

        if ($selector['specificityName']===$_POST['specificityName']) $sel = $selector;
    }
    //die();
    //var_dump ($selectors);    var_dump ($sel);    var_dump ($_POST);    die();

    if ($debug) { echo '$sel='; var_dump ($sel); }
    if (!array_key_exists('permissions',$sel)) {
        echo 'SESSION selector does not contain a "permissions" entry.'; exit();
    } else {
        if (!array_key_exists('write', $sel['permissions'])) {
            echo 'SESSION selector does not contain a "write" entry under "permissions".'; exit();
        }
        }
    foreach ($sel['permissions']['write'] as $pur => $urName) { // pur = permissionsUserRole, urName = userOrRoleName

        $permissions = $sel['permissions'];
        
        global $naWebOS;

        
        if ($useAdminLogin) {
            $hasPermission = true;
        } else {

            $username100 =
                (array_key_exists('cdb_loginName', $_SESSION)
                    ? $_SESSION['cdb_loginName']
                    : (array_key_exists('cdb_loginName', $_COOKIE)
                        ? $_COOKIE['cdb_loginName']
                        : 'Guest')
            );
            $username100 = preg_replace ('/.*___(.*)/', '$1', $username100);
            //echo '<pre style="color:blue;">'; var_dump ($_SESSION); echo '</pre>'; exit();
            $username101 = $db->translate_plainUserName_to_couchdbUserName ($username100);


            // check permissions
            $hasPermission = false;
            if ($useAdminLogin) {
                $roles = $naWebOS->dbsAdmin->findConnection('couchdb')->roles;
            } else {
                $roles = $naWebOS->dbs->findConnection('couchdb')->roles;
            }
            if ($debug) {
                echo '<pre>$_COOKIE='; var_dump ($_COOKIE); echo '</pre>'.PHP_EOL.PHP_EOL;
                echo '<pre>$username101='; var_dump ($username101); echo '</pre>'.PHP_EOL.PHP_EOL;
                echo '<pre>$roles='; var_dump ($roles); echo '</pre>'.PHP_EOL.PHP_EOL;
                echo '<pre>$permissions='; var_dump ($permissions); echo '</pre>'.PHP_EOL.PHP_EOL;
            };
            foreach ($permissions as $permissionType => $permissionRec) {
                if ($permissionType=='write') {
                    foreach ($permissionRec as $accountType => $permissionsList) {
                        foreach ($permissionsList as $idx321 => $userOrGroupID) {
                            if ($accountType == 'roles') {
                                foreach ($roles as $roleIdx => $groupID) {
                                    if ($userOrGroupID==$groupID) {
                                        $hasPermission = true;
                                    }
                                }
                            }
                            if ($accountType == 'users' && (array_key_exists('cdb_loginName',$_COOKIE)?$_COOKIE['cdb_loginName']:$username101) == $userOrGroupID) {
                                $hasPermission = true;
                            }
                        }
                    }
                }
            }
        }
        if (!$hasPermission) {
            if ($debug) echo 'You have no permission to write this data into the database.'.PHP_EOL;
            echo 'status : Failed.';
            exit();
        }        
    }    
}

$cdb->setDatabase($dataSetName, false);

// DONT! messes up ___themes settings horribly during updates :
// $rec = array_merge ($rec, $rec2);

// instead, use only the _id and _rev of the found document and overwrite the rest of the document with just the data that was fed into this script by the browser.
if (array_key_exists('_rev', $rec)) $rev = $rec['_rev']; else $rev=null;
$rec = $rec2;
if (!is_null($rev)) $rec['_rev'] = $rev;
$rec['_id'] = $id;

$rec = negotiateOptions ($sel, $rec);
unset($rec['display']);
unset($rec['has_read_permission']);
unset($rec['has_write_permission']);
unset($rec['beginDateTime']);
unset($rec['endDateTime']);

if ($debug) { echo '<pre>$rec (merged) : '; var_dump ($rec); var_dump($_POST); var_dump(json_last_error()); echo '</pre>'.PHP_EOL.PHP_EOL; }
try {
    $call3 = $cdb->post($rec);
} catch (Exception $e) {
    if ($debug) {
        echo 'status : Failed : could not update record in database ('.$dataSetName.').<br/>'.PHP_EOL;
        echo '$cdbDomain=';var_dump($cdbDomain);echo '<br/>'.PHP_EOL;
        echo '$rec = <pre style="color:blue">'.PHP_EOL; var_dump ($rec); echo PHP_EOL.'</pre>'.PHP_EOL;
        echo '$call3 = <pre style="color:red">'.PHP_EOL; var_dump ($call3); echo PHP_EOL.'</pre>'.PHP_EOL;
        echo '$e = <pre style="color:red">'.PHP_EOL; var_dump ($e); echo PHP_EOL.'</pre>'.PHP_EOL; 
        exit();
    
    } else {
        echo 'status : Failed.'; exit();
    }
}
if ($debug) { echo '<pre>$call3='; var_dump ($call3); var_dump($rec); var_dump(json_last_error()); echo '</pre>'.PHP_EOL.PHP_EOL; }
        
if ($call3->headers->_HTTP->status=='201' || $call3->headers->_HTTP->status=='200') {
    echo 'status : Success';
} else {
    echo 'status : Failed';
}
?>
