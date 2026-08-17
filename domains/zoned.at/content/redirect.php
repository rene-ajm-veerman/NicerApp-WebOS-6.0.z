        <div class="flex-title">
            <h1>zoned.at url link shortener</h1>
            <h2>soon with more features!</h2>
            <h2 id="redirectOrNot">Redirecting you soon</h2>
        </div>
<?php
require_once (dirname(__FILE__).'/../php-couchdb/boot.php');
    
$date = new DateTime();
$ip = (array_key_exists('X-Forwarded-For',apache_request_headers())?apache_request_headers()['X-Forwarded-For'] : $_SERVER['REMOTE_ADDR']);
$codeLocation = realpath(__FILE__);

$serverSettings = array (
    'http' => 'http://',
    'domain' => '192.168.178.29',
    'port' => 5984,
    'adminUsername' => 'nicer_app___Rene_AJM_Veerman',
    'adminPassword' => 'dontGoBitingMee,Pups'
);
$server = new couchdb_server ($serverSettings, $codeLocation);
if (!cdb_processResults ($server, $codeLocation, is_object($server) && !is_null($server->address))) {
    if ($calledFromApache) {
        echo PHP_EOL.'<br/><h2>Cannot connect to server, invalid $serverSettings</h2><br/><pre>'.PHP_EOL; var_dump ($serverSettings); echo '</pre>';
    } else {
        echo PHP_EOL.'Cannot connect to server, invalid $serverSettings'.PHP_EOL;
        var_dump ($serverSettings);
    }
    die();
}

