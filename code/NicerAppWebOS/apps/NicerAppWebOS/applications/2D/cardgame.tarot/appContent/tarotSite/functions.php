<?php
$rootPath = realpath(dirname(__FILE__).'/../../../../../../../..');
require_once ($rootPath.'/NicerAppWebOS/boot.php');
require_once(dirname(__FILE__).'/../../appLogic/seo_tarot.php');
require_once(dirname(__FILE__).'/../../appLogic/functions.php');

function t2_getSiteRootURL() {
	return '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite';
}

function t2_getDecksRootURL() {
	return '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite';
}

function t2_getDecksFromFilesystem() {
	$p = getFilePathList (dirname(__FILE__).'/decks', TRUE, '/^back.*/', null, array('file'));
	//reportVariable('$p', $p); exit();
	$r = array();
	$r1 = array();
	foreach ($p as $idx => $path) {
		$p[$idx] = $path;
		$path = str_replace('/back2.jpg','',$path);
		$path = str_replace('/back.jpg','',$path);
		$path = str_replace('/back.png','',$path);
		$path = str_replace(dirname(__FILE__).'/decks/', '', $path);
		//var_dump($path);exit();

		if (strpos($path,'\\')!==false) $pp = explode('\\', $path); else $pp= explode('/', $path);
		//var_dump ($idx);var_dump ($pp);exit;
		$picName = $pp[count($pp)-1];
	
		if (array_key_exists(0,$pp)) {
			$name0 = $pp[0];
			$r[$name0] = array();
		}
		if (array_key_exists(1,$pp)) {
			$name1 = $pp[1];
			$r[$name0][$name1] = array();
		}
		if (array_key_exists(2,$pp)) {
			$name2 = $pp[2];
			$r[$name0][$name1][$name2] = array();
		}
		if (array_key_exists(3,$pp)) {
			$name3 = $pp[3];
			$r[$name0][$name1][$name2][$name3] = array();
		}
		if (array_key_exists(4,$pp)) {
			$name4 = $pp[4];
			$r[$name0][$name1][$name2][$name3][$name4] = array();
		}
		if (array_key_exists(5,$pp)) {
			$name5 = $pp[5];
			$r[$name0][$name1][$name2][$name3][$name4][$name5] = array();
		}
		
		$r1 = array_merge_recursive ($r1, $r);
	}
	//var_dump ($r1); exit();
	return $r1;
}


global $decks;
$decks = null;

function t2_getDecks() {
	global $decks;
	$fn = dirname(__FILE__).'/tarot_decks.json';
	if (!file_exists($fn)) {
		// re-scan the filesystem for any new decks;
        $decks = t2_getDecksFromFilesystem();
        file_put_contents ($fn, json_encode($decks)); // and cache as JSON data
	} else {
		// use cached decks;
		//echo 'reading '.$fn.PHP_EOL;
		if (is_null($decks)) $decks = json_decode(file_get_contents($fn), true);
	}
	
	global $decks;
	//echo '<pre>'; var_dump ($decks); die();

	return $decks;
}

function t2_getNumberOfDecks() {
	$decks = t2_getDecks();
	//return t2_getNumberOfDecks_recurse($decks);
	$c = 0;
	count_elt ($decks, $c);
	return $c;
}


function count_elt($array, &$count=0){
  if (count($array)===0) ++$count;
  foreach($array as $v) if(is_array($v)) count_elt($v,$count); else ++$count;
  return $count;
}






function t2_getNumberOfDecks_recurse ($d) {
	$r = 0;
	if (is_array($d) && count($d)>0) {
		foreach ($d as $n => $v) {
			if (!is_array($v)) {
				$r++;
			} else {
				$r += t2_getNumberOfDecks_recurse($v);
			}
		}
	}

	return $r;
}

