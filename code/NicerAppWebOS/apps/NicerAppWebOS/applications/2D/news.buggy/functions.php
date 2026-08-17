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
        $i = str_replace(' - ', '.',$k);
        $i = str_replace(' ', '-',$i);
        $r = strtolower('news-'.$i);
        return $r;
    }

    function unfoldMenuKey ($fk) {
        $i = str_replace('news-','',$fk);
        $i = str_replace('-',' ', $i);
        $i = str_replace('.',' - ', $i);
        return $i;
    }



?>
