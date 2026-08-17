<?php

function saAgenda__initialize($userViewed) {
	$userDir = SA_SITE_HD.'apps/user/appData/users/'.$userViewed.'/';
	$templateDir = SA_SITE_HD.'siteData/templates/seductiveapps/com/ui/agenda/';
	
	if (!file_exists($userDir.'agenda.json')) {
		//var_dump ($templateDir.'saAgenda.json');
		$r = copy ($templateDir.'saAgenda.json', $userDir.'agenda.json')
		//var_dump($r);
	}
	
}
?>