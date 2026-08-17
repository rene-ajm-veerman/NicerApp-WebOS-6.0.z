<?php 

// default usage (bookmark this for https://YOURDOMAIN.TLD/NicerAppWebOS/db_init.php?....) :
// http://localhost/NicerAppWebOS/db_init.php?doTree=y&doThemeData=y&doMenu=y&doAPI_imageSearch=y&doApp_news=y&doApp_webmail=y&doApp_3D_fileExplorer=y
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


global $naBypassMainErrorHandler; $naBypassMainErrorHandler = false;
require_once (realpath(dirname(__FILE__)).'/boot.php');

$debug = true; 
global $naLAN;

$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
//echo '<h1>'.$ip.'</h1>'; exit();
if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && !$naLAN
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    exit();
}

global $naWebOS;
//echo '<pre>'; var_dump ($naWebOS); exit();

$un1 = strtolower(trim($naWebOS->ownerInfo['OWNER_NAME']));
$un1 = str_replace(' ', '_', $un1);
$un1 = str_replace('.', '__', $un1);
$db1 = $naWebOS->domainFolderForDB.'___'.trim($naWebOS->ownerInfo['OWNER_NAME']);

$dbs = [
    'analytics',
    'errorHandling',
    'logEntries',
    'ip_info',
    'views',
    //'data_by_users',
    'cms_tree',
    'cms_tree___role___guests',
    'cms_tree___role___website',
    'cms_tree___user___administrator___'.$un1,
    'cms_tree___user___guest',
    'cms_documents___role___guests',
    'cms_documents___role___website',
    'cms_documents___user___administrator___'.$un1,
    'cms_documents___user___guest',
    'cms_comments',
    'themes',
    'api_wallpaperscraper__plugin_bingImages',
    'api_wallpaperscraper__plugin_googleImages',
    'app_2D_news__rss_items'//,
    //'app_2D_webmail__accounts',
    //'app_3D_fileManager__tree_d_positions'
];
$dbs2 = [];
foreach ($dbs as $i => $db) {
    $dbs2[$db] = false;
}
$dbs = $dbs2;
//echo '<pre style="color:blue">'; var_dump ($dbs); echo '</pre>';

$dbsReset = [
    'analytics',
    'errorHandling',
    'logEntries',
    'ip_info',
    //'data_by_users',
    'views',
    'cms_tree',
    'cms_tree___role___guests',
    'cms_tree___role___website',
    'cms_tree___user___administrator___'.$un1,
    'cms_tree___user___guest',
    'cms_documents___role___guests',
    'cms_documents___role___website',
    'cms_documents___user___administrator___'.$un1,
    'cms_documents___user___guest',
    'cms_comments',
    'themes',
    'api_wallpaperscraper__plugin_bingImages',
    'api_wallpaperscraper__plugin_googleImages',
    'app_2D_news__rss_items'//,
    //'app_2D_webmail__accounts',
    //'app_3D_fileManager__tree_d_positions'
];
$dbs2Reset = [];
foreach ($dbsReset as $i => $db) {
    $dbs2Reset[$db] = false;
}
$dbsReset = $dbs2Reset;


function mustDo ($dbCategoryName) {
    $fncn = '.../NicerAppWebOS/db_init.php::mustDo($dbCategoryName)';

    $key = 'do'.strtoupper(substr($dbCategoryName,0,1)).substr($dbCategoryName,1);
    if (array_key_exists($key, $_GET)) {
        return $_GET[$key]!=='no';

    } elseif (array_key_exists('dbCategoryNames', $_GET)) {
        $mustDoDBs = json_decode(decode_base64_url($_GET['dbCategoryNames']), true);
        $in = in_array($dbCategoryName, $mustDoDBs);
        if ($in && $mustDoDBs[$dbCategoryName]!=='no') return true; else return false;
    }

    return true;
}

function goDo ($dbs, $goDoItems) {
    foreach ($goDoItems as $i => $ii) {
        $dbs[$ii] = true;
    }
    return $dbs;
}


function mustReset ($dbCategoryName) {
    $fncn = '.../NicerAppWebOS/db_init.php::mustDo($dbCategoryName)';

    $key = 'reset'.strtoupper(substr($dbCategoryName,0,1)).substr($dbCategoryName,1);
    if (array_key_exists($key, $_GET)) {
        return $_GET[$key]!=='no';

    };

    return false;
}

function goReset ($dbsReset, $goResetItems) {
    foreach ($goResetItems as $i => $ii) {
        $dbsReset[$ii] = true;
    }
    return $dbsReset;
}

function addPrefixes ($dbs) {
    global $naWebOS;
    $dbs2 = [];
    foreach ($dbs as $dbName => $doDB)
        $dbs2[$naWebOS->domainFolderForDB.'___'.$dbName] = $doDB;
    return $dbs2;
}



if (mustDo('analytics')) {
    $dbs = goDo ($dbs, [ 'analytics' ]);
}
if (mustDo('log')) {
    $dbs = goDo ($dbs, [ 'errorHandling', 'logEntries' ]);
}
if (mustDo('urlRedirection')) {
    $dbs = goDo ($dbs, [ 'data_by_users', 'views' ]);
}