$dbName = 'zoned_at___urls';
$dbSettings = array (
    'server' => $server,
    'dbName' => $dbName,
    'createIfNotExists' => true
);
$db = $server->connectToDB ($dbSettings, $codeLocation); // this call will succeed regardless whether or not the database already exists.
if (cdb_processResults ($db, $codeLocation, is_object($db) && $db instanceof couchdb_database)) {

    $findCommand = array (
        'server' => $server,
        'dbName' => $dbSettings['dbName'],
        '_find' => array(
            'selector' => array(
                'shortened' => $_GET['redirect']
            ),
            'fields' => array(
                '_id', 
                'creator', 'destination', 'shortened', 
                'timespanMin', 'timespanSec', 'manualRedirect', 
                'displayCount', 'displayCounts', 
                'redirectionCount', 'redirectionCounts'
            ),
            'use_index' => 'shortened-index'
        )
    );
    $doc = $db->find ($findCommand);
    $d = $doc['docs'][0];
    
    //echo '<pre style="color:white;">'; var_dump ($doc);echo '</pre>';die();
    if (cdb_processResults ($doc, $codeLocation, 
        is_array($doc) 
        && array_key_exists('docs', $doc)
        && is_array($doc['docs'])
        && count($doc['docs']) === 1
    )) {

        // increase redirection counter by one
        $docSettings = array (
            'server' => $server,
            'dbName' => $dbSettings['dbName'],
            'id' => $d['_id']
        );
        $docFromDB = $db->getDoc ($docSettings);

        //echo '<pre>';var_dump ($docFromDB);echo '</pre>';

        
        if (!array_key_exists('timespanMin', $d) || !array_key_exists('timespanSec', $d)) {
            $d['timespanMin'] = 0;
            $d['timespanSec'] = 10;
        }
        if (
            $d['timespanMin'] === 0
            && $d['timespanSec'] < 10
        ) $d['timespanSec'] = 10;
        
        $date = new DateTime();
        $docSettings = array (
            'server' => $server,
            'dbName' => $dbSettings['dbName'],
            'id' => $docFromDB['_id'],
            '_rev' => $docFromDB['_rev'],
            'data' => array (
                '_id' => $docFromDB['_id'],
                '_rev' => $docFromDB['_rev'],
                'creator' => $docFromDB['creator'],
                'destination' => $docFromDB['destination'],
                'shortened' => $docFromDB['shortened'],
                'timespanMin' => $docFromDB['timespanMin'],
                'timespanSec' => $docFromDB['timespanSec'],
                'manualRedirect' => $docFromDB['manualRedirect'],
                'displayCount' => $docFromDB['displayCount'],
                'displayCounts' => $docFromDB['displayCounts'],
                'redirectionCount' => $docFromDB['redirectionCount'],
                'redirectionCounts' => $docFromDB['redirectionCounts']
            )
            
        );
        if (!array_key_exists('displayCount', $docSettings['data'])) {
            $docSettings['data']['displayCount'] = 0;
        }
        if (
            !array_key_exists('displayCounts', $docSettings['data'])
            || !is_array($docSettings['data']['displayCounts'])
        ) {
            $docSettings['data']['displayCounts'] = array ();
        }
        if (
            array_key_exists('data', $docSettings)
            && is_array($docSettings['data'])
            && array_key_exists('displayCounts', $docSettings['data'])
            && is_array($docSettings['data']['displayCounts'])
            && !array_key_exists($date->format('Y-m-d'), $docSettings['data']['displayCounts'])
        ) {
            $docSettings['data']['displayCounts'][$date->format('Y-m-d')] = 0;
        }
        if (
            !array_key_exists('redirectionCount', $docSettings['data'])
            || !$docSettings['data']['redirectionCount']
        ) {
            $docSettings['data']['redirectionCount'] = 0;
        }
        if (
            !array_key_exists('redirectionCounts', $docSettings['data'])
            || !is_array($docSettings['data']['redirectionCounts'])
        ) {
            $docSettings['data']['redirectionCounts'] = array ();
        }
        if (
            array_key_exists('data', $docSettings)
            && is_array($docSettings['data'])
            && array_key_exists('redirectionCounts', $docSettings['data'])
            && is_array($docSettings['data']['redirectionCounts'])
            && !array_key_exists($date->format('Y-m-d'), $docSettings['data']['redirectionCounts'])
        ) {
            $docSettings['data']['redirectionCounts'][$date->format('Y-m-d')] = 0;
        }
        $docSettings['data']['displayCount']++;
        $docSettings['data']['displayCounts'][$date->format('Y-m-d')]++;
        //echo '<pre>';var_dump ($docSettings);echo '</pre>';
        $docUpdated = $db->updateDoc ($docSettings);
        //echo '<pre>';var_dump ($docUpdated);echo '</pre>';

        // spit out redirection HTML
        ?>
            <div class="shortened"><?php echo $d['shortened'];?></div>
        
            <div class="to">
            <span class="destination">To : </span><span class="destinationAddress"><?php echo $d['destination'];?></span><br/>
            <p id="countdown">
            <span id="countdownLabelIn">In</span> <span id="countdownTimespanMin"><?php echo array_key_exists('timespanMin', $d) ? $d['timespanMin'] : 0;?></span> <span id="countdownLabelTimespanMin">minutes</span><span id="countdownLabelComma">,</span> <span id="countdownTimespanSec"><?php echo array_key_exists('timespanSec',$d) ? $d['timespanSec'] : 10;?></span> <span id="countdownLabelTimespanSec">seconds</span><br/>
            </p>
            </div>
            <div id="manualRedirectSetting"><?php echo !array_key_exists('manualRedirect', $d) || $d['manualRedirect']===false ? 'true' : 'false';?></div>
            
        <?php 
        if ($ip===$docFromDB['creator']) {
        //if (true) {
        ?>
            <div class="displayedRedirected">
            <span class="info">Only you (IP <?php echo $ip;?>) can see these statistics :</span><br/>
            <span class="label">Displayed :</span><pre class="displayCounts"><?php echo json_encode($docSettings['data']['displayCounts'], JSON_PRETTY_PRINT);?></pre>
            <span class="label">Displayed total : </span><span class="displayCount"><?php echo $docSettings['data']['displayCount'];?></span><br/>
            <br/>
            <span class="label">Redirected : </span><pre class="redirectionCounts"><?php echo json_encode($docSettings['data']['redirectionCounts'], JSON_PRETTY_PRINT);?></pre>
            <span class="label">Redirected total : </span><span class="redirectionCount"><?php echo $docSettings['data']['redirectionCount'];?></span><br/>
            </div>
        <?php
        }
        ?>
            <div class="buttonHolder">
            <button id="btnCancelRedirect" onclick="zat.cancelRedirect()">
                <span>Cancel redirect</span>
            </button>
            <button id="btnRedirectNow" onclick="zat.redirectNow()">
                <span>Redirect now</span>
            </button>
            </div>
            <div class="buttonHolder">
            <button id="btnRedirectAnother" onclick="zat.gotoFrontpage()">
                <span>Redirect another URL</span>
            </button>
            </div>
        <?php
    } else {
        // no docs found in db
        ?>
            <div class="error">No redirection data found in database for that shortened URL</div>
            <div class="buttonHolder">
            <button id="btnRedirectAnother" onclick="zat.gotoFrontpage()">
                <span>Redirect another URL</span>
            </button>
            </div>
        <?php 
    }

}

?>
