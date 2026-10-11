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
    "Website" : {
        "groups" : [
            "Guests", "Users", "Moderators"
        ],
        "email" : "[NO EMAIL]",
        "password" : "vjfDJeuff883hak32shfFJEusjrFejsWEF48342ls3JefnveiFEQf0dej4832",
        "realname" : "<?php echo $naWebOS->domainFolder.' Guest user';?>"
    }
}
