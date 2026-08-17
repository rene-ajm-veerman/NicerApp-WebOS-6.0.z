<?php 
$rootPathNA = realpath(dirname(__FILE__).'/../..').'/NicerAppWebOS';
require_once ($rootPathNA.'/boot.php');

global $naWebOS;

// do the changing of theme names as the couchdb admin user-id, meaning permissions won't matter one bit for this change.
$result = $naWebOS->dbsAdmin->findConnection('couchdb')->changeThemeName ($_POST['oldThemeName'], $_POST['newThemeName']);
if ($result) echo 'status : Success.'; else echo 'status : Failed.';




// needed or var data in success() of the ajax call in JS is going to fail, 
// meaning the browser user-interface won't be updated correctly with the new theme name in the #themes dropdown box..
// (my code editor always eventually sticks an extra empty line at the bottom of scripts like this .php script you're now looking at).
exit();
?>
