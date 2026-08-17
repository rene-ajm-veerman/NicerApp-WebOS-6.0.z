<?php
$_SERVER['SUBDOMAIN_DOCUMENT_ROOT'] = dirname(dirname(__FILE__));
$_SERVER['SERVER_NAME'] = 'edenscode.info'; // adjust this to your own server's name'
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['SERVER_SOFTWARE'] = 'win32'; // not really important for this script, added to prevent notices.
require_once (dirname(__FILE__).'/../../../../boot.php');
require_once (dirname(__FILE__).'/jv.php');


// Don't touch this section;'
$_GET['fresh'] = true;
set_time_limit(0);
ini_set ('memory_limit', '3000M');

$eol = "\n";
echo 'about to start'.$eol; flush(); ob_flush();

/*do_a_test(50);
do_a_test(150);
do_a_test(200);
do_a_test(300);
do_a_test(400);
do_a_test(500);
do_a_test(750);
do_a_test(1000);
do_a_test(1250);
do_a_test(1500);
do_a_test(2000);*/
do_a_test(1500);


function do_a_test ($memSize) {
	// Here you can specify your test data settings.
	$eol = "\n";
	$_GET['mem'] = $memSize; // how much memory to use, approximately
	$f = fopen ('jv_testdata_'.$memSize.'mb.json', 'w'); // what file to write to
	jsonViewer_selfTest ('p', 'q', array('height'=>'100%'), array('file'=>$f,'excludeSeperators'=>true));
	fclose ($f);
	echo 'done '.$memSize.'mb'.$eol; flush(); ob_flush();
}

