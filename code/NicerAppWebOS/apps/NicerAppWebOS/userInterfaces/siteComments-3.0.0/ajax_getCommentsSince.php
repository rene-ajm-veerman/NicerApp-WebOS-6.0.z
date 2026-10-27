<?php
require_once (__DIR__.'/../../../../boot.php');
global $naWebOS;

$naWebOS->comments->mode = 'ajax-PHP-Apache-nginx';

$markSeen = !empty($_POST['markSeen']);
$limit    = isset($_POST['limit']) ? (int)$_POST['limit'] : 200;

echo $naWebOS->comments->getCommentsSince(null, [
    'asHtml'   => true,
    'markSeen' => $markSeen,
    'limit'    => $limit
]);
