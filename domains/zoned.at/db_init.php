<h1>nicerapp couchdb initialization script</h1>
<?php 
require_once (dirname(__FILE__).'/3rd-party/sag/src/Sag.php');
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL);


$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
if (
    $ip !== '::1'
    && $ip !== '127.0.0.1'
    && $ip !== '45.83.241.21'
) {
    header('HTTP/1.0 403 Forbidden');
    echo '403 - Access forbidden.';
    die();
}

$couchdbConfigFilepath = realpath(dirname(__FILE__)).'/couchdb.json';
$cdbConfig = json_decode(file_get_contents($couchdbConfigFilepath), true);

$cdb = new Sag($cdbConfig['domain'], $cdbConfig['port']);
$cdb->setHTTPAdapter($cdbConfig['httpAdapter']);
$cdb->useSSL($cdbConfig['useSSL']);
$cdb->login($cdbConfig['adminUsername'], $cdbConfig['adminPassword']);

// create users
$uid = 'org.couchdb.user:Administrator';
$got = true;
$cdb->setDatabase('_users',false);
try { $call = $cdb->get($uid); } catch (Exception $e) { $got = false; }
if (!$got) {
    try {
        $rec = array (
            '_id' => $uid,
            'name' => 'Administrator', 
            'password' => (array_key_exists('AdministratorPassword',$_REQUEST) ? $_REQUEST['AdministratorPassword'] : 'Administrator'), 
            'realname' => 'nicerapp Administrator', 
            'email' => (array_key_exists('AdministratorEmail',$_REQUEST) ? $_REQUEST['AdministratorEmail'] : 'root@localhost'), 
            'roles' => [ "guests", "administrators", "editors" ], 
            'type' => "user"
        );
        $call = $cdb->post ($rec);
        if ($call->body->ok) echo 'Created Administrator user record.<br/>'; else echo '<span style="color:red">Could not create Administrator user record.</span><br/>';
    } catch (Exception $e) {
        echo '<pre style="color:red">'; var_dump ($e); echo '</pre>';
    }
} else {
    echo 'Already have an Administrator user record.<br/>';
}

$uid = 'org.couchdb.user:Guest';
$got = true;
$cdb->setDatabase('_users',false);
try { $call = $cdb->get($uid); } catch (Exception $e) { $got = false; }
if (!$got) {
    try {
        $rec = array (
            '_id' => $uid, 
            'name' => 'Guest', 
            'password' => 'Guest', 
            'realname' => 'nicerapp Guest', 
            'email' => 'guest@localhost', 
            'roles' => [ "guests" ], 
            'type' => "user"
        );
        $call = $cdb->post ($rec);
        if ($call->body->ok) echo 'Created Guest user record.<br/>'; else echo '<span style="color:red">Could not create Guest user record.</span><br/>';
    } catch (Exception $e) {
        echo '<pre style="color:red">'; var_dump ($e); echo '</pre>';
    }
} else {
    echo 'Already have a Guest user record.<br/>';
}


$cdb->setDatabase('zoned_at___users',true);
$json = '{ "admins": { "names": [], "roles": ["guests"] }, "members": { "names": ["Administrator"], "roles": ["guests"] } }';
try { 
    $call = $cdb->setSecurity ($json);
} catch (Exception $e) {
    echo '<pre style="color:red">'; var_dump ($e); echo '</pre>'; die();
}
echo 'Created database zoned_at___users<br/>';


$cdb->setDatabase('zoned_at___urls',true);
$json = '{ "admins": { "names": [], "roles": ["guests"] }, "members": { "names": ["Administrator"], "roles": ["guests"] } }';
try { 
    $call = $cdb->setSecurity ($json);
} catch (Exception $e) {
    echo '<pre style="color:red">'; var_dump ($e); echo '</pre>'; die();
}
echo 'Created database zoned_at___urls<br/>';

$cdb->setDatabase('zoned_at___logs',true);
$json = '{ "admins": { "names": [], "roles": ["guests"] }, "members": { "names": ["Administrator"], "roles": ["guests"] } }';
try { 
    $call = $cdb->setSecurity ($json);
} catch (Exception $e) {
    echo '<pre style="color:red">'; var_dump ($e); echo '</pre>'; die();
}
echo 'Created database zoned_at___logs<br/>';

/*
$docs = array (
    array (
        '_id' => 'id0001',
        'destination' => 'https://candyland.com/blabla',
        'shortened' => 'def'
    ),
    array (
        '_id' => 'id0002',
        'destination' => 'https://youtube.com/',
        'shortened' => 'jkl'
    )
);

foreach ($docs as $idx => $doc) {
    try {
        echo '<pre style="color:orange;">'; var_dump($cdb->post($doc)->body); echo '</pre>';
    } catch (SagCouchException $e) {
        echo 'record '.$idx.' already exists<br/>';
    }
}*/

/*
$cmdSetIndex = array (
    'index' => array ( 'fields' => array ('shortened') ),
    'name' => 'shortened-index',
    'ddoc' => 'shortened-index',
    'type' => 'json'
);
$call = $cdb->setIndex ($cmdSetIndex);
try {
echo '<pre style="color:orange;">'; var_dump($call->body); echo '</pre>';
} catch (SagCouchException $e) {
echo 'index already exists<br/>';
};*/

/*
$cmdFind = array (
    'selector' => array ('shortened' => 'def'),
    'fields' => array ('destination', 'shortened'),
    'use_index' => 'shortened-index'
);
$call = $cdb->find ($cmdFind);
echo '<pre style="color:orange;">'; var_dump($call->body); echo '</pre>';
*/
?>