function t2_html_deck_info () {
	$cd = t2_getCurrentDeck();
	
	$xmlFile = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/decks/'.$cd.'/packinfo.xml';
	if (file_exists($xmlFile)) {
		$xml = file_get_contents($xmlFile);
		//var_dump ($xmlFile);
	
		$name = t2_html_deck_info_preg ('/\<PackName\>(.*)\<\/PackName\>/', $xml, 'Could not find deck name..');
		$description = t2_html_deck_info_preg ('/\<Description\>(.*)\<\/Description\>/', $xml, 'Could not find a deck description in this deck\'s xml file');
	
		$r = 
			'<table>'
			.'<tr><td>'
			.'<h2 style="display:inline-block;">Deck : ' . $name . '</h2>'
			.'<p class="backdropped">' . $description . '</p>'
			.'</td><td style="vertical-align:bottom">';
		if (file_exists(dirname(__FILE__).'/decks/'.$cd.'/box.jpg')) $r .= t2_html_deck_box('box.jpg',$cd);
		if (file_exists(dirname(__FILE__).'/decks/'.$cd.'/box1.jpg')) $r .= t2_html_deck_box('box1.jpg',$cd);
		if (file_exists(dirname(__FILE__).'/decks/'.$cd.'/box2.jpg')) $r .= t2_html_deck_box('box2.jpg',$cd);
		$r .= '</td></tr></table>';
	} else {
		$r = 'No Deck info available.';
	}
	
	return $r;
}

function t2_html_deck_box ($boxFilename,$deck) {
	return '<a href="'.t2_getSiteRootURL().'/decks/'.$deck.'/'.$boxFilename.'" rel="prettyPhoto" title="'.$boxFilename.'"><img style="width:60px;" src="'.t2_getSiteRootURL().'/decks/'.$deck.'/'.$boxFilename.'"/></a>';
}

function t2_html_deck_info_preg ($pattern, $subject, $default) {
	$r = $default;
	$matches = array();
	preg_match_all ($pattern, $subject, $matches);
	if (
		array_key_exists(1,$matches)
		&& array_key_exists(0, $matches[1])
	) $r = $matches[1][0];
	return $r;
}

function t2_html_menu_decks() {
	
	$decks = t2_getDecks();
	//echo '<pre>';var_dump ($decks);die();
	//$decks = getFilePathList(dirname(__FILE__).'/decks/',true,FILE_FORMATS_photos, null, array('dir'), null, 1, true);

	//echo '</ul></ul></ul></ul></div></div></div><pre>'; var_dump ($decks); die();


	$html = t2_html_menu_decks_recurse ($decks);	
	//echo '</ul></ul></ul></ul></div></div></div><pre>'; var_dump ($html); die();
	//echo $html; die();
	
	return $html;
}	

function t2_html_menu_decks_recurse ($decks, $path="") {
	global $naWebOS;
	$html='';
	$d = &$decks;

	if (is_array($d) && count($d)>0) {
		foreach ($d as $n => $v) {
			$path2 = $path.'/'.$n;
			//echo '<pre>'; var_dump ($path2); echo '</pre>';
			//$path = t2_getCurrentDeck_recurse($decks, $n, '').'/'.$n;

			if (is_array($v) && count($v)===0) {
				$viewSettings = [
					'deck' => $path2,
					'reading' => $_GET['reading'] // should be fine at this point
				];
                $hrefJSON = '{"/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot":{'.json_encode($viewSettings).'}}';
                $href = '/view/'.encode_base64_url($hrefJSON);
                $n = str_replace ('(','',$n);
                $n = str_replace (')','',$n);
				$html.='<li><a class="appMenu selectDeck" href="'.$href.'">'.$n.'</a></li>';
			} else if (is_array($v)) {
				$html.='<li><a class="appMenu noPushState" href="javascript:return false;">'.$n.'</a>';
				$html.="\n<ul>\n\t";
				$html.=t2_html_menu_decks_recurse($v, $path2);
				$html.='</ul>'."\n";
			}
		}
	}
	return $html;
}

function t2_getNumberOfReadings () {
	$readings = getFilePathList(dirname(__FILE__),false,'/.*tarot_reading_.*\.json$/',null, array('file'));
	return count($readings);
}

