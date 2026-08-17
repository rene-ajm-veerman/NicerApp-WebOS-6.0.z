<?php 
    if (array_key_exists('apps',$_GET)) {
        $view = $_GET['apps'];
        $json = json_decode(decode_base64_url ($view), true);
    } elseif (array_key_exists('url',$_GET)) {
        $view = str_replace('/apps/','',$_GET['url']);
        $json = json_decode(decode_base64_url($view), true);
    } else {
        echo '<pre style="color:red;">'; var_dump ($_GET); echo '</pre><br/>';
    }
    //echo '<pre style="color:lime;">'; var_dump ($_GET); echo '</pre><br/>';
    //echo '<pre style="color:yellow;">'; var_dump ($json); echo '</pre><br/>';
    echo '<center><a href="javascript:window.top.na.s.c.back();"><img src="'.$json['photo']['fileURL'].'" style="max-width:97%;max-height:97%;"/></a></center>';
?>
