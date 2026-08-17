<?php
require_once (realpath(dirname(__FILE__).'/../').'/boot.php');
global $naDebugAll;
$debug_naWebOS_traceroute_data_gathering = $dbgDG = true; //$naDebugAll;
global $dbgDG;
global $naWebOS;

$fn = realpath(dirname(__FILE__)).'/naWebOS_traceroute_data_gathering.css';
$fnWeb = $naWebOS->adjustPath($fn);

//MAIN:
echo '<link type="text/css" rel="StyleSheet" href="'.$fnWeb.'?m='.$naWebOS->fileDateTimeStamp($fn).'"   >'.PHP_EOL;

echo naWebOS_gather_traceroute_data();



function naWebOS_gather_traceroute_data () {
    $cmd = [
        '{"HTML_className":"naWebOS-debug-outer-DIV"}' => 'naWebOS-debug-outer-DIV',
        '{"HTML_className":"naWebOS-field-name"}' => 'naWebOS-field-name',
        '{"HTML_className":"naWebOS-field-value"}' => 'naWebOS-field-value',
        '{"HTML_className":"naWebOS-debug-lineRemaining"}' => 'naWebOS-debug-lineRemaining',
        '{"value":"linesRemaining"}' => 'color:ivory;background:rgba(255,0,0,0.4);border-radius:5px;box-shadow:2px 2px 4px 3px rgba(0,0,0,0.555)',
        '{"color":"hostnamectl"}' => 'color:white;background:rgba(255,0,0,0.4);border-radius:10px;box-shadow:2px 2px 4px 3px rgba(0,0,0,0.555)',
        '{"color":"tracerouteVersion"}' => 'color:yellow;background:rgba(255,255,0,0.7);border-radius:5px;box-shadow:2px 2px 4px 3px rgba(0,0,0,0.555)',
        '{"color":"tracerouteResults"}' =>'color:white;font-weight:bold;background:rgba(0,0,255,0.4);border-radius:5px;box-shadow:2px 2px 4px 3px rgba(0,0,0,0.555)',
        '{"color":"tracerouteResultLine"}' => 'color:ivory;background:rgba(255,255,50,0.4);border-radius:5px;box-shadow:2px 2px 4px 3px rgba(0,0,0,0.555)'
    ];
    $cmd['{"colorSelection_outerDiv":"determinedInUpwardFunction"}'] = 'hostnamectl';

    $r = naWebOS_gather_desktop_OS_info($cmd);

    $cmd['{"colorSelection_outerDiv":"determinedInUpwardFunction"}'] = 'traceroute --version';
    $r .= naWebOS_gather_traceroute_version($cmd);

    $destinations = [
        'www.freedomnet.nl'/*,
        'google.nl',
        'google.nl',
        'gmail.com',
        'drive.google.com',
        'drive.google.nl',
        'images.google.com',
        'images.google.nl',
        'hotmail.com',
        'hotmail.nl',
        'bing.com',
        'bing.nl'*/
    ];
    foreach ($destinations as $destIdx => $destAddress) {
        $cmd['target'] = $destAddress;
        $cmd['{"colorSelection_outerDiv":"determinedInUpwardFunction"}'] = '0:tracerouteResults,N:tracerouteResultLine'; //CSV format for value
        $r .= naWebOS_gather_traceroute_data_for_VPN_outgoing_connections ($cmd);
    }
    return $r;
}

function naWebOS_gather_desktop_OS_info($cmd) {
    $xec = 'hostnamectl';
    exec ($xec, $output, $result_code);
    $di = [
        'mainPreClassName' => 'naWebOS_desktopos_info',
        'execString' => $xec,
        'output' => $output,
        'result_code' => $result_code
    ];
    $cmd['di'] = $di;
    $cmd['target'] = $xec;
    return naWebOS_output_debug_command ($cmd);

    /*
    $regEx = '/\s+\([a-z][A-Z[0-0]\):\s([a-z][A-Z[0-0]\)./)';
    $preg_match_result_code = preg_match ($regEx, $ouput, $matches, PREG_OFFSET_CAPTURE);
    $di = [
        '$regEx' => $regEx,
        'mode' => 'PREG_OFFSET_CAPTURE',
        '$matches' => $matches,
        '$preg_match_result_code' => $preg_match_result_code
    ];
    naWebOS_output_debug_info ($di);
    */
}

