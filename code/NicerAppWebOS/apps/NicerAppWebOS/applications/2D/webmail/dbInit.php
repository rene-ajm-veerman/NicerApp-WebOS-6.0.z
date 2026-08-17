<?php 
function nicer_app__webmail__dbInit ($naWebOS, $cdb, $security_admin, $security_user) {
    $dbName = str_replace('.','_',$naWebOS->domainFolder).'___webmail_accounts';
    $cdb->setDatabase ($dbName, true);
    $cdb->setSecurity ($security_user);
}
?>
