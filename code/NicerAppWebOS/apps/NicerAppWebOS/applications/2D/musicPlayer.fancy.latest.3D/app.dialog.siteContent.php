<?php
$fn = realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/boot.php';
//var_dump ($fn); die();
require_once ($fn);
global $naWebOS; global $naLAN;
//$naWebOS->__construct2();



    $appFolder = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering';
    $rf = dirname(__FILE__).'/music/';
    //var_dump($rf); //die();

    $ffi = []; // file and folder index
    $views = [];

    /*
    if (file_exists(dirname(__FILE__).'/index.views.json') && (!array_key_exists('rc',$_GET) || $_GET['rc']!=='true')) {
        $views = json_decode(file_get_contents(dirname(__FILE__).'/index.views.json'),true);
    };
    */

$view = $naWebOS->view;
if (false) {
    echo '<pre style="color:lime;background:blue;">app.dialog.siteContent.php::';
    var_dump($appFolder); echo PHP_EOL;
    var_dump ($view); echo PHP_EOL;
    var_dump(is_array($view[$appFolder])); echo PHP_EOL;
    echo '</pre>';
};

if (is_array($view) && array_key_exists($appFolder, $view)) {
    //echo '<pre style="color:brown;">addsc::'; var_dump($view); echo '</pre>';

    if ($view[$appFolder]['set']=='index') {
        echo require_return (dirname(__FILE__).'/frontpage.php');
    } else {
        echo require_return (dirname(__FILE__).'/app.dialog.siteContent_seeAndPlayMusicFolder.php');
    }
} else {
        echo '<pre>addsc1::'; var_dump($view); echo '</pre>';
    /*if ($view['set']=='index')
        echo require_return (dirname(__FILE__).'/frontpage.php');
    else
        echo require_return (dirname(__FILE__).'/app.dialog.siteContent_seeAndPlayMusicFolder.php');
    */
}
die();
?>
