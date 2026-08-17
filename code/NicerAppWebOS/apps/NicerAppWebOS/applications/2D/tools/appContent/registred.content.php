<?php 
require_once (dirname(__FILE__).'/../../../seductiveapps/com/core/registration4sa/boot.php');
global $seductiveapps_registration4sa;
$db = $seductiveapps_registration4sa['db'];

$ok = false;

$s = $db->get('seductiveapps/com/core/registration4sa/byIP', true);
foreach ($s as $ip => $regRecord) {
//	var_dump ($s);
//	var_dump ($regRecord);
	$reguri = $_SERVER['REQUEST_URI'];
//	var_dump ($reguri);
	$reguri = str_replace ('/content/tools/registred/', '', $reguri);
//	var_dump ($regRecord['token']);
//	var_dump ($reguri);
	if ($regRecord['token']===$reguri) {
		$ok = true;
		break;
	}
}
if ($ok) {
?>

<h2 id="headerDownload">Download</h2>
<script type="text/javascript">
var vividTextCmd = {
	el : jQuery('#headerDownload')[0],
	theme : sa.cg.themes.saColorgradientSchemeRed, 
	animationType : sa.vividText.globals.animationTypes[0],
	animationSpeed : 2500
};
sa.vividText.initElement (vividTextCmd);	

</script>

<p>
You are now ready to download the full sources for seductiveapps.com and blessedtarot.info (which serves as an example of how to have an app inside an iframe manipulate the main page's document.location.href without page refreshes)..
</p>

<?php $downloadLink = 'SeductiveApps.com web-ui framework full sources 2014-10-24 00-44 CEST.rar'; ?>
<p>
<a href="<?php echo SA_SITE_WEB?>downloads/<?php echo $downloadLink?>" class="nomod" onclick="sa.marketing.downloading('<?php echo $downloadLink?>', '<?php echo $regRecord['clientIP']?>');"><?php echo $downloadLink?></a>.
</p>
<?php
} else {
?>

<h2 id="headerDownload">Download</h2>
<script type="text/javascript">
var vividTextCmd = {
	el : jQuery('#headerDownload')[0],
	theme : sa.cg.themes.saColorgradientSchemeRed, 
	animationType : sa.vividText.globals.animationTypes[0],
	animationSpeed : 2500
};
sa.vividText.initElement (vividTextCmd);	

</script>

<p>
You must first <a href="<?php echo SA_SITE_WEB?>tools/registration">register, before you can download the full sources for seductiveapps.com and blessedtarot.info</a>.
</p>

<?php
}
?>