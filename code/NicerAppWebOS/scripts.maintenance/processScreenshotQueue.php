<?php
require_once __DIR__ . '/../boot.php';   // or however you load NicerApp
require_once __DIR__ . '/../businessLogic/class.screenshots.php';

global $naWebOS;
$uDB2 = $naWebOS->dbsAdmin;
$manager = new naScreenshots($uDB2);
$report = $manager->createDatabaseAndIndexes();
print_r($report);
$comments = new class_naComments();
$report = $comments->enqueueScreenshotsFromAllComments([
    'retain' => 86400 * 14,   // 14 days
    'force'  => false,
    'limit'  => 3             // no limit
]);

print_r($report);

$workerId = 'worker-' . gethostname() . '-' . getmypid();
$manager->runWorker($workerId, maxJobs: 20, sleepSeconds: 1);

echo "Done.\n";