function t2_html_menu_readings() {
	global $naWebOS;
	$html = '';
	$readings = getFilePathList(dirname(__FILE__),false,'/.*tarot_reading_.*\.json$/',null, array('file'));
	//var_dump ($readings); exit();
	asort($readings);
	foreach ($readings as $idx=>$readingJSONfilepath) {
		$r = str_replace(dirname(__FILE__).'/tarot_reading_','',$readingJSONfilepath['realPath']);
		$r = str_replace('.json','',$r);
                global $naWebOS;
				$view = $naWebOS->view['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/'];
				if (array_key_exists('apps',$view)) {
					$viewSettings = array (
						'deck' => $view['apps']['deck'],
						'reading' => $_GET['reading'] // should be fine at this point
					);
				} else {
					$viewSettings = array (
						'deck' => $view['deck'],
						'reading' => $_GET['reading'] // should be fine at this point
					);
				}
                //var_dump ($naWebOS->view);
                //var_dump ($viewSettings); exit();
                $hrefJSON = '{"/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/":{"cardgame.tarot":'.json_encode($viewSettings,true).'}}';
                $href = '/view/'.encode_base64_url($hrefJSON);
		$html .= '<li><a class="appMenu selectReading" href="'.$href.'">'.$r.'</a></li>'."\n";
	}
	return $html;
}

function t2_draw_cards ($readingID, $explanationID) {
	//error_log ('readingID='.$readingID);
	$file = dirname(__FILE__).'/tarot_reading_'.$readingID.'.json';
	
	$reading = json_decode(file_get_contents($file),true);

	global $naWebOS;
	//var_dump ($naWebOS->view);	var_dump ($file); var_dump ($reading); exit();
	
	if (is_null($reading)) trigger_error ('Could not decode '.$file,E_USER_ERROR);

	$deck = t2_getCurrentDeck();
	//var_dump ($deck); exit();
	
	$ciFile = dirname(__FILE__).'/tarot_card_index.json';
	if (!file_exists($ciFile)) $ciFile = 'tarot_card_index.json';
	$cardIndex = json_decode(file_get_contents($ciFile), true);
	if (is_null($cardIndex)) trigger_error ('Could not decode '.$ciFile, E_USER_ERROR);

	foreach ($reading['cards'] as $cardNo => $card) {
		if (
			stripos($deck,'majors')!==false 
			|| (
				array_key_exists('majors',$_GET)
				&& $_GET['majors']=='majors'
			)
		) $upperLimit=21; else $upperLimit=77;
		
		$cn = false;
		while ($cn===false || t2_haveCardInReading($reading, $cn)) {
			$cn = rand(0,$upperLimit);
		}
		$reading['cards'][$cardNo]['drawnCardNumber'] = $cn;
		$reversed = $reading['cards'][$cardNo]['reversed'] = (rand(0,1)==0?false:true);
		
		global $explanationID;
		$explanationID = 'nicerEnterprises_tarot';
		
		$file = dirname(__FILE__).'/explanations/'.$explanationID.'/'.$cardIndex[$cn]['explanation'].'.html';
		//var_dump ($file); exit();
		if (!file_exists($file)) $file = dirname(__FILE__).'/explanations/'.$explanationID.'/'.$cardIndex[$cn]['explanation'].'.html';
		$e = file_get_contents($file);
		$reading['cards'][$cardNo]['explanation'] = $e;
		
		$reading['cards'][$cardNo]['cardName'] = $cardIndex[$cn]['cardName'];
	}
	return $reading;
}

function t2_haveCardInReading ($reading, $cn) {
	foreach ($reading['cards'] as $cardNo => $cardRec) {
		if (array_key_exists('drawnCardNumber', $cardRec) && $cn===$cardRec['drawnCardNumber']) return true;
	}
	return false;
}

