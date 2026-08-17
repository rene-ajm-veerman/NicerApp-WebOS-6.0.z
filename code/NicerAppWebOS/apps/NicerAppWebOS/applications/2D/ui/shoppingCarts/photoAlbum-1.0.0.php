<?php
require_once (dirname(__FILE__).'/../../../boot.php');
require_once (dirname(__FILE__).'/../../../com/cms/boot.php');

global $photoAlbums_globals;
$photoAlbums_globals = array (
	'pathAlbums' => SA_SITE_HD.'customer/niels/seductiveapps/com/ui/shoppingCarts/brochures/',
	'urlAlbums' => SA_SITE_WEB.'customer/niels/seductiveapps/com/ui/shoppingCarts/brochures/',
	'pathImageMagick' => 'C:\\Program Files\\ImageMagick-6.8.9-Q16\\',
	'lowres' => array (
		'width' => 250,
		'height' => 250		
	)
);

/*
set_time_limit (0);
$albumsList = shoppingCarts_getAlbumsListFromFilesystem();
$albumsNestedList = shoppingCarts_getAlbumsNestedListCalculated($albumsList);
$albumsContents = shoppingCarts_getAlbumsContentsFromFilesystem($albumsList);
shoppingCarts_resizeAlbums ($albumsContents);
*/

function shoppingCarts_getAlbumsListFromFilesystem() {
	global $photoAlbums_globals;
	
	$rootPath = $photoAlbums_globals['pathAlbums'];
	
	$folders = getFilePathList ($rootPath, true, '/(.*)/', array('dir'));
	
	$foldersUse = array();
	foreach ($folders as $idx=>$folder) {
		if (
			strpos($folder, 'inactive')===false
			&& strpos($folder, 'lowres')===false
			&& strpos($folder, '.rar')===false
		) {
			$foldersUse[] = str_replace($rootPath, '', substr($folder,0,strlen($folder)-1));
		}
	}
	
	return $foldersUse;
}

function shoppingCarts_getAlbumsNestedListCalculated($albumsList) {
	global $photoAlbums_globals;
	
	$albumsNestedList = array();
	foreach ($albumsList as $idx => $albumRelativePath) {
		$a = explode ('/', $albumRelativePath);
		$p = &$albumsNestedList;
		foreach ($a as $idx2 => $folder) {
			if (!array_key_exists($folder, $p)) $p[$folder] = array();
			$p = &$p[$folder];
		}
	}
	
	return $albumsNestedList;
}

function shoppingCarts_getAlbumsContentsFromFilesystem($albumsList) {
	global $photoAlbums_globals;

	$rootPath = $photoAlbums_globals['pathAlbums'];
	
	$albumsContent = array();
	foreach ($albumsList as $idx => $albumRelativePath) {
		$albumContent = getFilePathList ($rootPath.$albumRelativePath, false, '/(.*)/', array('file'));
		$albumsContent[$albumRelativePath] = array();
		foreach ($albumContent as $idx2 => $file) {
			//var_dump ($file);
			//var_dump ($rootPath.'/');
			if (
				strpos ($file, '.rar')===false
			) {
			
				$albumsContent[$albumRelativePath][] = str_replace($rootPath, '', $file);
			}
		}
	}
	
	
	return $albumsContent;
}

function shoppingCarts_resizeAlbums ($albumsContents) {
	global $photoAlbums_globals;

	$rootPath = $photoAlbums_globals['pathAlbums'];
	$im = $photoAlbums_globals['pathImageMagick'];
	$w = $photoAlbums_globals['lowres']['width'];
	$h = $photoAlbums_globals['lowres']['height'];
	
	unlink ($rootPath.'run.bat');
	
	foreach ($albumsContents as $albumRelativePath => $files) {
		createDirectoryStructure ($rootPath.$albumRelativePath.'/lowres');
		//echo $albumRelativePath;
		
		foreach ($files as $idx => $filename) {
			$original = $rootPath.$albumRelativePath.'/'.$filename;
			$pi = pathinfo($original);
			$original = $rootPath.$albumRelativePath.'/'.$pi['basename'];
			$lowres = $rootPath.$albumRelativePath.'/lowres/'.$pi['basename'];
			
			if (!file_exists($lowres)) {
				$cmd = '"'.$im.'convert.exe" "'.$original.'" -resize '.$w.'x'.$h.' "'.$lowres.'"';
				
				$f = fopen ($rootPath.'run.bat', 'a');
				fwrite ($f, $cmd."\r\n");
				fclose ($f);
			}
			
			//if (!file_exists($lowres)) safe_exec ($cmd);
		}
	}
}

