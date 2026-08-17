<?php
require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php');

//echo '<pre>'; var_dump ($_REQUEST); die();
    
global $naWebOS;
$view = $naWebOS->view;
global $wiki_url;
if (array_key_exists('app-wikipedia_org', $_GET)) {
    $wiki_url = 'https://en.wikipedia.org/wiki/'.urlencode($_GET['app-wikipedia_org']);
    //var_dump ($wiki_url); die();
    if (
        $wiki_url=='https://en.wikipedia.org/wiki/en.wikipedia.org/frontpage'
        || $wiki_url=='https://en.wikipedia.org/wiki/frontpage'
        || $wiki_url=='https://en.wikipedia.org/wiki/en.wikipedia.org'
    ) $wiki_url = 'https://www.wikipedia.org';
    if (
        strpos($_GET['app-wikipedia_org'], 'en.wikipedia.org/')!==false
    ) $wiki_url = 'https://en.wikipedia.org/wiki/'.urlencode(str_ireplace('wiki/','',str_ireplace('en.wikipedia.org/','',$_GET['app-wikipedia_org'])));
} else {
    $wiki_url = 'https://www.wikipedia.org/search-redirect.php';
}

global $naIP;
//var_dump ($xec);
//echo '<pre>'; var_dump ($_GET); echo '</pre>'; //die();
if (array_key_exists('search',$_GET)) $wiki_url .= '?search='.$_GET['search'];
if (array_key_exists('family',$_GET)) $wiki_url .= '&family='.$_GET['family'];
if (array_key_exists('language',$_GET)) $wiki_url .= '&language='.$_GET['language'];
$xec = 'curl -L '.$wiki_url.' -H "User-Agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36" -H "API-User-Agent: NicerAppWebOS/5.10.8 (https://nicer.app/wiki/frontpage)" -H "X-NA-Forwarded-For: '.$naIP.'"';
//var_dump ($xec); //die();

exec ($xec, $output, $result);
$output2 = join ("\n", $output);
//$output2 = preg_replace ('/\s+/', '  ', $output2); // essential for multi-line replaces
//echo $output2; die();
//

//$output2 = str_replace('<body class="', '<body class="vividScrollpane ', $output2);

//$output2 = preg_replace('/<html.*head>/','',$output2);
//$output2 = preg_replace('/<body.*content<\/a>/','',$output2);
//$output2 = preg_replace('/<\/body>/','', $output2);
//$output2 = preg_replace('/<\/html>/','', $output2);
$output2 = str_replace('url(portal/', 'url(https://www.wikipedia.org/wiki/portal/', $output2);
$output2 = str_replace('"www.wikipedia.org/wiki/', '"/wiki/', $output2);

preg_match_all('/<form (.*?)action="(.*?)"/', $output2, $m, PREG_PATTERN_ORDER);
//var_dump ($m); die();
foreach ($m[0] as $idx => $str) {
    //$rf = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/3rd-party-site.wikipedia.org/search_redirect.php';
    $rf =
        '/wiki-search/'.(array_key_exists('search',$_GET)?$_GET['search']:'frontpage')
        .'/'.(array_key_exists('title',$_GET)?$_GET['title']:'title')
        .'/'.(array_key_exists('family',$_GET)?$_GET['family']:'family');
    $r2 = '<form '.$m[1][$idx].' action="'.$rf.'"';
    $output2 = str_replace ($str, $r2, $output2);

}
//echo '<pre>';
//echo htmlentities($output2);
preg_match_all('/<img (.*?)src="(.*?)"/', $output2, $m, PREG_PATTERN_ORDER);
//var_dump ($m); die();
foreach ($m[0] as $idx => $str) {
  //  echo '$idx='.$idx.PHP_EOL;
    //var_dump (htmlentities($str));
    $replace = $m[2][$idx];
    //var_dump ($replace);
    if (substr($replace,0,2)=='//') $replaceFinal = 'https:'.$replace;
    elseif (substr($replace,0,1)=='/') $replaceFinal = substr($wiki_url,0,-1).$replace;

    $mangleMe1 = !(
        substr($replace,0,8)=='https://'
       || substr($replace,0,2)=='//'
        || substr($replace,0,1)=='/'
    );

    $rf = mangleURL ($replace, $mangleMe1);
    //var_dump (substr($replace,0,8));
    //var_dump ($mangleMe1);
    //var_dump ($rf);
    //$replaceFinal = $wiki_url.str_replace ('//', '/', $replace);
//    $replaceFinal = str_replace ('https://'.$wiki_url.'https://'.$wiki_url, 'https:', $replaceFinal);
    //$r1 = '<img '.$m[1][$idx].'src="'.$replace.'"'.$m[3][$idx].$m[4][$idx].'>';
    $r2 = '<img '.$m[1][$idx].' src="'.$rf.'"';
    //var_dump (strpos($output2, $str));
    //var_dump (htmlentities($r2));
    $output2 = str_replace ($str, $r2, $output2);
}
//die();