function naWebOS_gather_traceroute_version ($cmd=null) {
    $xec = 'traceroute --version';
    exec ($xec, $output, $result_code);
    $di = [
        'mainPreClassName' => 'naWebOS_traceroute_version',
        'execString' => $xec,
        'output' => $output,
        'result_code' => $result_code
    ];
    $cmd['di'] = $di;
    $cmd['target'] = $xec;
    return naWebOS_output_debug_command ($cmd);

    /*
    $expectedOutputs = [
        0    => [
            'traceroute (GNU inetutils) 2.5',
            'Copyright (C) 2023 Free Software Foundation, Inc.',
            'License GPLv3+: GNU GPL version 3 or later <https://gnu.org/licenses/gpl.html>.',
            'This is free software: you are free to change and redistribute it.',
            'There is NO WARRANTY, to the extent permitted by law.',
            '',
            'Written by Elian Gidoni.'
        ]
    ];

    $cr = false;
    foreach ($expectedOutputs as $k => $v) {
        if ($result_code === $k && $v == $output[0][$k]) $cr = true;
    }
    return $cr;
    */
}

function naWebOS_gather_traceroute_data_for_VPN_outgoing_connections ($cmd=null) {
    $fncn = 'naWebOS_gather_traceroute_data_for_VPN_outgoing_connections';
    if (!is_array($cmd)) trigger_error ($fncn.' : $cmd is not an [k=>v,k=>v,...] array.');
    $xec = 'traceroute '.$cmd['target'];
    exec ($xec, $output, $result_code);
    $di = [
        'idx' => 0,
        'execString' => $xec,
        'output' => $output,
        'result_code' => $result_code
    ];
    $cmd['di'] = $di;
    $r = '<h1>'.$output[0].'</h1>'.PHP_EOL.PHP_EOL;
    $r .= naWebOS_output_debug_command ($cmd);
/*
    $idx = 1;
    while ($idx >= 1 && array_key_exists($idx,$output)) {
        $line = $output[$idx];
        $line1a = $line;
        $tl = trim($line1a);
        $sectionComplete = false;

        while ($tl!='') {

            $line1a = preg_replace('/\s+\d+\s+/','',$line1a);

            $matches2 = [];
            while (is_array($matches2) && count($matches2)===0) {
                $regEx_linePartData = '/([\-\w\d\.]+)\s+(\([\w\d]+\.[\w\d]+\.[\w\d]+\.[\w\d]+\))/';
                $preg_match_result_code = preg_match ($regEx_linePartData, $line1a, $matches2, PREG_OFFSET_CAPTURE);
                if (is_array($matches2) && count($matches2)!==0) {
                    $line1a = str_replace($matches2[0][0],'',$line1a);
                    $tl = trim($line1a);
                    $line1b = $line1a;
                    $di = [
                        '$idx' => $idx,
                        '$idx2' => '[NULL]',
                        '$regEx_linePartMatch' => $matches2[0][0],
                        '$regEx_linePartData' => $regEx_linePartData,
                        '$mode' => 'PREG_OFFSET_CAPTURE',
                        '$preg_match_result_code' => $preg_match_result_code,
                        '$matches' => $matches2,
                        '$lineRemaining' => $tl
                    ];
                    $cmd['di'] = $di;
                    $r .= naWebOS_output_debug_info ($cmd);
                }

                flush();
                ob_flush();
                ob_end_flush();
                ob_end_clean();
                ob_start();

            }

            $line1a = $tl;
            $idx2 = 0;
            while ($tl!=='' && !$sectionComplete) {

                if ($tl==($idx).'  * * *') {
                    $r .= '<p>'.($idx).' * * * [UNREACHABLE]</p>';
                    $idx2++;
                    break;
                } else {
                    $matches3 = [];
                    $regEx_linePartMS = '/\s*(\d+[.,]\d+)\s+ms\s* /';
                    $preg_match_result_code = preg_match ($regEx_linePartMS, $line1a, $matches3, PREG_OFFSET_CAPTURE);
                    if (is_array($matches3) && count($matches3)!==0) {
                        $line1a = str_replace($matches3[0][0],'',$line1a);
                        $tl = trim($line1a);
                        $line1b = $line1a;
                        $di = [
                            '$idx' => $idx,
                            '$idx2' => $idx2,
                            '$regEx_linePartMatch' => $matches3[0][0],
                            '$regEx_linePartMS' => $regEx_linePartMS,
                            '$mode' => 'PREG_OFFSET_CAPTURE',
                            '$preg_match_result_code' => $preg_match_result_code,
                            '$matches' => $matches3,
                            '$lineRemaining' => $tl
                        ];
                        $cmd['di'] = $di;
                        $idx2++;
                        $r .= naWebOS_output_debug_info ($cmd);
                    } else { $sectionComplete = true; };
                }

                flush();
                ob_flush();
                ob_end_flush();
                ob_end_clean();
                ob_start();

            }
        }
    }
*/
    return $r;
}

function naWebOS_gather_traceroute_data_for_regular_outgoing_connections () {

}

function naWebOS_calculate_VPN_traceroute_averages_phase001 () {

}

function naWebOS_put_VPN_traceroute_activity_averages_into_DB_couchdb () {

}

function naWebOS_get_new_couchdb_IDs_for_VPN_traceroute_averages () {

}

?>
