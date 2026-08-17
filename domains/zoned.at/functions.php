<?php 
function content ($file) {
    ob_flush();
    ob_end_clean();
    ob_start();
    require_once ($file);
    $r = ob_get_contents();
    ob_end_clean();
    ob_start();
    return $r;
}

function randomString ($length) {
    $seed = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $r = '';
    for ($i=0; $i<$length; $i++) {
        $r .= substr ($seed, rand(0,strlen($seed)), 1);
    };
    return $r;
}
?>
