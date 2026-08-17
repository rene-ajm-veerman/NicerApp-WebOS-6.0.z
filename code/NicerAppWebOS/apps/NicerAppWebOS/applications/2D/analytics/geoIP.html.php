<?php 
//namespace Foo;
//require_once realpath(dirname(__FILE__).'/../../../../').'/NicerAppWebOS/3rd-party/geoLite2/vendor/autoload.php';
//use GeoIp2\Database\Reader;
session_start();

if (!array_key_exists('geoIP',$_SESSION)) $_SESSION['geoIP'] = array();
//if (!array_key_exists($_GET['IP'],$_SESSION['geoIP'])) {
    //$reader = new Reader(realpath(dirname(__FILE__).'/../../../../').'/NicerAppWebOS/3rd-party/geoLite2/GeoLite2-City.mmdb');
    //$record = $reader->city($_GET['IP']);
    $apiKey = trim(file_get_contents(dirname(__FILE__).'/apiKey.ipinfo.io.txt'));
     $xec = 'curl -H "X-Forwarded-For: '.$_GET['IP'].'" ipinfo.io/'.$_GET['IP'].'?token='.$apiKey;
    exec ($xec, $output, $result);
//echo '<pre>'; var_dump ($xec); var_dump ($output); die();
    $_SESSION['geoIP'][$_GET['IP']] = json_decode(join('',$output), true);
//};

$record = $_SESSION['geoIP'][$_GET['IP']];
//
//echo '<pre>'; var_dump ($_GET); var_dump ($record); die();
$html = 
    '<table>'
        //.'<tr><th>Continent</th><td>'.$record['continent_name'].'</td></tr>'
        .'<tr><th>Country</th><td>'.$record['country'].'</td></tr>'
        .'<tr><th>Province</th><td>'.$record['region'].'</td></tr>'
        .'<tr><th>City</th><td>'.$record['city'].'</td></tr>'
        .'<tr><th>ISP</th><td>'.$record['hostname'].'</td></tr>'
    .'</table>';

echo $html;
?>
