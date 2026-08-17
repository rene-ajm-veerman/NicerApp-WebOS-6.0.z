<?php
require_once(dirname(__FILE__).'/boot.php');

$api = new class_nicerappHumanLanguages_english_plugin_princetonWordnet();
$api->readRawData();

echo '<h1>Finding data for word "short" :</h1>'.PHP_EOL;
echo '<pre>'.json_encode($api->retrieveWordData('short'), JSON_PRETTY_PRINT).'</pre>'.PHP_EOL;

echo '<h1>Finding data for word "jejune" :</h1>'.PHP_EOL;
echo '<pre>'.json_encode($api->retrieveWordData('jejune'), JSON_PRETTY_PRINT).'</pre>'.PHP_EOL;

echo '<h1>Finding data for word "tall" :</h1>'.PHP_EOL;
echo '<pre>'.json_encode($api->retrieveWordData('tall'), JSON_PRETTY_PRINT).'</pre>'.PHP_EOL;

echo '<h1>Finding data for word "work" :</h1>'.PHP_EOL;
echo '<pre>'.json_encode($api->retrieveWordData('work'), JSON_PRETTY_PRINT).'</pre>'.PHP_EOL;

echo '<h1>Finding data for word "slack" :</h1>'.PHP_EOL;
echo '<pre>'.json_encode($api->retrieveWordData('slack'), JSON_PRETTY_PRINT).'</pre>'.PHP_EOL;

?>