function shoppingCarts_html_menu () {
	global $photoAlbums_globals;
	global $cms;

	$rootPath = $photoAlbums_globals['pathAlbums'];
	$albumsNestedList = $cms->db->get ('seductiveapps/com/ui/photoAlbum/albumsNestedList', true);
	$html = shoppingCarts_html_menu_recurse ($albumsNestedList, '');
	return $html;
}

function shoppingCarts_html_menu_recurse ($d, $p) {
	$html='';
	if (is_array($d) && count($d)>0) {
		foreach ($d as $n => $v) {
			$a = ($p!='' ?$p.'_'.$n :$n);
			if (!is_array($v)) {
				$html.='<li><a href="'.SA_SITE_WEB.'album/'.shoppingCarts_urlEncode($a).'">'.$n.'</a></li>';
			} else {
				$html.='<li><a href="'.SA_SITE_WEB.'album/'.shoppingCarts_urlEncode($a).'">'.$n.'</a>';
				$html.="\n<ul>\n\t";
				$html.= shoppingCarts_html_menu_recurse ($v, $a);
				$html.='</ul>'."\n";
			}
		}
	}
	return $html;
}

function shoppingCarts_html_viewAlbum ($album, $page) {
	global $photoAlbums_globals;
	global $cms;

	$rootURL = $photoAlbums_globals['urlAlbums'];
	$w = $photoAlbums_globals['lowres']['width'];
	$h = $photoAlbums_globals['lowres']['height'];
	
	$albumRelativePath = shoppingCarts_urlDecode ($album);
?>
	<script type="text/javascript">
		//function startApp() {
			sa.vcc.init (document.getElementById('scrollpane_content'));
			jQuery(window).resize (resizeApp);
			
			sa.m.waitForCondition ('db load',
				function () {
					var r = typeof window.parent.window.sa.s.c.settings.db == 'object';
					return r;
				},
				function () {
					var photoAlbumSettings = {
						div : 'scrollpane_content',
						iframe : 'iframeContent',
						album : '<?php echo $albumRelativePath;?>',
						page : <?php echo $page;?>,
						rootURL : '<?php echo $rootURL?>',
						db : window.parent.window.sa.s.c.settings.db.seductiveapps.com.ui.photoAlbum,
						lowres : {
							width : <?php echo $w?>,
							height : <?php echo $h?>
						},
						transformLinks : window.parent.window.sa.s.c.transformLinks
					};
					sa.vividControls.setOptions('pageMenu',{ level_0 : { orientation : 'horizontal', animspeed : 400 } });

					sa.photoAlbum.init (photoAlbumSettings);
					resizeApp();
				},
				10
			)



		//};
		function resizeApp () { 
			sa.photoAlbum.resize();
		}	
	</script>
<?php	
}

