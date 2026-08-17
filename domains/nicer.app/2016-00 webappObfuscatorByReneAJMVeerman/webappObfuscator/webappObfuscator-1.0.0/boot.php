<?php 
//OBSOLETED require_once (dirname(__FILE__).'/webappObfuscator.globals.php');
require_once (dirname(__FILE__).'/webappObfuscator-1.0.0.php');

$webappObfuscator__factorySettings = array (
  'logLevel' => 500,
  'filepath' => dirname(__FILE__),
  'url' => 'http://seductiveapps.com/siteFramework__pw-e8xj2.K8-2jTx_zE37/20161102 0759/siteComponents/webappObfuscator/',
  'tokens' => array (
    'produceUnicode' => false, // can't be anything but false (browser limitations in 2015)
    'randomStringJSO_length' => 3, // dont lower this lower than 3
    'ignorelist' => $wo__tokens__ignoreList,
    'ignoreListAllLowercase' => $wo__tokens__ignoreList__allLowercase,
    'minimumTokenLength' => 2,
    'tokenBoundary' => '\b',
    'regxBoundary' => '#',
    'searchTokenSpecialChars' => array ( '(', ')', '[', ']', '#', '?', '&', '*', '.', '+' ),
    'replaceTokenSpecialChars' => array ( '\(', '\)', '\[', '\]', '\#', '\?', '\&', '\*', '\.', '\+' )
  )
);
global $webappObfuscator__factorySettings;
global $webappObfuscator;
$webappObfuscator = new webappObfuscator ($webappObfuscator__factorySettings);
global $webappObfuscator;



$woWebsite__factorySettings = array (
  // unused atm
);
global $woWebsite__factorySettings;
global $woWebsite;
$woWebsite = new woWebsite ($woWebsite__factorySettings);
global $woWebsite;

?>
