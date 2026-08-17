<script type="text/javascript">
    na.m.waitForCondition('comments js loaded', function() { return na && na.site && na.site.settings.current.desktopIdle && typeof naForums =='object'; }, function() { naComments.onload(); }, 50);
</script>
<?php
    require_once (realpath(dirname(__FILE__).'/../../..').'/boot.php');
    global $naWebOS;
    //$naWebOS->init();
    $view = $naWebOS->view;
    
    
    $fn = 'pageGenerators/error_app.dialog.siteContent.php';
    if (
        array_key_exists('app.2D.commerceIndexPages', $view)
        && array_key_exists('page', $view['app.2D.commerceIndexPages'])
        && is_string($view['app.2D.commerceIndexPages']['page'])
    ) switch ($view['app.2D.commerceIndexPages']['page']) {
        case 'index':
            $fn = 'index.php';
            break;
        case 'install':
            $fn = 'install.php';
            break;
        case 'configure_admin':
            $fn = 'pageGenerators/configure_admin.php';
            break;
        case 'configure_user':
            $fn = 'pageGenerators/configure_user.php';
            break;
        case 'view_index':
            $fn = 'pageGenerators/view_index.php';
            break;
        case 'view_forum':
            $fn = 'pageGenerators/view_forum.php';
            break;
        case 'view_thread':
            $fn = 'pageGenerators/view_thread.php';
            break;
        default:
            break;
    }
    echo execPHP(dirname(__FILE__).'/'.$fn, false);
?>