//$output2 = preg_replace('|<a(.*?)href="(.*?)"(.*?)</a>|U', '<a $1 href="http://192.168.178.29/wiki/$2"$3</a>', $output2);
preg_match_all('/<a (.*?)href="(.*?)"(.*?)>/', $output2, $m, PREG_PATTERN_ORDER);
//echo '<pre>';
//var_dump ($m); //die();
foreach ($m[0] as $idx => $str) {
    //var_dump ('$idx='.$idx);
    //var_dump (htmlentities($str));
    $replace = $m[2][$idx];
    //echo 't33;'; var_dump ($replace); echo PHP_EOL;
    $rf = mangleURL($replace);
    //var_dump ($rf);//die();
    if (strpos($rf,'#')===false) {
        $rf = preg_replace('/https:\/\/nicer.app\/wiki\/.*?/','',$rf);
        $rf = preg_replace('/https:\/\/said.by\/wiki\/.*?/','',$rf);
        $rf = str_replace ("www.wikipedia.org/wiki/", '', $rf);
        $replaceFinal = 'javascript:window.top.na.site.loadContent(event, \'/wiki/'.$rf.'\');';
    } else {
        $replaceFinal = 'javascript:window.top.na.site.scrollContent(event, \''.$rf.'\');';
    };
    //var_dump ($replaceFinal);
    //var_dump (substr($replace,0,8)=='https://');

   // $r1 = '<a '.$m[1][$idx].'href="'.$replace.'"'.$m[3][$idx].'>'.$m[4][$idx].'</a>';
    $r2 = '<a '.$m[1][$idx].' class="contentSectionTitle3_a" href="'.$replaceFinal.'"'.$m[3][$idx].'><span class="contentSectionTitle3_span">';
    //var_dump (strpos($output2, $r1)).PHP_EOL;
    $output2 = str_replace ($str, $r2, $output2);
    //var_dump (htmlentities($replace)); echo PHP_EOL;
    //var_dump (htmlentities($r2)); echo PHP_EOL;
    //var_dump (htmlentities($r1)).PHP_EOL;
    //var_dump (htmlentities($r2)).PHP_EOL;
}
//die();

//echo '<pre>';
preg_match_all('/<link (.*?)href="(.*?)"/', $output2, $m, PREG_PATTERN_ORDER);
foreach ($m[0] as $idx => $str) {
    //echo '$idx='.$idx.PHP_EOL;

    $replace = $m[2][$idx];
    if (substr($replace,0,2)=='//') $replaceFinal = 'https:'.$replace;
    elseif (substr($replace,0,1)=='/') $replaceFinal = substr($wiki_url,0,-1).$replace;
    $rf = mangleURL($replace, false);

    $r1 = '<link '.$m[1][$idx].' href="'.$rf.'"';

    $output2 = str_replace ($str, $r1, $output2);
}
//die();

preg_match_all('/<script (.*?)src="(.*?)"/', $output2, $m, PREG_PATTERN_ORDER);
//echo '<pre>';
foreach ($m[0] as $idx => $str) {
    //echo '$idx='.$idx.PHP_EOL;

    $replace = $m[2][$idx];
    if (substr($replace,0,2)=='//') $replaceFinal = 'https:'.$replace;
    elseif (substr($replace,0,1)=='/') $replaceFinal = substr($wiki_url,0,-1).$replace;
    else $replaceFinal = $wiki_url.'/'.$replace;
    $rf = mangleURL($replace, false);
    //var_dump ($replace);
    //var_dump ($replaceFinal);

    $r1 = '<script '.$m[1][$idx].' src="'.$rf.'"';

    $output2 = str_replace ($str, $r1, $output2);
}
//die();
$output2 = preg_replace('/\.mw\-parser\-output \#mp\-left \{.*?\}/','',$output2);

//echo '<pre>';
//die();
$output2 = str_replace('</body>', '<link type="text/css" rel="StyleSheet" href="https://'.$naWebOS->domainFolder.'/domainConfig/index.css"/><link type="text/css" rel="StyleSheet" href="https://'.$naWebOS->domainFolder.'/domainConfig/index.dark.css"/><link type="text/css" rel="StyleSheet" href="https://'.$naWebOS->domainFolder.'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/3rd-party-site.wikipedia.org/index.css"/></body>', $output2);


echo $output2;

function mangleURL ($replace, $mangleMe1=true) {
    global $wiki_url;
    //echo $wiki_url; die();
    //var_dump($_SERVER); die();
    $wiki_url_1 = str_replace('portal/','https:://www.wikipedia.org/wiki/portal/',$wiki_url);
    $wiki_url_1 = str_replace('https://','',$wiki_url_1);
    $wiki_url_1 = preg_replace('/\/.*/','',$wiki_url_1);
    if (substr($replace,0,2)=='//' && $mangleMe1) $rf = str_replace('//','',$replace);
    elseif (substr($replace,0,2)=='//' && !$mangleMe1) $rf = $replace;//str_replace('//','',$replace);
    elseif (substr($replace,0,8)=='https://' && $mangleMe1) $rf = str_replace('https://','',$replace);
    elseif (substr($replace,0,1)=='/' && $mangleMe1) $rf = 'https://'.$_SERVER['SERVER_NAME'].'/wiki/'.$wiki_url_1.$replace;
    elseif (substr($replace,0,1)=='/' && !$mangleMe1) $rf = '//'.parse_url($wiki_url, PHP_URL_HOST).$replace;
    elseif ($mangleMe1) $rf = '//'.parse_url($wiki_url, PHP_URL_HOST).'/'.$replace;
    else $rf = $replace;
    //echo PHP_EOL.'<pre>$replace='; var_dump ($replace); echo PHP_EOL.'$rf='; var_dump($rf); echo '</pre>'.PHP_EOL;
    return $rf;
}
?>
