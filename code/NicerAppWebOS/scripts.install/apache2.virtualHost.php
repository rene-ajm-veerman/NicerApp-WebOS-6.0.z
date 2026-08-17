<?php
$rootPath_installer = realpath(dirname(__FILE__).'/../..');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$fn = $rootPath_installer.'/NicerAppWebOS/install.scripts/apache2.virtualHost.txt';
if (!file_exists($fn)) {
    trigger_error ('FATAL : "'.$fn.'" does not exist.', E_USER_ERROR);
};
if (!is_readable($fn)) {
    trigger_error ('FATAL : "'.$fn.'" is not readable.', E_USER_ERROR);
}
$c = file_get_contents($fn);
$c1 = str_replace ('SERVER_NAME', $argv[1], $c);
$c1 = str_replace ('APACHE_EMAIL', $argv[2], $c1);
$c1 = str_replace ('DOMAIN_TLD', $argv[3], $c1);

$fn2 = '/etc/apache2/sites-available/'.$argv[3].'.conf';
file_put_contents ($fn2, $c1);

$output = [];
$r = null;
$xec = 'a2ensite '.$argv[3].'.conf';
exec ($xec, $output, $r);
$dbg = [
    'xec' => $xec,
    'output' => $output
    'r' => $r
];
var_dump ($dbg);

$output = [];
$r = null;
$xec = 'service apache2 restart';
exec ($xec, $output, $r);
$dbg = [
    'xec' => $xec,
    'output' => $output
    'r' => $r
];
var_dump ($dbg);

?>
