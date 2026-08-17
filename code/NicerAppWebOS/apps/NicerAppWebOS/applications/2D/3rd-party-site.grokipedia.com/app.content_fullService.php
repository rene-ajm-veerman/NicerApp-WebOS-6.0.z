<?php
require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php');

//echo '<pre>'; var_dump ($_REQUEST); die();
    
global $naWebOS;
$view = $naWebOS->view;
global $wiki_url;
if (array_key_exists('app-grokipedia_com', $_GET)) {
    $wiki_url = 'https://en.wikipedia.org/wiki/'.urlencode($_GET['app-grokipedia_com']);

    $slug = 'PHP_(programming_language)';
    $url  = 'https://grokipedia.com/page/' . rawurlencode($slug);

    $html = file_get_contents($url);  // or use curl with a proper User-Agent
    echo '<div id="rawGrokData" style="background:blue;color:white;text-shadow:0px 0px 3px black, 2px 2px 3px black">'.$html.'</div>';

    // Then parse with DOMDocument / DOMXPath
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    // Extract main article body — inspect actual selectors on the live site
    $nodes = $xpath->query('//article | //div[@class="content"]');

    echo '<pre style="background:blue;color:white;text-shadow:0px 0px 3px black, 2px 2px 3px black">'; var_dump ($nodes); echo '</pre>';
} else {
    $wiki_url = 'https://www.wikipedia.org/search-redirect.php';


    $slug = 'PHP';
    $url  = 'https://grokipedia.com/page/' . rawurlencode($slug);
    $url  = 'https://grokipedia.com';

    try {
        $html = file_get_contents($url);  // or use curl with a proper User-Agent
        $html = preg_replace('/<svg.*?\/svg>/i','',$html);
        echo '<div id="rawGrokData" style="color:white;text-shadow:0px 0px 3px black, 2px 2px 3px black">'.$html.'</div>';
    } catch (Exception $e) {
        global $naLAN;
        if ($naLAN)
            echo '<div id="rawGrokData" style="background:red;color:orange;text-shadow:0px 0px 3px black, 2px 2px 3px black">Fatal Error retrieving "'.$url.'" : '.$e->getMessage().'<br/><pre>'.$e->getTraceAsString().'</pre></div>';
        else
            echo '<div id="rawGrokData" style="background:red;color:orange;text-shadow:0px 0px 3px black, 2px 2px 3px black">Fatal Error retrieving "'.$url.'" : '.$e->getMessage().'</div>';
    }

    // Then parse with DOMDocument / DOMXPath
    //$dom = new DOMDocument();
    //@$dom->loadHTML($html);
    //$xpath = new DOMXPath($dom);

    // Extract main article body — inspect actual selectors on the live site
    //$nodes = $xpath->query('//article | //div[@class="content"]');

    //echo '<pre style="background:blue;color:white;text-shadow:0px 0px 3px black, 2px 2px 3px black">'; var_dump ($nodes); echo '</pre>';

}

?>