function t2_getBadDecks () {
    global $naWebOS;
	$deck = $naWebOS->view['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D']['cardgame.tarot']['deck'];//$_GET['deck'];
	$decks = t2_getDecks();

	t2_getBadDecks_recurse($decks, '');
}

function t2_getBadDecks_recurse ($d, $cd) {
	$found = false;
	if (is_array($d) && count($d)>0) {
		foreach ($d as $n => $v) {
			if (!is_array($v)) {
				//var_dump (array('$n'=>$n,'$wantedDeck'=>$wantedDeck,'result'=>($n==$wantedDeck)));
				if (strpos($n,'-')!==false) {
					echo $cd.'/'.$n."<br/>\r\n";
				}
			} else {
				$res = t2_getBadDecks_recurse ($v, $cd.'/'.$n);
			}
		}
	}
}


function t2_getCurrentDeck () {
	global $naWebOS;
	$deck = $_GET['deck'];
	return $deck;
	/*
	//var_dump ($deck); exit();
	
	$decks = t2_getDecks();
	//var_dump ($decks); exit();

	$path = t2_getCurrentDeck_recurse($decks, $deck, '');
 	//var_dump ($path); exit();
	
	return $path['result']; // 'Waite-Smith/Pam Colman Smith/Original Rider Waite Tarot';
	*/
}



function t2_getCurrentDeck_recurse ($d, $wantedDeck, $path) {
	$found = false;
	$res = array();
	//echo '<pre>';var_dump ($d); 

	
	if (is_array($d) && count($d)>0) {
		foreach ($d as $n => $v) {
			//if (is_array($v) ) {
				//echo '<pre>'; var_dump (array('path'=>$path,'is_array($v)'=>is_array($v),'$n'=>$n,'$wantedDeck'=>$wantedDeck,'result'=>(strpos($n,$wantedDeck)))); echo '</pre><br/><br/>'.PHP_EOL.PHP_EOL;
				$found = false;
				if (
					strpos($n,$wantedDeck)!==false
				) {
					$found = true;
				}
				
				
				if ($found) {
					
					$r = array(
						'found' => $found,
						'result' => $path.'/'.$n
					);
					
					return $r;
				} else {
                    if (is_array($v)) {
                        $r = t2_getCurrentDeck_recurse ($v, $wantedDeck, $path.'/'.$n);
                        if (is_array($r) && $r['found']===true) return $r;
                    }
				}
			//} 
		}
	}
}

function t2_html_draw_cards ($reading) {
	$html = '';
	$deck = t2_getCurrentDeck();
	$deck = $_GET['deck'];//$naWebOS->view['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot']['deck'];
	//var_dump ($deck); exit();
	foreach ($reading['cards'] as $cardNo => $card) {
		$html .= '<div id="card__'.$cardNo.'" class="quickflip card" style="position:absolute; z-index:999;right:'.$card['right'].'px; top:'.$card['top'].'px;width:'.$reading['theme']['cardWidth'].'px;height:'.$reading['theme']['cardHeight'].'px;text-align:center;">';
		//$html .= '	<div class="quickflip-front" style="width:100%;height:100%">';
		$html .= '		<img idx="'.$cardNo.'" style="top:15px; width:100%;height:100%;" id="backOfCard__'.$cardNo.'" class="backOfCard" src="'.t2_getDecksRootURL().'/decks/'.$deck.'/back.jpg"/>';
		//$html .= '	</div>';
		//$html .= '	<div class="quickflip-back" style="width:100%;height:100%">';
		//$html .= '		<!--<a href="'.t2_getDecksRootURL().'decks/'.$deck.'/'.($card['drawnCardNumber']<10?'0':'').$card['drawnCardNumber'].'.jpg" rel="saZoomPhoto" title="'.$card['cardName'].'">-->';
		$html .= '		<img id="frontOfCard__'.$cardNo.'" class="frontOfCard" idx="'.$cardNo.'" style="display:none;width:100%;height:100%;" src="'.t2_getDecksRootURL().'/decks/'.$deck.'/'.($card['reversed']?'r':'').($card['drawnCardNumber']<10?'0':'').$card['drawnCardNumber'].'.jpg"/>';
		//$html .= ' 		<!--</a>-->';
		//$html .= '		<div style="width:'.$reading['theme']['cardWidth'].'px;text-align:center;"><a href="javascript:ts.displayCardExplanation('.$cardNo.');">explanation</a></div>';
		//$html .= '	</div>';
		$html .= '</div>'."\n";
	}
	return $html;
}

