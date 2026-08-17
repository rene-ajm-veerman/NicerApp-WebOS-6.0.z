<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../../').'/';
require_once ($rootPathNA.'/boot.php');
//echo '<!--'; var_dump ($_GET); echo '-->';
    global $naWebOS;

    if (array_key_exists('specificityName', $_GET)) {
        $r = $naWebOS->getPageCSS(true, true, $_GET['includeClientOnlyThemes']==='true'?true:false, $_GET['stickToCurrentSpecificity']==='false'?false:true, $_GET['specificityName']);
    } else {
        $r = $naWebOS->getPageCSS(
            true, true,
            $_GET['includeClientOnlyThemes']==='true'?true:false, $_GET['stickToCurrentSpecificity']==='true'?true:false
        );
    }

    echo $r;
?>