function shoppingCarts_html_viewImage ($image, $page) {
	global $photoAlbums_globals;
	global $cms;

	$rootURL = $photoAlbums_globals['urlAlbums'];
	$w = $photoAlbums_globals['lowres']['width'];
	$h = $photoAlbums_globals['lowres']['height'];
	
	$imageRelativePath = shoppingCarts_urlDecode ($image);

/*
	<a id="btnUp" href="#" style="position:absolute;top:1%;left:49%;z-index:10000;"><img src="<?php echo SA_SITE_WEB?>seductiveapps/com/ui/photoAlbum/btnUp.png" style="width:40px;" style="width:40px;"/></a>
	<a id="btnPrevious" href="#" style="position:absolute;top:50%;z-index:10000;width:40px;"><img src="<?php echo SA_SITE_WEB?>seductiveapps/com/ui/photoAlbum/btnPrevious.png" style="width:40px;"/></a>
	<a id="btnNext" href="#" style="position:absolute;top:50%;right:0px;z-index:10000;width:40px;"><img src="<?php echo SA_SITE_WEB?>seductiveapps/com/ui/photoAlbum/btnNext.png" style="width:40px;"/></a>
	<img src="<?php echo $rootURL.$imageRelativePath?>" style="width:98%;"/>

	<table style="width:100%;height:100%;">
		<tr><td style="text-align:center"><a id="btnUp" href="#"><img src="<?php echo SA_SITE_WEB?>seductiveapps/com/ui/photoAlbum/btnUp.png" style="width:40px;"/></a></td></tr>
		<tr style="height:*"><td style="width:*">&nbsp;</td></tr>
		<tr><td style="text-align:center;vertical-align:middle;"><img id="sashoppingCarts_photo" src="<?php echo $rootURL.$imageRelativePath?>" style="width:auto;height:auto;text-align:center;"/></td></tr>
		<tr style="height:*"><td style="width:*">&nbsp;</td></tr>
	</table>
*/	
?>
	<img id="sashoppingCarts_photo" style="position:relative;display:none;width:auto;height:auto;text-align:center;vertical-align:middle;"/>
	<a id="btnUp" href="#"><img src="<?php echo SA_SITE_WEB?>seductiveapps/com/ui/photoAlbum/btnUp.png" style="position:absolute;top:5px;left:49%;width:40px;"/></a>
	<a id="btnPrevious" href="#" style="position:absolute;top:50%;left:5px;z-index:10000;width:40px;"><img src="<?php echo SA_SITE_WEB?>seductiveapps/com/ui/photoAlbum/btnPrevious.png" style="width:40px;"/></a>
	<a id="btnNext" href="#" style="position:absolute;top:50%;right:5px;z-index:10000;width:40px;"><img src="<?php echo SA_SITE_WEB?>seductiveapps/com/ui/photoAlbum/btnNext.png" style="width:40px;"/></a>
	<script type="text/javascript">
		//function startApp() {
//			sa.vcc.init (document.body);
			var app = {};
			
			jQuery(window).resize (function () {
				if (app.photoSettings) resizeApp(app.photoSettings);
			});

			sa.m.waitForCondition ('db load',
				function () {
					var r = (
						typeof window.parent.window.sa.s.c.settings.db == 'object'
					);
					return r;
				},
				function () {
					var
					photoSettings = {
						div : 'scrollpane_content',
						iframe : 'iframeContent',
						image : '<?php echo $imageRelativePath;?>',
						page : <?php echo $page?>,
						rootURL : '<?php echo $rootURL?>',
						db : window.parent.window.sa.s.c.settings.db.seductiveapps.com.ui.photoAlbum,
						lowres : {
							width : <?php echo $w?>,
							height : <?php echo $h?>
						},
						transformLinks : window.parent.window.sa.s.c.transformLinks
					};
					app.photoSettings = photoSettings;
					sa.photoAlbum.init (photoSettings);
					sa.photoAlbum.setImageButtonLinks (photoSettings);
					resizeApp(photoSettings);

				},
				200
			)
		function resizeApp (photoSettings) { 
			sa.photoAlbum.setImageDimensions (photoSettings);
		}	


			
		//};
	</script>
<?php	
}



function shoppingCarts_html_viewAlbumContent ($album) {
	
	global $photoAlbums_globals;
	global $cms;

	$rootPath = $photoAlbums_globals['pathAlbums'];
	$rootURL = $photoAlbums_globals['urlAlbums'];
	$w = $photoAlbums_globals['lowres']['width'];
	$h = $photoAlbums_globals['lowres']['height'];

	$albumRelativePath = shoppingCarts_urlDecode ($album);
	$albumsContents = $cms->db->get ('seductiveapps/com/ui/photoAlbum/albumsContents', true);
	//var_dump ($albumsContents);
	$albumsContent = $albumsContents[$albumRelativePath];
	//var_dump ($albumRelativePath);
	//var_dump ($albumsContent);
	$html = '';	
	foreach ($albumsContent as $idx => $imageRelativePath) {
		$pi = pathinfo ($imageRelativePath);
		
		$hires = SA_SITE_WEB.'photo/'.$album;
		$thumbnail = $rootURL.$pi['dirname'].'/lowres/'.$pi['basename'];
		
		$html .= '<a class="sashoppingCarts_linkToImage" href="'.$hires.'" style="float:right"><img class="sashoppingCarts_thumbnail" src="'.$thumbnail.'"/></a>'."\n";
	}
	$html .= '<hr/>';
	
	return $html;
}

function shoppingCarts_urlEncode ($u) {
	$r = $u;
	$r = str_replace ('/', '_', $r);
	return $r;
}

function shoppingCarts_urlDecode ($ue) {
	$r = $ue;
	$r = str_replace ('_', '/', $r);
	return $r;
}



?>