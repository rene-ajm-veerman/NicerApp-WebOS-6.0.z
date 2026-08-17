<?php
require_once (dirname(__FILE__).'/../../globals.php');
global $dfoPUBLICurl;
global $dfoPUBLIChd;
/*
	<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $dfoPUBLICurl?>siteLogic/get_css_siteTemplate_unobfuscatable.php">
	<script type="text/javascript" src="<?php echo $dfoPUBLICurl?>siteLogic/get_javascripts_siteTemplate-unobfuscatable.php"></script>
	<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $dfoPUBLICurl?>siteLogic/get_css_siteTemplate.php">
	<script type="text/javascript" src="<?php echo $dfoPUBLICurl?>siteLogic/get_javascripts_siteTemplate.php"></script>
	<script type="text/javascript" src="<?php echo $dfoPUBLICurl?>siteLogic/get_javascripts_settings.php"></script>
*/
/*
	<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $dfoPUBLICurl?>webappObfuscator__output/css/siteTemplate.css">
	<script type="text/javascript" src="<?php echo $dfoPUBLICurl?>webappObfuscator__output/javascript/siteLogic/siteCode.source.js"></script>

*/
?>
	<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $dfoPUBLICurl?>siteLogic/get_css_siteTemplate.php"/>
	<?php 
	    if (false) {
		require($dfoPUBLIChd.'siteLogic/get_css_siteTemplate_unobfuscatable.php');
		require($dfoPUBLIChd.'siteLogic/get_javascripts_siteTemplate-unobfuscatable.php'); 
	    } else {
	?>
	    <link type="text/css" rel="StyleSheet" media="screen" href="<?php echo $dfoPUBLICurl?>siteLogic/get_css_siteTemplate_unobfuscatable.php"/>
	    <script type="text/javascript" src="<?php echo $dfoPUBLICurl?>siteLogic/get_javascripts_siteTemplate-unobfuscatable.php"></script>
	
	<?php }; ?>
	
	<script type="text/javascript" src="<?php echo $dfoPUBLICurl?>siteLogic/get_javascripts_siteTemplate.php"></script>
	<script type="text/javascript" src="<?php echo $dfoPUBLICurl?>siteLogic/get_javascripts_settings.php"></script>
      