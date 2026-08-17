        <script type="text/javascript">
            na.m.waitForCondition('forums js loaded', function() { return na && na.site && na.m.desktopIdle() && typeof naForums =='object'; }, function() { naForums.onload(); }, 100);
        </script>
<?php
    require_once (realpath(dirname(__FILE__).'/../../../../..').'/boot.php');
    global $naWebOS;
    //$naWebOS->init();
    $view = $naWebOS->view;
    //echo '<pre>'; var_dump ($view); die();
    
    
    $fn = 'pageGenerators/error_app.dialog.siteContent.php';
    $k = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/forums';
    if (
        array_key_exists($k, $view)
        && array_key_exists('page', $view[$k])
        && is_string($view[$k]['page'])
    ) switch ($view[$k]['page']) {
        case 'configure_admin':
            $fn = 'pageGenerators/configure_admin.php';
            break;
        case 'configure_user':
            $fn = 'pageGenerators/configure_user.php';
            break;
        case 'index':
            $fn = 'pageGenerators/view_index.php';
            break;
        case 'subforum':
            $fn = 'pageGenerators/view_forum.php';
            break;
        case 'thread':
            $fn = 'pageGenerators/view_thread.php';
            break;
        default:
            break;
    }
    echo execPHP(dirname(__FILE__).'/'.$fn, false);
?>
