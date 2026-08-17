<?php 

function naGetAppSettings__news () {
    $r = array(
        'divs' => array(
            '#siteContent' => realpath(dirname(__FILE__)).'/newsApp.siteContent.php'
        )
    );
    
    return $r;
}

function foldMenuKey ($k) {
    $i = strtolower(substr($k,1));
    $i = str_replace('/', '-', $i);
    $i = str_replace('__', '-', $i);
    $i = str_replace('_', '-', $i);
    $i = str_replace(' - ', '.',$i);
    $i = str_replace(' ', '-',$i);
    //$r = strtolower('news-'.$i);

    return $i;
}



?>
