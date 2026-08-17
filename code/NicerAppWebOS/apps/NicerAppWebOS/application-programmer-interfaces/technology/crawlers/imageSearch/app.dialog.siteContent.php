<?php 
require_once (dirname(__FILE__).'/boot.php');

$plugin = new wallpaperScraper_plugin_imagesGoogleCom();
$plugin->readDB();
$result = $plugin->getContent();
?>
<h1>Google Image Results</h1>

<?php echo $result; ?>
