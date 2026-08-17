<?php 
$siteRootHD = dirname(__FILE__).'/'; global $siteRootHD;
$siteRootURL = 'http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/'; global $siteRootURL;

/* JUST DON'T! webappObfuscator = stand alone code.
$dfoLibURL = 'http://lib.localhost/'; global $dfoLibHD;
$dfoLibHD = dirname('/../../../../../lib.localhost'); global $dfoLibHD;
*/


$dfoPUBLIChd = dirname(__FILE__).'/public/'; global $dfoPUBLIChd;
$dfoSecretHD = dirname(__FILE__).'/secret-pw-JCn._-SA.LJ/'; global $dfoSecretHD;

$dfoPUBLICurl = $siteRootURL.'public/'; global $dfoPUBLICurl;
$dfoSecretURL = $siteRootURL.'secret-pw-JCn._-SA.LJ/'; global $dfoSecretURL;

$treatDevelopersAsOrdinaryViewers = false; global $treatDevelopersAsOrdinaryViewers;

$useUnicodeIdentifiers = false; global $useUnicodeIdentifiers;


$minTokenLength = 2; global $minTokenLength; // recommended u do not make this less than 2.
$randomStringJSO_length = ($useUnicodeIdentifiers ? 2 : 3); global $randomStringJSO_length; // $randomStringJSO_length = ($useUnicodeIdentifiers ? 2 : 3); are the bare-minimums folks.
//echo'<pre>111:'; var_dump($randomStringJSO_length);


$dfo_wo_pw = 'dku3-Ay54A_lk.jdDy'; global $dfo_wo_pw;
$sa_wo_pw = $dfo_wo_pw; global $sa_wo_pw;

$developerMode = ( // if TRUE, WILL SERVER UN-ENCRYPTED DEVELOPER SOURCES! USE WITH CARE!

	(
		!array_key_exists('wo_pw', $_GET) // being requested by webappObfuscator? don't go into developerMode!
	)
	&& (
		// option 1 : you can enable dev-mode by appending a parameter on the URL you call (?d=pw-AJZi3ljz. or &d=pw-AJZi3ljz.)
			// the last thing you want to do is buy every new internet device out there to test on it, 
			// so you end up going by phone stores and apple stores to test your wares.. 
			// (and don't bother asking apple staff if they got older models lying around to test
			// on, apple inc are a bunch of greedy assholes that will threaten not to let you in their store anymore 
			// if you do that, even if you've shown perfect ettiquette while testing your software in their store)..
		(
			array_key_exists('d', $_GET)
			&& $_GET['d']=='pw-AJZi3ljz.'
		)
		
		// option 2 : check by IP address.. do enter your home IP into this list here, so you get your own actual sources 
			// during normal development
		|| (
			!$treatDevelopersAsOrdinaryViewers
			&& (
				$_SERVER['REMOTE_ADDR']=='127.0.0.1'
				|| $_SERVER['REMOTE_ADDR']=='127.0.1.2'
				|| $_SERVER['REMOTE_ADDR']=='127.0.1.3'
				|| $_SERVER['REMOTE_ADDR']=='127.0.1.4'
				// || $_SERVER['REMOTE_ADDR']=='12.23.34.45'
			)
		)
	)
); 

//$developerMode = true;
global $developerMode;

?>