function t2_make_goodFilenames () {
	$files = getFilePathList (dirname(__FILE__).'/decks/', true, '/back.*/i', null, array('file'));
	//var_dump ($files);
	foreach ($files as $idx => $fp) {
		$pi = pathinfo($fp);
		$filename = $pi['basename'];
		
		$execStr = 'cmd /c ren "'.$fp.'" "'.$pi['dirname'].'/back.jpg"';
		exec ($execStr, $output, $return);
		var_dump ($execStr);
		var_dump ($output);
		var_dump ($return);
	}
}

function t2_make_strangeFilesList () {
	$files = getFilePathList (getcwd().'/decks/', true, '/^\d\d\.jpg$/', null, array('file'));
	$notStrange = array (
		'/^\d\d.jpg$/',
		'/^r\d\d.jpg$/',
		'/^back.jpg$/',
		'/^CardData.xml$/',
		'/^packinfo.xml$/'
	);
	$r = '';
	foreach ($files as $idx => $fp) {
		$pi  = pathinfo($fp);
		$strange = true;
		foreach ($notStrange as $idx=>$ns) {
			if (preg_match($ns, $pi['basename'])) $strange=false;
		}
		if ($strange) $r.=$fp."\r\n";
	}
	file_put_contents('strange.lst', $r);
}

function t2_make_reverseCards () {
	$decks = t2_getDecks();
	$files = getFilePathList (getcwd().'/decks/', true, '/^\d\d\.jpg$/', null, array('file'));
	$execStr = '';	
	foreach ($files as $idx => $filepath) {
		$pi = pathinfo($filepath);
		$filename = $pi['basename'];
		
		$execStr .= 'convert "'.$filepath.'" -rotate 180 "'.$pi['dirname'].'/r'.$filename.'"'."\r\n";
	}
	file_put_contents ('img-convert.bat', $execStr);
}

function t2_html_explanation ($reading) {
	return '';
}

function t2_calculate_size_for_cards ($reading) {
	$top = 0; 
	$left = 0;
	foreach ($reading['cards'] as $idx => $card) {
		if ($card['top']>$top) $top = $card['top'];
		if ($card['left']>$left) $left = $card['left'];
	}
	$x = $left + $reading['theme']['cardWidth'] + 10;
	$y = $top + $reading['theme']['cardHeight'] + 10;
	$reading['theme']['size'] = array (
		'x' => $x,
		'y' => $y
	);
	return $reading;
}

function t2_get_readingType($readingType) {
    global $naWebOS;
	//echo '<pre>'; var_dump($naWebOS->view); echo '</pre>'; exit;
	$view = $naWebOS->view['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/']['cardgame.tarot'];
	//echo '<pre>'; var_dump($view); echo '</pre>'; exit;
	if (!t2_valid_readingType($readingType)) {
		$readingType = '3 Cards';
	}
	return $readingType;
}

function t2_valid_readingType($readingType) {
//echo 'BUG_content_001 : defined functions =<pre>'; var_dump (get_defined_functions()); echo '</pre>'; 
	$rt = getFilePathList (dirname(__FILE__), false, '/tarot_reading_.*\.json/', null, array('file'));
	//var_dump ($rt); 
	$valid = false;
	foreach ($rt as $idx => $ret) {
		$reat = str_replace(dirname(__FILE__).'/tarot_reading_', '', $ret['realPath']);
		$reat = str_replace('.json', '', $reat);
		if ($reat == $readingType) {
			$valid = true;
			break;
		}
	}
	return $valid;
}
?>