//echo '<pre style="color:red">'; var_dump (mustDo('cms')); echo '</pre>';
if (mustDo('cms')) {
    $un1 = strtolower(trim($naWebOS->ownerInfo['OWNER_NAME']));
    $un1 = str_replace(' ', '_', $un1);
    $un1 = str_replace('.', '__', $un1);
    $dbs = goDo ($dbs, [
        'ip_info',
        'cms_tree',
        'cms_tree___role___guests',
        'cms_tree___role___website',
        'cms_tree___user___administrator___'.$un1,
        'cms_tree___user___guest',
        'cms_documents___role___guests',
        'cms_documents___role___website',
        'cms_documents___user___administrator___'.$un1,
        'cms_documents___user___guest',
        'cms_comments'
    ]);
};

if (mustDo('themeData')) {
    $dbs = goDo ($dbs, [ 'themes' ]);
};
if (mustReset('themeData')) {
    $dbs['themes'] = false; // don't delete *all* the user supplied data!
    $dbsReset = goReset ($dbsReset, [ 'themes' ]);
};

if (mustDo('api_imageSearch')) {
    $dbs = goDo ($dbs, [
        'api_wallpaperscraper__plugin_googleImages',
        'api_wallpaperscraper__plugin_bingImages'
    ]);
};
if (mustDo('app_news')) {
    $dbs = goDo ($dbs, [ 'app_2D_news__rss_items' ]);
};
if (mustDo('app_webmail')) {
    $dbs = goDo ($dbs, [ 'app_2D_webmail__accounts' ]);
};
if (mustDo('app_fileManager')) {
    $dbs = goDo ($dbs, [ 'app_3D_fileManager__tree_d_positions' ]);
};
//echo '<pre style="color:blue">'; echo json_encode ($dbs, JSON_PRETTY_PRINT); echo '</pre>';

$dbs2 = addPrefixes($dbs);
//echo '<pre style="color:lime;background:navy;border-radius:10px;margin:10px;">t118:'; echo json_encode ($naWebOS->dbsAdmin->findConnection('couchdb'), JSON_PRETTY_PRINT); echo '</pre>';
try {
    $allDBs = $naWebOS->dbsAdmin->getAllDatabases ();
} catch (Exception $e) {
    $fn = dirname(__FILE__).'/domainConfigs/'.$naWebOS->domainFolder.'/databases.username-admin.json';
    $msg =
        $e->getMessage().'<br/>'
        .str_replace('#', '<br/>&nbsp;&nbsp;&nbsp;#', $e->getTraceAsString()).'<br/>'
        .'credentials used are in '.$fn;
    echo $msg;
    die();
}
//echo '<pre style="color:green">'; var_dump ($allDBs); echo '</pre>'; die();
//echo '<pre style="color:green">'; var_dump ($dbs); echo '</pre>'; die();
echo $naWebOS->dbsAdmin->listDatabases ($allDBs, $dbs, $dbsReset);

//echo require_return(dirname(__FILE__).'/domainConfigs/'.$naWebOS->domainFolder.'/database.users.json.php', true); die();

$users = safeLoadJSONfile($naWebOS->path.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/database.users.json.php', true);
//echo '<pre style="color:black;background:skyblue;">'; var_dump ($users); die();
//$users = json_decode($usersJSON, true);
$groups = safeLoadJSONfile($naWebOS->path.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/database.groups.json.php', true);
//echo '<pre>'; var_dump ($groups); die();
//$groups = json_decode($groupsJSON, true);

$clientUsersJSONfn = dirname(__FILE__).'/domainConfigs/'.$naWebOS->domainFolder.'/database.users.CLIENT.json.php';
$clientUsersJSON = (!file_exists($clientUsersJSONfn) ? '' : require_return($clientUsersJSONfn, true));
$clientUsers = json_decode ($clientUsersJSON, true);
$clientGroupsJSONfn = dirname(__FILE__).'/domainConfigs/'.$naWebOS->domainFolder.'/database.groups.CLIENT.json.php';
$clientGroupsJSON = (!file_exists($clientGroupsJSONfn) ? '' : require_return($clientGroupsJSONfn, true));
$clientGroups = json_decode ($clientGroupsJSON, true);

if (!is_null($clientUsers))
    $usersFinal = array_merge_recursive($users, $clientUsers);
else $usersFinal = $users;

if (!is_null($clientGroups))
    $groupsFinal = array_merge_recursive($groups, $clientGroups);
else $groupsFinal = $groups;

$naWebOS->dbsAdmin->clearOutDatabases ($dbs2);
$naWebOS->dbsAdmin->createUsers($users, $groupsFinal);

$naWebOS->dbsAdmin->createDatabases ($dbs);
$naWebOS->dbsAdmin->resetDatabases ($dbsReset);


$fn = dirname(__FILE__).'/scripts.maintenance/htaccess.build.php';
/*
$xec = 'php "'.$fn.'"';
exec ($xec, $output, $result);
$dbg = [
    '$xec' => $xec,
    '$result' => $result,
    '$output' => $output
];
echo '<pre>'; var_dump($dbg); echo '</pre>';
*/
require_once ($fn);

$fn = dirname(__FILE__).'/scripts.maintenance/htaccess.build-views.php';
/*
$xec = 'php "'.$fn.'"';
exec ($xec, $output, $result);
$dbg = [
    '$xec' => $xec,
    '$result' => $result,
    '$output' => $output
];
echo '<pre>'; var_dump($dbg); echo '</pre>';
*/
require_once ($fn);
?>
