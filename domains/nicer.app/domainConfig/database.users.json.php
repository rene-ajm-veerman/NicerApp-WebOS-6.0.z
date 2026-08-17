<?php
global $na_full_init;
$na_full_init = false;
//require_once(dirname(__FILE__).'/../../boot.php');
global $naWebOS;
//$db = $naWebOS->dbs->findConnection('couchdb');
//$cdb = $db->cdb;
//$dn = $naWebOS->domainFolderForDB;
?>
{
    "<?php echo $naWebOS->ownerInfo['OWNER_NAME'];?>" : {
        "groups" : [
            "Owners",
            "Chief Officers",
            "Guests",
            "Users"
        ],
        "email" : "<?php echo $naWebOS->ownerInfo['OWNER_EMAIL'];?>",
        "password" : "<?php echo $naWebOS->ownerInfo['OWNER_PASSWORD'];?>",
        "realname" : "<?php echo $naWebOS->domainFolder.' Owner';?>"
    },
    "Guest" : {
        "groups" : [
            "Guests", "Users"
        ],
        "email" : "[NO EMAIL]",
        "password" : "Guest",
        "realname" : "<?php echo $naWebOS->domainFolder.' Guest user';?>"
    },
    "timo" : {
        "groups" : [
            "Guests", "Users"
        ],
        "email" : "1871a09375@sinenomine.email",
        "password" : "fordtimelord",
        "realname" : "<?php echo $naWebOS->domainFolder.' Guest user';?>"
    }
}
