<?php
    require_once (realpath(dirname(__FILE__).'/../../../..').'/NicerAppWebOS/boot.php');
    global $naWebOS;
    $naWebOS->init();
    $view = $naWebOS->view;
    //$dat = json_encode($view);
    switch ($naWebOS->view['app.2D.webmail.v1.0.0']['page']) {
        case 'index':
            $fn = 'index.php';
            break;
        case 'install' :
            $fn = 'install.php';
            break;
    }
    //echo dirname(__FILE__).'/../webmail-1.0.0/'.$fn; exit();
    $dat = execPHP(dirname(__FILE__).'/'.$fn, false);
    echo $dat;
?>
