<?php 
    require_once (dirname(__FILE__).'/functions.php');
    
      echo webmail_get_mail_headers (
        $_POST['serverConfig'], 
        $_POST['serverIdx'], 
        $_POST['mailboxes'], 
        $_POST['mailboxIdx'], 
        $_POST['mailIdx']
    );
?>
