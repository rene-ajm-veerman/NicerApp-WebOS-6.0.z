<?php
require_once (dirname(__FILE__).'/../../../boot_stage_001a.php');

function saComments_getSubscription($subscriptionName) {
	while (ob_get_length()>0) ob_end_flush();
	ob_start();
	saComments_echoSubscription($subscriptionName);
	$r = ob_get_contents();
	ob_end_clean();
	return $r;
}

function saComments_echoSubscription ($subscriptionName) {
	//reportVariable ('stacktrace', debug_backtrace());
	//reportVariable ('$subscriptionName', $subscriptionName); die();
	$data = saComments_readSubscription($subscriptionName);
	//reportVariable ('$data', $data); die();
	$data['comments'] = array_reverse ($data['comments']);
	foreach ($data['comments'] as $idx => $comment) {
		saComments_echoEntry ($subscriptionName, count($data['comments']) - $idx - 1, $comment);
	}
}

function saComments_echoEntry ($subscriptionName, $idx, $comment) {
	if (!array_key_exists('deleted', $comment)) {
		$spacer = "\n\t";
		$dialogTheme = 'dialog_black_stoneWall_square';
		$dialog = 'vividDialog vividTheme__'.$dialogTheme.' ';
		echo '<div id="saComment_subscription_' . $subscriptionName . '_item_' . $idx . '" class="vdialog_relative '.($subscriptionName!=='all'?$dialog:'').' vividUninitialized saDontDoCSSinner saComment saComment_div saComment_subscription_' . $subscriptionName . '" style="visibility:'.($subscriptionName!=='all'?'hidden':'visible').';width:95%;height:auto;z-index:1000000">'.$spacer;
		echo '<table style="width:98%">'.$spacer;
		echo '<tr>'.$spacer;
		echo '<td><span class="saComment_from">' . $comment['from'] . '</span></td>'.$spacer;
		if (SA_VISITOR_IS_DEVELOPER || $_SERVER['REMOTE_ADDR']==$comment['ip']) echo '<td class="saComment_remove" rowspan="2" style="width:20px;height:20px;" onclick="sa.comments.removeComment(\'' . $subscriptionName . '\', ' . $idx . ');">&nbsp;</td>'.$spacer;
		echo '</tr><tr>'.$spacer;
		echo '<td class="saComment_when_display"><span class="saComment_when_displayToViewer"></span><span class="saComment_when" style="">' . $comment['when'] . '</span><span class="saComment_whenUTC" style="display:none;">'.$comment['whenUTC'].'</span></td>'.$spacer;
		echo '</tr><tr>'.$spacer;
		echo '<td colspan="2"><span class="saComment_comment">'. str_replace('<a ','<a rel="nofollow" ', $comment['comment']) . '</span></td>'.$spacer;
		echo '</tr></table>'.$spacer;	
		echo '</div>'.$spacer;
	}
}

function saComments_acceptNewComment ($s) {
	$sn = $s['subscription'];
	$entry = array (
		'from' => $s['from'],
		'ip' => $_SERVER['REMOTE_ADDR'],
		'when' => $s['when'],
		'whenGetTime' => $s['whenGetTime'],
		'whenTimezoneOffset' => $s['whenTimezoneOffset'],
		'whenUTCgetTime' => $s['whenUTCgetTime'],
		'whenUTC' => $s['whenUTC'],
		'comment' => stripslashes($s['comment'])
	);
	return $entry;
}

function saComments_addCommentToSubscription ($subscriptionName, $entry) {
	$data = saComments_readSubscription($subscriptionName);
	$data['comments'][] = $entry;
	//reportVariable ('$data', $data);
	saComments_writeSubscription ($subscriptionName, $data);	
	return count($data['comments'])-1;
}

function saComments_removeCommentFromSubscription ($subscriptionName, $idx) {
	$data = saComments_readSubscription($subscriptionName);
	if (SA_VISITOR_IS_DEVELOPER || $data['comments'][$idx]['ip']==$_SERVER['REMOTE_ADDR']) $data['comments'][$idx]['deleted'] = TRUE;
	saComments_writeSubscription ($subscriptionName, $data);	
}

function saComments_writeSubscription ($subscriptionName, $data) {
	file_put_contents (saComments_subscriptionPath ($subscriptionName), json_encode($data));
	//var_dump (file_exists(saComments_subscriptionPath ($subscriptionName)));
}

function saComments_readSubscription ($subscriptionName) {
	$data = NULL;
	if (file_exists(saComments_subscriptionPath($subscriptionName))) $data = json_decode (file_get_contents (saComments_subscriptionPath ($subscriptionName)),true);
	if (!is_array($data)) {
		$data = array (
			'comments' => array ()
		);
	};
	//var_dump (saComments_subscriptionPath ($subscriptionName));
	//var_dump ($data);
	return $data;
}

function saComments_subscriptionPath ($subscriptionName) {
	global $saSiteHTTP; global $saSiteDomain; global $saSiteRootFolder; global $saFrameworkFolder;
	global $saSiteHD; global $saFrameworkHD; global $saSiteURL; global $saFrameworkURL;
	global $saIsLocalhost; global $saHTDOCShd;
	global $saServerOperatingSystem; global $saDeveloperMode;
	
	global $saUpstreamRootURL; global $locationbarInfo;

	//return SA_SITE_HD.'/siteData/seductiveapps/com/ui/comments/saComments_subscription_'.$subscriptionName.'.json';
	$path = dirname(__FILE__).'/../../siteData/com/ui/comments/saComments_subscription_'.$subscriptionName.'.json';
	createDirectoryStructure (dirname($path));
	if ($saServerOperatingSystem!=='linux') $path = str_replace('/','\\',$path);
	return $path;
	return realpath ($path);
}
?>
