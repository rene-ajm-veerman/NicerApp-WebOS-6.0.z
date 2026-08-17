<?php
require_once (realpath(dirname(__FILE__).'/../../../../../../').'/NicerAppWebOS/boot.php');
require_once (dirname(__FILE__).'/functions.php');
require_once (dirname(__FILE__).'/sources-list.php');
global $naWebOS;
//var_dump ($naWebOS->view);die();
foreach ($naWebOS->view as $viewPath => $viewRec) {
    $section = $viewRec['section'];
    $hl = '';
    if (
        strpos($section, 'News')!==false
        || strpos($section, 'Arabic')!==false
    ) {
        $op = 'on nicer.app';
    }
    if (strpos($section, 'Nieuws')!==false) {
        $op = 'op nicer.app';
    }
    if (strpos($section, 'Deutsche')!==false) {
        $op = 'auf nicer.app';
    }
    if (strpos($section, 'Headlines')===false) {
        $hl = ' headlines';
    }
    //var_dump ($section); die();

    $section2 = preg_replace('/.*__/','',$section);
    $section2 = preg_replace('/_/',' ',$section2);
}
echo $section2.$hl.' '.$op;
//die();
?>
