<?php
class webappObfuscator_obfuscate__javascript {

	public $settings = null;
	public $workData = array();
	private $pastModes = array();
	
	public function __construct ($settings) {
		$this->settings = $settings;
	}
	
	
	public function obfuscate () {
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		$wHTML = $obfuscator->getWorker('html');
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;

		global $deadSlow______butThorough; 
			// $deadSlow______butThorough===FALSE AT THE MOMENT TO AVOID obfuscate__stage3() which is a *major* time-drain for larger source collections being obfuscated
			// hope this can stay set to false, and eventually all that code "behind it" removed entirely, but will have to test against my own seductiveapps.com sources first
			// might take until 2015 July 15th to know that.
			
		
		if ($deadSlow______butThorough) {
			reportStatus ('<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 1 of 3 - obfuscating '.filesizeHumanReadable(strlen($wd['sourceForObfuscation'])).'</p>');
			$wd['output'] = $this->obfuscate__stage1 ($wd['sources']['stages'][2]['sourcecode'], $obfuscator->workData);
		
			reportStatus (500, 
				'<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 2 of 3 - obfuscating [\'stringsRemoved\'] into [\'stringsObfuscated\'] ('
				.count($obfuscator->workData['tokens']).' tokens * '.count($wd['sources']['stages'][2]['stringsRemoved']).' strings = '
				.largeNumberHumanReadable(count($obfuscator->workData['tokens']) * count($wd['sources']['stages'][2]['stringsRemoved'])).' iterations of preg_replace().)</p>'
			);
			$wd['obfuscation'] = array(
				'stages' => array (
					2 => array(
						'stringsObfuscated' => array()
					)
				)
			);
		
			$wd['obfuscation']['stages'][2]['stringsObfuscated'] = $this->obfuscate__stage2 ($wd['sources']['stages'][2]['stringsRemoved'], $obfuscator->workData);

			reportStatus (500, 
				'<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 3 of 3 - restore strings and regular expressions as found in [\'stringsObfuscated\']'
				.' ('.filesizeHumanReadable(strlen($wd['output'])).', '.largeNumberHumanReadable(count($wd['obfuscation']['stages'][2]['stringsObfuscated'])).' strings)</p>'
			);
			$wd['output'] = $this->obfuscate__stage3 ($wd['output'], $wd['obfuscation']['stages'][2]['stringsObfuscated']);
			//$wd['obfuscation']['stages'][2]['stringsObfuscated'] = $wd['sources']['stages'][2]['stringsRemoved'];
			
		} else {
			
			$allSources = $wd['sources']['stages'][0];
			$allTokens = $obfuscator->workData['tokens'];
			$wd['output'] = array();
			foreach ($allSources as $sourcesDescription => $sources) {
				if (is_array($sources)) {
					$wd['output'][$sourcesDescription] = array();
					foreach ($sources as $sourceDescription => $source) {
						//$minified = $source;
						$minified = $this->stripCommentsAndMinify($source);
						reportStatus (500, 
							'<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 1 of 1 -'
							.' obfuscating $sources["javascript"]["'.$sourcesDescription.'"]["'.$sourceDescription.'"]'
							.' ('.filesizeHumanReadable(strlen($minified)).')</p>'
						);
					
						$output = $obfuscator->phpJSO_replace_tokens ($allTokens, $minified);
						foreach ($wd['stringsRemoved'] as $idx => $stringToRestore) {
							$search = '`'.$idx.'`';
							$output = str_replace ($search, $stringToRestore, $output);
						}
						$wd['output'][$sourcesDescription][$sourceDescription] = $output;
					}
				} else {
					//$minified = $sources;
					$minified = $this->stripCommentsAndMinify($sources);
					reportStatus (500, 
						'<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 1 of 1 -'
						.' obfuscating $sources["javascript"]["'.$sourcesDescription.'"]'
						.' ('.filesizeHumanReadable(strlen($minified)).')</p>'
					);
				
					$output = $obfuscator->phpJSO_replace_tokens ($allTokens, $minified);
					foreach ($wd['stringsRemoved'] as $idx => $stringToRestore) {
						$search = '`'.$idx.'`';
						$output = str_replace ($search, $stringToRestore, $output);
					}
					$wd['output'][$sourcesDescription] = $output;
				}
			}

			//echo '<pre>$wd=';foreach ($wd as $k=>$v) { var_dump($k); } die();
			$sources = $wd['sourceForObfuscation'];
			$minified = $this->stripCommentsAndMinify($sources);
			reportStatus (500, 
				'<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 1 of 1 -'
				.' obfuscating $sources["javascript"]  --- siteTemplate'
				.' ('.filesizeHumanReadable(strlen($minified)).')</p>'
			);
		
			$output = $obfuscator->phpJSO_replace_tokens ($allTokens, $sources);

			$htmlTokens = $wHTML->workData['tokensHTML'];
			foreach ($wd['stringsRemoved'] as $idx => $stringToRestore) {
				//$wd['stringsRemoved'][$idx] = str_replace ($htmlTokens, array_keys($htmlTokens), $stringToRestore);
			}
			
			foreach ($wd['stringsRemoved'] as $idx => $stringToRestore) {
				$search = '`'.$idx.'`';
				$output = str_replace ($search, $stringToRestore, $output);
			}
			
			$wd['sourceObfuscated'] = $output;
		}
		
	}
	
	
	
	
	// OBFUSCATE() stage :
	
	public function stripCommentsAndMinify ($source) {
		$src = preg_replace('#var\r\n#', 'var ', $source);
		$src = str_replace('%', '% ', $src);
		$src = str_replace('% 20', '%20', $src);

	
		$regxSearch = array (
			'#//.*[\r\n]#',
			'#\/\*[\s\S]*?\*\/#'
		);
		$regxReplace = array ('', '');
		$r = pregReplace ($regxSearch, $regxReplace, $src);
		if (is_string($r)) {
			//BULLSHIT FUNCTION $r = $this->phpJSO_strip_junk($r);
			$r = str_replace(';', ";\r\n", $r);
			return $r; 
			
		} else return false;
	}
	
	
	
	public function obfuscate__stage1 ($src, &$owd) {
		//$dbg = 'webappObfuscator_obfuscate__javascript::obfuscate__stage1 (PRE-CALL)'."\r\n".$src;
		//var_dump ($dbg); reportStatus ('<pre>'.$dbg.'</pre>');
		$output = $obfuscator->phpJSO_replace_tokens ($owd['tokens'], $src);
		//$dbg = 'webappObfuscator_obfuscate__javascript::obfuscate__stage1 (POST-CALL)'."\r\n".$output;
		//var_dump ($dbg); //reportStatus ('<pre>'.$dbg.'</pre>');
		return $output;
	}
	
	public function obfuscate__stage2 (&$strings, &$owd) {
		global $tokenBoundary; $b = $tokenBoundary;
		global $regxBoundary; $rx = $regxBoundary;
		global $searchTokenSpecialChars; global $replaceTokenSpecialChars;		
		$stringsObfuscated = $strings;
		foreach ($owd['tokens'] as $token => $tokenObfuscated) {
			$tokkie = str_replace ($searchTokenSpecialChars, $replaceTokenSpecialChars, $token);
			$regx = $rx.$b.$tokkie.$b.$rx;
			foreach ($stringsObfuscated as $sk => $sv) {
				//$replace = '${1}'.$owd['tokensObfuscated'][$tk].'${2}';
				$replace = $tokenObfuscated;
				$r = pregReplace ($regx, $replace, $sv);
				if (is_string($r)) $stringsObfuscated[$sk] = $r;
			}
		}
		return $stringsObfuscated;
	}
	
	public function obfuscate__stage3 (&$output, &$strings) {
		//$dbg = 'webappObfuscator_obfuscate__javascript::obfuscate__stage3 (PRE-CALL)'."\r\n".$output;
		//var_dump ($dbg); reportStatus ('<pre>'.$dbg.'</pre>');
		//var_dump ('webappObfuscator_obfuscate__javascript::obfuscate__stage3() :'); var_dump ($strings);
		
		$output = $this->phpJSO_restore_strings ($output, $strings);
		$output = str_replace ('}', '}'."\r\n", $output);

		//$dbg = "\r\n"."\r\n"."\r\n"."\r\n"."\r\n"."\r\n"."\r\n"."\r\n"."\r\n"."\r\n"."\r\n"
		//	."\r\n".'webappObfuscator_obfuscate__javascript::obfuscate__stage3 (POST-CALL)'."\r\n".$output;
		//var_dump ($dbg); //reportStatus ('<pre>'.$dbg.'</pre>');
		return $output;
	}
	
	
	public function preprocess ($sources=null) {
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		if (is_null($sources)) $sources = &$gs['sources']['fetched']['javascript'];
		//var_dump ($sources); 
		global $lowerMemoryUsage;

		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 1 of 3 - walking sources</p>');
		$wd = array(
			'sources' => array (
				'stages' => array (
					0 => $sources
				)
			)
		);
		$wd['sources']['stages'][1] = $this->preprocess__stage1__walk ('', $sources);
		
		$wd['sources']['stages'][2] = $this->preprocess__stage2new ($wd['sources']['stages'][1]);
		$wd['tokens'] = $wd['sources']['stages'][2]['tokens'];
		$wd['sourceForObfuscation'] = $wd['sources']['stages'][2]['sourceToObfuscate'];
		$wd['stringsRemoved'] = $wd['sources']['stages'][2]['stringsRemoved'];
	
		/* // 2015 july 4th (Rene) : old phpJSO routines ----> fundamentally flawed i'm afraid (when tested against my own seductiveapps.com sources).
		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 2 of 3 - stripping strings and regular expressions</p>');
		$wd['sources']['stages'][2] = $this->preprocess__stage2 ($wd['sources']['stages'][1]);
		
		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 3 of 3 - gathering tokens</p>');
		$data = &$wd['sources']['stages'][2];
		$r = $this->preprocess__stage3 ($data['sourcecode'], $data['stringsRemoved']);
		$wd['tokens'] = $r['tokens'];
		$wd['sourceForObfuscation'] = $r['sourceForObfuscation'];
		//echo '<pre>123'; var_dump ($wd['tokens']); die();
		*/
		
		if ($lowerMemoryUsage) {
			unset ($wd['sources']['stages'][1]);
		}
		
		//echo '<pre>preprocess $wd=';foreach ($wd as $k=>$v) { var_dump($k); } 

		$this->workData = $wd;
	}
	
	private function preprocess__stage1__walk ($path, $sources, $output='') {
		foreach ($sources as $k => $v) {
 			if (is_string($v)) {
			reportStatus (500, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 1 - processing '.$k.'</p>');
				$output .= $this->preprocess__stage1__node ($path, $k, $v);
			} if (is_array($v)) {
				$output .= $this->preprocess__stage1__walk ($path.'/'.$k, $sources[$k], $output);
			}
		}
		
		return $output;
	}
	
	private function preprocess__stage1__node ($path, $k, $src) {
		$src = preg_replace('#var\r\n#', 'var ', $src);
		$src = str_replace('%', '% ', $src);
		$src = str_replace('% 20', '%20', $src);
		$src = $src."\r\n";
		
		/*
		if (strpos($k, 'saCore')!==false) { 
			echo '<pre>$path='.$path.'/'.$k.'</pre>'; $r = $this->getTokensAndStrings($src);
			echo '<pre style="color:red;fontweight:bold;">'; var_dump ($r['sourceToObfuscate']); echo '</pre>';
			die();
		}*/
		return $src;
	}
	
	private function preprocess__stage2new ($source) {
		/*$regxSearch = array (
			'#//.*[\r\n]#',
			'#\/\*[\s\S]*?\*\/#'
		);
		$regxReplace = array ('', '');
		$src = pregReplace ($regxSearch, $regxReplace, $source);
		*/
	
	
		$r = $this->getTokensAndStrings($source);
		
		//echo '<pre style="color:brown;font-weight:bold;">stage2_new $r='; echo htmlentities($r['sourceToObfuscate']); echo '</pre>';	die();
		
		return $r;
	}
	
	public function getTokensAndStrings ($source) {
		//echo '<pre style="color:#030">$source='; echo htmlentities($source); echo '</pre>';
	
		$debugMe = true;
		//$sourceMinusStrings = '';
		//$sourceMinusComments = '';
		$sourceToObfuscate = '';
		$stringsRemoved = array();
		$stringsRemovedCount = -1;
		$tokens = array();
		$tokensDbg = array();
		$token = '';
		$tokenDelimiters = array ('!', '*', '/', '+', '-', ',', '\\', '.', '[', ']', '(', ')', '{', '}', '?', ':', '=', '|', '&', ';', ' ', '\t', '\r', '\n');
		$modes = array(
			'inString' => false,
			'inRegexp' => false,
			'inArguments' => false,
			'inComment' => false,
			'inHTML' => false,
			'inJavascript' => true
		);
		$modesStartI = array('modes' => array(), 'stringModes' => array(), 'commentModes'=>array() );
		$modesStartI2 = array('modes' => array(), 'stringModes' => array(), 'commentModes'=>array() );
		$argumentDepthLevel = 0; // this opens whole shipping-containers full of worms..
		$stringModes = array (
			'inSingleQuoted' => false,
			'inDoubleQuoted' => false,
			'mustKeepInSource' => true
			
		);
		$commentModes = array (
			'singleLine' => false,
			'multiLine' => false
		);
		$tokenCount = 0;
		$debugI = false;
		$loopMode = array();
		$nowCapturing = array();
		$maxScanback = 1000;
		
		$dbg10 = false;
		$dbg11 = false;
		$dbg12 = false;
		
		for ($i=0; $i<strlen($source); $i++) {
			$c = substr($source, $i, 1);
			$c2 = substr($source, $i, 2);
			$c3 = substr($source, $i, 3);
			$dbg = substr($source, $i - 500, 500) . ' ___ ' . $c . ' ___ ' . substr($source, $i+1, 500);
			
			
			// BEGIN DEBUG --- scanback viewer
			if (array_key_exists($i-1, $this->pastModes)) {
				$pastModeOuter = $this->pastModes[$i-1]['modes'];
			} else {
				$pastModeOuter = null;
			}
			
			/*if (
				$modes['inString'] === true
				&& $pastMode['inString'] === false
			) {
				for ($j = $i-2; $j--; $j>$i-$maxScanback) {
					$pastMode = json_decode($this->pastModes[$j]['modes'],true);
					if ($modes['inString']===true) break;
				}
				$scanbackStartI = $j;
			}*/
			
			/* TODO : needs work
			foreach ($modes as $k => $modeNowEnabled) {
				if (!array_key_exists($k, $nowCapturing)) $nowCapturing[$k] = false;
				if (
					$modes['inString'] === false
					&& !is_null($pastModeOuter)
					&& $pastModeOuter['inString'] === true
				) {
					unset ($modesStartI[$k]); // unsure if this is needed
					unset ($modesStartI2[$k]);
				}					
				
				
				if ($k==='inString' && $modeNowEnabled && array_key_exists($k, $modesStartI)){
					if (
						$i - $modesStartI[$k] > 100
						&& $i - $modesStartI[$k] < 200
					) {
						if (!array_key_exists($k, $modesStartI2)) $modesStartI2[$k] = $i;
						$j = ($modesStartI[$k] - 20) + ($i - $modesStartI2[$k]);
					
						if (array_key_exists($j, $this->pastModes))  {
							$dbgModes = $this->pastModes[$j];
							$pastMode = json_decode($this->pastModes[$j]['modes'],true);
							$pastStringMode = json_decode($this->pastModes[$j]['stringModes'],true);
							$pastCommentMode = json_decode($this->pastModes[$j]['commentModes'],true);
							
							// scan ahead
							for ($m=0; $m < 4; $m++) {
								$m1 = $j + $m;
								if (array_key_exists($m1, $this->pastModes))  {
									$dbgModesScanForward = $this->pastModes[$m1];
									$pastScanForward_modes = json_decode($this->pastModes[$m1]['modes'],true);
									$pastScanForwardString_modes = json_decode($this->pastModes[$m1]['stringModes'],true);
									$pastScanForwardComment_modes = json_decode($this->pastModes[$m1]['commentModes'],true);
									if (
										$pastScanForward_modes[$k] === true
										&& $pastMode[$k] === false
									) {
										$nowCapturing[$k] = true;
										//echo '<pre style="color:white;background:grey;font-weight:bold;">$i='.$i.', $m='.$m.', $this->pastModes[$m]=';var_dump ($dbgModesScanForward);echo'</pre>';
									}
									
								}
							}
							
							if (
								$pastMode[$k] === false
								&& $nowCapturing[$k] === true
							) {
								echo '<pre style="color:white;background:grey;	font-weight:bold;">$i='.$i.', $j='.$j.', $k='.$k.', $dbgPastMode[$j]='; var_dump ($dbgModes); echo '</pre>';
							}
							
							if (
								$pastMode[$k] === true
							) {
								echo '<pre style="color:red;background:white;font-weight:bold;">$i='.$i.', $j='.$j.', $k='.$k.', $this->pastModes[$j]=';var_dump ($dbgModes);echo'</pre>';
							} /*elseif ($nowCapturing && $pastMode[$k]===false) {
								$nowCapturing = false;
							} elseif ($nowCapturing) {
								echo '<pre style="color:green;background:white;font-weight:bold;">$i='.$i.', $j='.$j.', $k='.$k.', $this->pastModes[$j]=';var_dump ($dbgModes);echo'</pre>';
							};* /
						}
					}
				}
				
				/*
				if (!$modeNowEnabled) {
					unset ($modesStartI[$k]);
				}
				
				if ($modeNowEnabled && !array_key_exists($k, $modesStartI)) {
					$modesStartI[$k] = $i;
				}* /
				
				
			} */
			// END DEBUG --- scanback viewer
		
			// skip comments
			if (
				$c2=='/*'
				&& $modes['inString'] === false
				&& $modes['inRegexp'] === false
			) {
				//echo '<p style="color:red;font-weight:bold;font-size:110%">'.substr($source, $i, 100).'</p>';
			
				$j = $i + 2;
				$d2 = substr ($source, $j, 2);
				while ($d2!=='*/') {
					$j++;
					$d2 = substr ($source, $j, 2);
				};
				//echo '<p style="color:red;font-weight:bold;font-size:110%">'.substr($source, $i, $j-$i + 3).'</p>';
				//die();
				$i = $j + 2; //TEST +2 works better than + 1 ??
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
			} 
			
			/*
			if (
				$c2=='//'
				&& (
					$modes['inString'] === true
					|| $modes['inRegexp'] === true
				)
			) {
				$debugI = $i;
			}*/
			
			if (
				$c2=='//'
				&& $modes['inString'] === false
				&& $modes['inRegexp'] === false
			) {
				//echo '<p style="color:red;font-weight:bold;font-size:110%">'.substr($source, $i, 100).'</p>';
			
				$j = $i + 1;
				$d = substr ($source, $j, 1);
				while (
					$d!=="\r"
					&& $d!=="\n"
				) {
					$j++;
					$d = substr ($source, $j, 1);
				};
				//echo '<p style="color:red;font-weight:bold;font-size:110%">'.substr($source, $i, $j-$i + 3).'</p>';
				//die();
				$i = $j + 1;
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
			} 
		
		
		
		
			
			// argument detection :
			if (
				$modes['inComment']===false
				&& $modes['inRegexp']===false
				&& $c==='('
			) {
				$modes['inArguments'] = true;
				$argumentDepthLevel++;
			};
			if (
				$modes['inComment']===false
				&& $modes['inRegexp']===false
				&& $c===')'
			) {
				$argumentDepthLevel--;
				if ($argumentDepthLevel===0) {
					$modes['inArguments'] = false;
				}
			};
			
			// string detection :
			$dbgStrings = false;//strpos($dbg, "case 'pageOptions'")!==false;
			
			// escaping inside strings and regexps (force this parser to the next character)
			if (
				(
					$modes['inString'] === true
					|| $modes['inRegexp'] === true
				)
				&& $c === '\\'
			) {
				//$i = $i + 1; // jump to AFTER the escaped character! //TODO : +1 or +2??
				
				if (true) { //TEST 
				$sourceToObfuscate .= $c2;
				$i = $i + 1;
				} else {
				$sourceToObfuscate .= $c;
				}
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
			}
			
			
			if (
				$c3 === '\'"\''
				|| $c3 === '"\'"'
			) {
				if ($c3 === '\'"\'') {
					unset ($modesStartI['stringModes']['inDoubleQuoted']);
					$stringModes['inDoubleQuoted'] = false;
				}
				if ($c3 === '"\'"') {
					unset ($modesStartI['stringModes']['inSingleQuoted']);
					$stringModes['inSingleQuoted'] = false;
				}
				$i = $i + 2;
				$sourceToObfuscate .= $c3;
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
			}
			if (
				$c2 === '\'\''
				|| $c2 === '""'
				|| $c2 === '\'"'
				|| $c2 === '"\''
			) {
				
				$i = $i + 1; // continue; does $i++ as well!
				$sourceToObfuscate .= $c2;
				//echo '<pre>666sto=<br/>'; echo htmlentities($sourceToObfuscate); die();
				
				if (
					$c2 === '\'"'
					|| $c2 === '"\''
				) {
					if (
						$c2 === '\'"'
						&& $stringModes['inDoubleQuoted'] === true
					) {
						$stringModes['inSingleQuoted'] = false;
						$stringModes['inDoubleQuoted'] = false;
						unset ($modesStartI['stringModes']['inSingleQuoted']);
						unset ($modesStartI['stringModes']['inDoubleQuoted']);

					} elseif (
						$c2 === '"\''
						&& $stringModes['inSingleQuoted'] == true
					) {
						$stringModes['inSingleQuoted'] = false;
						$stringModes['inDoubleQuoted'] = false;
						unset ($modesStartI['stringModes']['inSingleQuoted']);
						unset ($modesStartI['stringModes']['inDoubleQuoted']);
					} else {
						$stringModes['inSingleQuoted'] = !$stringModes['inSingleQuoted'];
						$stringModes['inDoubleQuoted'] = !$stringModes['inDoubleQuoted'];
						if ($stringModes['inSingleQuoted'] === false) unset ($modesStartI['stringModes']['inSingleQuoted']);
						if ($stringModes['inDoubleQuoted'] === false) unset ($modesStartI['stringModes']['inDoubleQuoted']);
					}

					if (
						$stringModes['inDoubleQuoted'] === false
						&& $stringModes['inSingleQuoted'] === false
					) {
						$modes['inString'] = false;
						unset ($modesStartI['modes']['inString']);
						$modes['inJavascript'] = true;
						$stringModes['mustKeepInSource'] = true;
						$token = '';
					} else {
						$modes['inString'] = true;
						$modesStartI['modes']['inString'] = $i;
						
						$modes['inJavascript'] = false;
					}

				}

				if (false) { //$debugI!==false) {//(strpos($dbg,'this.target')!==false) {// = \'\'; // we dont target="_new" as we\'re using fancybox ')!==false) {
					$dbg1 = array (
						'dbg' => htmlentities($dbg),
						'c2' => $c2,
						'modes' => json_encode($modes),
						'stringModes' => json_encode($stringModes),
						'commentModes' => json_encode($commentModes)
					);
					echo '<pre style="color:purple">666:'; var_dump ($dbg1); echo '</pre>';
				}
				
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
			}
			
				/*if (strpos($dbg,'this.target = \'\'; // we dont target="_new" as we\'re using fancybox ')!==false) {
					$dbg1 = array (
						'dbg' => $dbg,
						'c2' => $c2,
						'modes' => json_encode($modes),
						'stringModes' => json_encode($stringModes),
						'commentModes' => json_encode($commentModes)
					);
					echo '<pre style="color:blue">'; var_dump ($dbg1); echo '</pre>';
				}*/
			
			if (
				$modes['inString'] === false
				&& (
					$c === '\''
					|| $c === '"'
				)
			) {
				$modesStartI['modes']['inString'] = $i;
			}
			
			
			if (
				$modes['inComment']===false
				&& $modes['inRegexp']===false
				&& $stringModes ['inSingleQuoted'] === false
				&& $c === '\''
			) {
			
				$stringModes ['inSingleQuoted'] = true;
				$modesStartI['stringModes']['inSingleQuoted'] = $i;
				$modes['inString'] = true;
				//$modesStartI['modes']['inString'] = $i;
				$modes['inJavascript'] = false;

				
				if ($dbg10) {//if ($modes['inString'] === false) {
					$dbg1 = array (
						'dbg' => htmlentities($dbg),
						'modes' => json_encode($modes),
						'stringModes' => json_encode($stringModes),
						'commentModes' => json_encode($commentModes)
					);
					echo '<pre style="color:green;font-weight:bold;">663:'; var_dump ($dbg1); echo '</pre>';
					//die();
				}
			
				
				//$keep = $stringModes ['mustKeepInSource'] = $this->getTokensAndStrings__mustKeepStringInSource ($source, $i);
				
				
				$sourceToObfuscate .= $c;
				//if ($modes['inString']===false) {
					$keep = FALSE;// $stringModes ['mustKeepInSource'] = $this->getTokensAndStrings__mustKeepStringInSource ($source, $i);
					if (!$keep) {
						$stringsRemovedCount++;
						$stringsRemoved[$stringsRemovedCount] = '';
						$tmp = '`'.$stringsRemovedCount.'`';
						$sourceToObfuscate .= $tmp;
						//$i = $i + 1;
						//continue;
					}
				//}
				
				
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
				
			} elseif (
				$modes['inComment']===false
				&& $modes['inRegexp']===false
				&& $stringModes ['inDoubleQuoted'] === false
				&& $c === '"'
			) {
				$stringModes ['inDoubleQuoted'] = true;
				$modesStartI['stringModes']['inDoubleQuoted'] = $i;
				
				$modes['inString'] = true;
				//$modesStartI['modes']['inString'] = $i;
				
				$modes['inJavascript'] = false;
				
				//$keep = $stringModes ['mustKeepInSource'] = $this->getTokensAndStrings__mustKeepStringInSource ($source, $i);

				if ($dbg10) {
					$dbg1 = array (
						'dbg' => htmlentities($dbg),
						'modes' => json_encode($modes),
						'stringModes' => json_encode($stringModes),
						'commentModes' => json_encode($commentModes)
					);
					echo '<pre style="color:green;font-weight:bold;">663:'; var_dump ($dbg1); echo '</pre>';
				}
				
				
				$sourceToObfuscate .= $c;
				//if ($modes['inString']===false) {
					$keep = false; //$stringModes ['mustKeepInSource'] = $this->getTokensAndStrings__mustKeepStringInSource ($source, $i);
					if (!$keep) {
						$stringsRemovedCount++;
						$stringsRemoved[$stringsRemovedCount] = '';
						$tmp = '`'.$stringsRemovedCount.'`';
						$sourceToObfuscate .= $tmp;
						//$i = $i + 1;
						//continue;
					}
				//}
				
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
				
			} // NOT "elseif"!!	
			
			
			if (
				$modes['inString'] === true
				&& $pastModeOuter['inString'] === false
			) {
				$search = 'data:image/png;base64,';
				if (substr($source, $i+1, strlen($search))==$search) {
					$j = $i + 1;
					$d = substr($source, $j, 1);
					while ( $d !== '\'' && $d !== '"' ) {
						$j++;
						$d = substr($source, $j, 1);
					};
					$stringsRemovedCount++;
					$stringsRemoved[$stringsRemovedCount] = substr ($source, $i, $j-$i);
					$sourceToObfuscate .= '`'.$stringsRemovedCount.'`'.$d;
				$i = $j ;
				}
				
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
			}
			
			
			
			/*
			if (
				$modes['inString'] === true
				&& $stringModes['inSingleQuoted']===true
				&& (
					$c === '\''
					|| $c === '"'
				)
			) {
			
				if ($c==='\'' && $stringModes['inSingleQuoted']===true) {
					$stringModes['inSingleQuoted'] = false;
					$stringModes['inDoubleQuoted'] = false; // TEST experimental
				}
				if ($c==='"' && $stringModes['inDoubleQuoted']===true) $stringModes['inDoubleQuoted'] = false;
				
				
				//$stringModes['inSingleQuoted'] = false;
				//$stringModes['inDoubleQuoted'] = false;
				
				if (false) {
					$dbg1 = array (
						'dbg' => htmlentities($dbg),
						'modes' => json_encode($modes),
						'stringModes' => json_encode($stringModes),
						'commentModes' => json_encode($commentModes)
					);
					echo '<pre style="color:red">665:'; var_dump ($dbg1); echo '</pre>';
				}

				
				//$i = $i + 1;
				$sourceToObfuscate .= $c;
				continue;
			} */
			
			if (
				$modes['inString'] === true
				&& $stringModes['inSingleQuoted']===true
				&& $c === '\''
			) {
				$stringModes['inSingleQuoted'] = false;
				unset ($modesStartI['stringModes']['inSingleQuoted']);
				//$stringModes['inDoubleQuoted'] = false; // TEST experimental
				
				// TEST experimental as switched off //$stringModes['inSingleQuoted'] = false;
				$pastMode = $this->pastModes[$modesStartI['modes']['inString']];
				if (
					$pastMode['stringModes']['inDoubleQuoted'] === false
				) {
					$stringModes['inDoubleQuoted'] = false;
					unset ($modesStartI['stringModes']['inDoubleQuoted']);
				}
				
				$stringModes['inDoubleQuoted'] = false; 
				
				
				$sourceToObfuscate .= $c;
			} elseif (
				$modes['inString'] === true
				&& $stringModes['inDoubleQuoted']===true
				&& $c === '"'
			) {
				// TEST experimental as switched off //$stringModes['inSingleQuoted'] = false;
				$pastMode = $this->pastModes[$modesStartI['modes']['inString']];
				if (strpos($dbg, '2015 2012 March 11')!==false) {
					$x = $modesStartI['modes']['inString'];
					$dbz = substr($source, $x - 100). ' ___ '.substr($source, $x, 1).' ___ '.substr($source,$x+1,100);
					echo '<pre style="color:red;">$dbz='; htmlentities($dbz).'$pastMode='; var_dump ($pastMode); echo'</pre>';
					$dbg10 = true;
				} else {
				$dbg10= false;
				}
				if (
					$pastMode['stringModes']['inSingleQuoted'] === false
				) {
					$stringModes['inSingleQuoted'] = false;
					unset ($modesStartI['stringModes']['inSingleQuoted']);
				}
				
				$stringModes['inDoubleQuoted'] = false; 
				unset ($modesStartI['stringModes']['inDoubleQuoted']);
				
				$sourceToObfuscate .= $c;
			}
			
			if (
				$stringModes['inDoubleQuoted'] === false
				&& $stringModes['inSingleQuoted'] === false
			) {
				$modes['inString'] = false;
				unset ($modesStartI['modes']['inString']);
				
				$modes['inJavascript'] = true;
				$stringModes['mustKeepInSource'] = true;
				$token = '';
			} else {
				$modes['inString'] = true;
				//$modesStartI['modes']['inString'] = $i;
				$modes['inJavascript'] = false;
			}
			

			if (
				$c === '\''
				|| $c === '"'
			) {
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;
			}
			
			
			// regular expression detection:
			
			// new RegExp keyword
			if (
				substr ($source, $i, 8) === 'RegExp ('
				|| substr ($source, $i, 7) === 'RegExp ('
			) {
				// scan ahead to final ); sequence
				$parenthesisLevel = 1;
				if (substr ($source, $i, 8) === 'RegExp (') $j = $i + 8;
				if (substr ($source, $i, 7) === 'RegExp (') $j = $i + 7;
				while ($parenthesisLevel!==0) {
					$d = substr ($source, $j, 1);
					if ($d==='(') $parenthesisLevel++;
					if ($d===')') $parenthesisLevel--;
					$j++;
				}
				$sourceToObfuscate .= substr ($source, $i, $j + 1);
				$i = $i + $j + 1;
				$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
				continue;

			}
			
			// normal inline regular expressions aka var regx = /^bla$/;
			if (
				$modes['inComment']===false
				&& $modes['inRegexp']===false
				&& $modes['inString'] === false
				&& $c === '/'
			) {
				$modes['inRegexp'] = true;
			}
			
			if (
				$modes['inRegexp'] === true
				&& $c === '/'
			) {
				$modes['inRegexp'] = false;
			};
			
			
			
			// HTML detection:
			if (false && $debugI!==false) {
				echo '<pre style="color:red;background:yellow;">$c=';var_dump ($c);echo'</pre>';
			}
			if (
				//$modes['inComment']===false
				//&& $modes['inRegexp']===false
				$modes['inString'] === true
				&& $c === '<'
			) {
				$modes['inHTML'] = true;
				$modes['inJavascript'] = false;
				//echo '<pre style="color:yellow;background:red;">$modes=';var_dump (json_encode($modes));echo'</pre>';
			}
			if (
				//$modes['inComment']===false
				//&& $modes['inRegexp']===false
				$modes['inHTML'] === true
				&& $modes['inString'] === true
				&& $c === '>'
			) {
				$modes['inHTML'] = false;
				//$modes['inJavascript'] = true;
				//echo '<pre style="color:white;background:red;">$modes=';var_dump (json_encode($modes));echo'</pre>';
			}
			
			
			
			
			// token detection :
			if (
				/*$modes['inComment']===false
				&& $modes['inRegexp']===false
				&& $modes['inString'] === false*/
				$modes['inJavascript'] === true
			) {
				if (array_search ($c, $tokenDelimiters)===false) {
					$token .= $c;
				} else {
					$tkn = trim($token);
					if (
						strlen($tkn) > 1
						&& !(
							is_int($tkn)
							|| is_float($tkn)
						)
						&& !(
							substr($tkn, strlen($tkn)-1, 1) === '%'
							&& (
								is_int(substr($tkn, 0, strlen($tkn)-2))
								|| is_float(substr($tkn, 0, strlen($tkn)-2))
							)
						)
						&& strtolower(substr($tkn, strlen($tkn-2), 2)) !== 'px'
					) {
						if (false) {//($tkn=='div') { //$tkn==='\'src') { //if (false) { //if (strpos($dbg,'<span')!==false) {
							$tokens[$tokenCount] = $tkn;
							$tokensDbg[$tokenCount.'__dbg'] = $dbg;
							$tokensDbg[$tokenCount.'__dbg__modes'] = json_encode($modes);
							$tokensDbg[$tokenCount.'__dbg__stringModes'] = json_encode($stringModes);
							
							
							$dbg1 = array (
								'token' => $tkn,
								'dbg' => htmlentities($dbg),
								'modes' => json_encode($modes),
								'stringModes' => json_encode($stringModes),
								'commentModes' => json_encode($commentModes)
							);
							echo '<pre style="color:purple;">'; var_dump ($dbg1); echo '</pre>';
							
							$tokenCount++;
						} else {
							$tokens[] = $tkn;
						}
					}
					$token = '';
				}
			}
			
			if (
				(
					$modes['inJavascript']===true
					|| $modes['inString']===true
					|| $modes['inRegexp']===true
				)
				
			) {
				if ($modes['inString']===true) {
					if ($stringModes['mustKeepInSource']===true) {
						$sourceToObfuscate .= $c;
					} else {
						$stringsRemoved[$stringsRemovedCount] .= $c;
					}
				} else {
					$sourceToObfuscate .= $c;
				}
				
				/*if ($c2==='{}' || $c==='}') {
					echo '<pre style="color:red;font-weight:bold;">123='.htmlentities($sourceToObfuscate).'</pre>';
				};*/

			}

			
			$this->getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes);
			
			
			$dbg1 = array (
				'dbg' => htmlentities($dbg),
				'modes' => json_encode($modes),
				'stringModes' => json_encode($stringModes),
				'commentModes' => json_encode($commentModes)
			);
			if (false) {
				$s = 'seductiveapps.vividControls =';
				if (substr($source, $i, strlen($s))===$s) $debugI = $i;
				if ($debugI!==false && $i>$debugI && $i<$debugI+70) { echo '<pre style="color:orange;font-weight:bold;">$dbg1=';var_dump ($dbg1);echo'</pre>'; }
				if ($debugI!==false && $i>$debugI + 70) die();
			}
		
			
			
			//if (false) {
			if (false) { //strpos($dbg,'fireAppEvent')!==false && strpos($dbg,'1500')!==false) {
						$dbg1 = array (
							'dbg' => htmlentities($dbg),
							'modes' => json_encode($modes),
							'stringModes' => json_encode($stringModes),
							'commentModes' => json_encode($commentModes)
						);
				echo '<pre style="color:red">'; var_dump ($dbg1); echo '</pre>';
			}
			
		}
		
		
		$r = array (
			'tokens' => $tokens,
			'sourceToObfuscate' => $sourceToObfuscate,
			'stringsRemoved' => $stringsRemoved
			//'sourceMinusStrings' => $sourceMinusStrings,
			//'stringsRemoved' => $stringsRemoved
		);
		
		//reportTokens (700, $r['tokens'], 'webappObfuscator_obfuscate__javascript::getTokensAndStrings() : finalized Javascript tokens'); 
		//die(); 
		
		return $r;
	}
	
	private function getTokensAndStrings__recordPastMode ($i, $dbg, $modes, $stringModes, $commentModes) {
		$dbg2 = array (
			'dbg' => $dbg,
			'modes' => $modes,
			'stringModes' => $stringModes,
			'commentModes' => $commentModes
		);
		if (array_key_exists($i-2000, $this->pastModes)) unset ($this->pastModes[$i-2000]);
		$this->pastModes[$i] = $dbg2;
	}
	
	public function getTokensAndStrings__mustKeepStringInSource ($source, $i) {
		$r = (
			substr($source, $i-7, 7) === 'jQuery('
			|| substr($source, $i-8, 8) === 'jQuery ('
			|| substr($source, $i-12, 12) === 'removeClass('
			|| substr($source, $i-13, 13) === 'removeClass ('
			|| substr($source, $i-9, 9) === 'addClass('
			|| substr($source, $i-10, 10) === 'addClass ('
		);
		return $r;
	}
	
	
/*
	private function preprocess__stage2 (&$source) {
		$tmp = $this->phpJSO_strip_strings_and_comments($source); // 2015 july 4th (Rene) : fundamentally flawed i'm afraid (when tested against my own seductiveapps.com sources).
		$r = array(
			'sourcecode' => $this->phpJSO_strip_junk($tmp['source']),
			'stringsRemoved' => $tmp['stringsRemoved']
		);
		return $r;
	}
	
	private function preprocess__stage3 ($source, $stringsRemoved) {
		$tmp = $source;
		$tmp = $this->phpJSO_strip_strings ($tmp);
		//echo '<pre>2222'; var_dump ($stringsRemoved); die();
		$tokens = $this->phpJSO_get_tokens($tmp, $stringsRemoved);
		$r = array (
			'tokens' => $tokens,
			'sourceForObfuscation' => $tmp
		);
		return $r;
	}
*/
	public function phpJSO_restore_strings (&$str, &$strings) {
	/**
	* Place stripped strings back into code
	*/
		$f = function($m) use (&$strings) { 
			/* !!! Outputting all of these will crash chrome.
			$r = array (
				1 => $strings[$m[1]],
				2 => $m[1]
			); var_dump ('webappObfuscator_obfuscate__javascript::phpJSO_restore_strings()'); var_dump ($r); 
			*/
			return isset($strings[$m[1]]) ? $strings[$m[1]] : $m[1];
		};
		$str = preg_replace_callback ('#`([0-9]+)`#', $f, $str);
		return $str;
	}
	
	
	private function phpJSO_strip_junk ($str, $whitespace_only = false) {
		// Remove unneeded spaces and semicolons
		$find = array
		(
			'/([^a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
			'/([^a-zA-Z0-9_$]|^)\s+([a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
			'/([a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
			'/([^a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
			'/([^a-zA-Z0-9_$]|^)\s+([a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
			'/([a-zA-Z0-9_$]|^)\s+([^a-zA-Z0-9_$]|$)/s', // Unneeded spaces between tokens
			'/[\r\n]/s', // Unneeded newlines
			"/\t+/" // replace tabs with spaces
		);
		// Unneeded semicolons
		if (!$whitespace_only)
		{
			$find[] = '/;(\}|$)/si';
		}
		$replace = array
		(
			'$1$2',
			'$1$2',
			'$1$2',
			'$1$2',
			'$1$2',
			'$1$2',
			'',
			' ',
			'$1',
		);
		$str = preg_replace($find, $replace, $str);
		return $str;
	}
	
	public function phpJSO_strip_strings (&$str) {
		$str = preg_replace('#`([0-9]+)`#', '', $str);
		return $str;
	}
	
	public function phpJSO_strip_strings_and_comments (&$str) {
	// 2015 july 4th (Rene) : fundamentally flawed i'm afraid (when tested against my own seductiveapps.com sources).
	
		$num_strings = 0;
		$in_string = $last_quote_pos = $in_comment = $in_regex = false;
		$removed = 0;
		$invalid = array();
		$strings = array();

		
		// Find all occurances of comments and quotes. Then loop through them and parse.
		$quotes_and_comments = $this->phpJSO_sort_occurances($str, array('/', '//', '/*', '*/', '"', "'"));

		// Loop through occurances of quotes and comments
		foreach ($quotes_and_comments as $location => $token)
		{
			// Parse strings
			if ($in_string !== false)
			{
				if ($token == $in_string)
				{
					// First, we'll pull out the string and save it, and replace it with a number.
					$replacement = '`' . $num_strings . '`';
					$string_start_index = $last_quote_pos - $removed;
					$string_length = ($location - $last_quote_pos) + 1;
					
					if ($string_length>3) {
						$strings[$num_strings] = substr($str, $string_start_index, $string_length);
						++$num_strings;

						// Remove the string completely
						$str = substr_replace($str, $replacement, $string_start_index, $string_length);
					
						// Clean up time...
						$removed += $string_length - strlen($replacement);
						$in_string = $last_quote_pos = false;
					}
					
				}
			}
			// Parse multi-line comments
			else if ($in_comment !== false)
			{
				// If it's the end of a comment, replace it with a single space
				// We replace it with a space in case a comment is between two tokens: test/**/test
				if ($token == '*/')
				{
					$comment_start_index = $in_comment - $removed;
					$comment_length = ($location - $in_comment) + 2;
					$str = substr_replace($str, ' ', $comment_start_index, $comment_length);
					$removed += $comment_length - 1;
					$in_comment = false;
				}
			}
			// Parse regex
			else if ($in_regex !== false)
			{
				// Should be end of the regex, unless it's escaped
				// If it is the end... don't do anything except stop parsing
				// We just don't want strings inside of regex to be removed,
				// like: /["']*/ -- VERY bad when mistaken as a string
				if ($token == '/')
				{
					$string_start_index = $in_regex - $removed;
					$string_length = ($location - $in_regex) + 1;
					$in_regex = false;
				}
			}
			else
			{
				// Make sure string hasn't been extracted by another operation...
				if (substr($str, $location - $removed, strlen($token)) != $token)
				{
					continue;
				}
				
				// This string shouldn't have been escaped...
				if ($location && $str[$location - $removed - 1] == '\\')
				{
					continue;
				}
				
				// See what this token is ...
				// Start of multi-line comment?
				if ($token == '/*')
				{
					$in_comment = $location;
				}
				// Start of a string?
				else if ($token == '"' || $token == "'")
				{
					$in_string = $token;
					$last_quote_pos = $location;
				}
				// A single-line comment?
				else if ($token == '//')
				{
					$comment_start_position = $location - $removed;
					$newline_pos = strpos($str, "\n", $comment_start_position);
					$comment_length = ($newline_pos !== false ? $newline_pos - $comment_start_position : $comment_start_position);
					$str = substr_replace($str, '', $comment_start_position, $comment_length);
					$removed += $comment_length;
				}
				// Start of a regex expression?
				// Note that the second part of this conditional fixes a bug: if there
				// is a regex sequence followed by a comment of the EXACT SAME length,
				// it will try to parse the regex sequence a second time...
				else if ($token == '/' && (!isset($quotes_and_comments[$location - 1]) || ($quotes_and_comments[$location - 1] != '//' && $quotes_and_comments[$location - 1] != '*/')))
				{
					// Only start a regex sequence if there was NOT
					// an alphanumeric sequence before.
					// var regex = /pattern/
					// string.match(/pattern/)
					if (preg_match('#[(=]#', $str[$location - $removed - 1]))
					{
						$in_regex = $location;
					}
				}
			}
		}

		// get rid of regular expressions (shove into $strings)
		$matches = array();
		$r = preg_match_all ('#/.*?/[a-zA-Z]*?[\),\.;]#', $str, $matches);
		foreach ($matches[0] as $idx=>$m) {
			if ($m!=='//.') {
				//echo $idx.' : '.htmlentities($m).'<br/>';
				$replacement = '`' . $num_strings . '`';
				$strings[$num_strings] = $m;
				$str = str_replace ($m, $replacement, $str);
				++$num_strings;
			}
		}
		//die();
		
		// get rid of any "about" sub-objects
		$matches = array();
		$r = preg_match_all ('#about\s*\:\s*\{[^}]+\},#', $str, $matches);
		foreach ($matches[0] as $idx=>$m) {
			//if ($m!=='//.') {
				//echo 'REMOVED_ENTIRELY : '.$idx.' : '.htmlentities($m).'<br/>';
				$replacement = '`' . $num_strings . '`';
				//$strings[$num_strings] = $m;
				$str = str_replace ($m, '', $str);
				//++$num_strings;
			//}
		}
		
		$r = array (
			'source' => $str,
			'stringsRemoved' => $strings
		);
		return $r;
	}
	
	private function phpJSO_sort_occurances (&$haystack, $needles) {
	/**
	* Finds all occurances of different strings in the first passed string and sorts
	* them by location. Returns array of locations. The key of each array element is the string
	* index (location) where the string was found; the value is the actual string, as seen below.
	*
	* [18] => "
	* [34] => "
	* [56] => /*
	* [100] => '
	*/
		$locations = array();
		
		foreach ($needles as $needle)
		{
			$pos = -1;
			//$needle_length = strlen($needle);
			while (($pos = @strpos($haystack, $needle, $pos+1)) !== false)
			{
				// Don't save location if string length is 1, and the needle is escaped
				if ($pos && $haystack[$pos - 1] == '\\' && $needle != '*/')
				{
					continue;
				}

				// Save location of needle
				$locations[$pos] = $needle;
			}
		}
		
		ksort($locations);
		
		return $locations;
	}
	
	
	public function phpJSO_get_tokens ($code, $stringsRemoved) {
		//echo '<pre>phpJSO_get_tokens: '; var_dump ($stringsRemoved); 
		global $minTokenLength;
		global $reportStatusGoesToStatusfile;
		$s = &$this->settings;
		$obfuscator = &$s['obfuscator'];
		$wHTML = $obfuscator->getWorker('html');
		$workersHTML = array();
		
		$matches = array();
		
		//echo '<pre>443:'; var_dump ($minTokenLength); die();
		
  		preg_match_all('#([^\s\.;,\(\)\x5b\x5d\x3d\x3a\r\n])([a-zA-Z0-9\_\$]{'.$minTokenLength.',})([\/\x3a\x3d,\(\)\x5b\x5d\s\.;\r\n$])#s', $code, $matches);
		$tks = array();//array_unique($matches[2]);
		foreach ($matches[0] as $k=>$v) {
			$invalid = array ('{', '}', '/', '*', '-', '+', '?', '!', '&', '(', ')', '<', '>', '|');
			if ( array_search ($matches[1][$k], $invalid)===false ) {
				$tks[] = $matches[1][$k].$matches[2][$k];
			} else {
				$tks[] = $matches[2][$k];
			}
		};
		$tks = array_unique ($tks);
		//if ($reportStatusGoesToStatusfile===false) { echo '<pre class="webappObfuscator__findTokens__tokensFound">$tks = '; var_dump ($tks); echo '</pre>'; }
		reportTokens (997, $tks, 'webappObfuscator_obfuscate__javascript::phpJSO_get_tokens():$tks');
		
		$tokens = array();
		//$ignoreList = ignoreList_javascript();
		foreach ($tks as $k => $v) {
			if (
				!is_numeric($v) 
				&& substr($v, 0, 7)!=='http://'
				&& substr($v, 0, 8)!=='https://'
				//&& array_search ($v, $ignoreList)===false // done by webappObfuscator::obfuscate()
			) $tokens = array_merge($tokens, explode('.', $v));
		}
		reportStatus (911, '<p class="webappObfuscator__findTokens__code">webappObfuscator_obfuscate__javascript::phpJSO_get_tokens() : processing<br/>'.htmlentities($code)."</p>");
		reportStatus (911, '<p class="webappObfuscator__findTokens__counts">webappObfuscator_obfuscate__javascript::phpJSO_get_tokens() : before processing $stringsRemoved : '.count($tokens).' tokens found, '.count($stringsRemoved).' $stringsRemoved found</p>');

		if (false) {
			$tokens2 = $tokens;
			usort($tokens2,'sortByStringLength');
			reportTokens (1/*997*/, $tokens2/*$tokens*/, 'webappObfuscator_obfuscate__javascript::phpJSO_get_tokens():$tokens after 2nd foreach loop');
			reportTokens (1/*997*/, $stringsRemoved, 'webappObfuscator_obfuscate__javascript::phpJSO_get_tokens():$stringsRemoved after 2nd foreach loop');
		} else {
			reportTokens (997, $tokens, 'webappObfuscator_obfuscate__javascript::phpJSO_get_tokens():$tokens after 2nd foreach loop');
			reportTokens (997, $stringsRemoved, 'webappObfuscator_obfuscate__javascript::phpJSO_get_tokens():$stringsRemoved after 2nd foreach loop');
		};
		
		$htmlSnippetsCount = 0;
		$c = 0;
		foreach ($stringsRemoved as $k => $v) {
			//var_dump (substr($v, 0, 1));
			if ( 
				substr($v, 0, 1)==='/' // regular expression
				|| substr($v, 1, 1)==='/' // relative URL
			) {
				// we'll ignore these
			} else if (
				substr($v, 0, 1)==='#' 
			) {
				$v1 = substr($v, 1);
				$tokens[] = $v1;
			} else if (
				strpos($v, '/>')!==false
				|| strpos($v, '</')!==false
				|| strpos($v, '">')!==false
				|| strpos($v, '\'>')!==false
				|| strpos($v, '>\'')!==false
				|| strpos($v, '>"')!==false
			) {
				reportStatus (912, '<p class="webappObfuscator__findTokens__code">webappObfuscator_obfuscate__javascript::phpJSO_get_tokens() thinks $stringsRemoved['.$k.'] is HTML<br/>'.htmlentities($v).'</p>');			
			
				$htmlSnippetsCount++;
				$v1 = substr($v, 1, strlen($v)-2);	
				$sources = array (
					'from_javascript'.$htmlSnippetsCount => $v
				);
				$settingsHTML = array(
					'obfuscator' => &$this->settings['obfuscator'],
					'globalSettings'  => &$this->settings['globalSettings']
				);
//				echo '<pre>334'; var_dump ($wHTML->settings); var_dump ($settingsHTML);
				reportStatus (912, '<blockquote>'); //indent output
				$workersHTML[] = $workerHTML = new webappObfuscator_obfuscate__html ($settingsHTML);
				$workerHTML->preprocess ($sources);
				$tokens = array_merge ($tokens, $workerHTML->workData['tokens']);
				reportStatus (912, '<p class="webappObfuscator__findTokens__counts">webappObfuscator_obfuscate__javascript::phpJSO_get_tokens() : $stringsRemoved['.$c.'] ; token-count : '.count($workerHTML->workData['tokens']).'</p>');
				//if ($reportStatusGoesToStatusfile===false) { echo '<pre class="webappObfuscator__findTokens__tokensFound">$workerHTML->workData["tokens"] = '; var_dump ($workerHTML->workData['tokens']); echo '</pre>'; }
				reportTokens (997, $workerHTML->workData['tokens'], 'webappObfuscator_obfuscate__javascript::phpJSO_get_tokens():$workerHTML->workData["tokens"]');
				reportStatus (912, '</blockquote>'); //indent output
			} else if (
				strpos($v, ' ')===false
				&& strpos($v, "\t")===false
				&& strpos($v, "\r")===false
				&& strpos($v, "\n")===false
			) {
				$v1 = substr($v, 1, strlen($v)-2);
				$tokens = array_merge($tokens, explode('.', $v1));
			} 
			$c++;
			//&& array_search ($v, $ignoreList)===false
		}
		reportStatus (910, '<p class="webappObfuscator__findTokens__counts">webappObfuscator_obfuscate__javascript::phpJSO_get_tokens() : '.count($tokens).' tokens found, '.count($stringsRemoved).' stringsRemoved</p>');
		
		//usort($tokens, 'sortByStringLength');
		
		return $tokens;
	}
};

function ignoreList_javascript () {
	$r = array (
			// general
			'', '/*', '*/', '{}', 			

			// javascript core 
			'major', 'minor', 'title', 'parseInt', 'parseFloat', 'constructor', 'toExponential', 'toFixed', 'toLocaleString', 'toPrecision', 'toString', 'valueOf', 'Boolean', 'Integer', 'Float', 'Number', 'String', 'Object', 'Array', 'Infinity', 'NaN', 'undefined', 'decodeURI', 'decodeURIComponent', 'encodeURI', 'encodeURIComponent', 'escape', 'eval', 'isFinite', 'isNaN', 'unescape', 'hasOwnProperty', 'createStyleSheet', 'QUOTA_EXCEEDED_ERR', 'arguments', 'callee', 'caller', 
			'undefined', 'null', 'false', 'true', 'undefined', 'instanceof', 'new', 'typeof', 'var', 'string', 'number', 'delete', 'unset', 'prototype', 'throw', 'Event', 'Error', 'event', 'preventDefault', 'Infinity', 'Date', 'getDate', 'getTime', 'Array', 'Function', 'Object', 'String', 'Image', 'fromCharCode', 'match', 'replace', 'indexOf', 'substr', 'function', 'if', 'else', 'while', 'for', 'as', 'switch', 'case', 'default', 'continue', 'break', 'return', 'try', 'catch', 'this', 'length', 'trim', 'append', 'top', 'left', 'width', 'height', 'css', 'documentElement', 'innerHTML', 'src'
			, 'cookie', 'each', 'alert', 'navigator', 'userAgent', 'console', 'log', 'window', 'push', 'slice', 'concat', 'call', 'apply', 'style', 'color', 'document', 'html', 'href', 'createElement', 'attachEvent', 'detachEvent', 'addEventListener', 'removeEventListener', 'debugger', 'Math', 'abs', 'sin', 'asin', 'pow', 'sqrt', 'PI', 'parentNode', 'removeChild', 'appendChild', 'target', 'remove', 'DOMParser', 'rgba', 'span', 'text', 'in', 'test', 'extend', 'callee', 'caller', 'before', 'random', 'RegExp', 'always', 'progress', 'unbind', 'plugin', 'iframe', 'focus', 'isNaN', 'webgl', 'first', 'title', 'opera', 'value'	, 'input', 'swing', 'default', 'delete', 'void', 'with', 'event', 
			'naturalWidth', 'naturalHeight', 'or', 'and', 'use strict', 
			
			// reserved by javascript for future extensions to ecmascript (javascript language)
			'class', 'enum', 'extends', 'super', 'const', 'export', 'import', 'implements', 'let', 'private', 'public', 'yield', 'interface', 'package', 'protected', 'static', 
			
			// firefox error 
			'Error', 'EvalError', 'InternalError', 'RangeError', 'ReferenceError', 'SyntaxError', 'TypeError', 'URIError', 'columnNumber', 'fileName', 'lineNumber', 'message', 'name', 'stack', 'toSource', 'toString', 
			
			// internet explorer error 
			'Error', 'constructor', 'prototype', 'description', 'message', 'name', 'number', 'stack', 'stackTraceLimit', 'toString', 'valueOf', // more at https://msdn.microsoft.com/en-us/library/htbw4ywd(v=vs.94).aspx
			
			// try..catch.. 
			'stack', 'throw', 'try', 'catch', 'finally', 
			
			// google excanvas 
			'getContext', 
			
			
			// DOMparser 
			'DOMParser', 'parseFromString', 'async', 'loadXML', 
			
			// XMLHttpRequest 
			'XMLHttpRequest', 'abort', 'getAllResponseHeaders', 'getResponseHeader', 'open', 'send', 'setRequestHeader', 'onreadystatechange', 'readyState', 'responseText', 'responseXML', 'status', 'statusText', 
			
			// Boolean Object 
			'toSource', 'toString', 'valueOf', 
			
			// String HTML wrappers 
			'anchor', 'big', 'blink', 'bold', 'fixed', 'fontcolor', 'fontsize', 'italics', 'link', 'small', 'strike', 'sub', 'sup', 
		
			// Object 
			'constructor', 'length', 'prototype', 'assign', 'create', 'defineProperty', 'defineProperties', 'freeze', 'getOwnPropertyDescriptor', 'getOwnPropertyNames', 'getOwnPropertySymbols', 'getPrototypeOf', 'is', 'isExtensible', 'isFrozen', 'isSealed', 'keys', 'observe', 'preventExtensions', 'seal', 'setPrototypeOf',
			
			// Array Object 
			'concat', 'indexOf', 'join', 'lastIndexOf', 'pop', 'push', 'reverse', 'shift', 'slice', 'sort', 'splice', 'toString', 'unshift', 'valueOf',
		
			// String Object 
			'charAt', 'charCodeAt', 'concat', 'fromCharCode', 'indexOf', 'lastIndexOf', 'localeCompare', 'match', 'replace', 'search', 'slice', 'split', 'substr', 'substring', 'toLocaleLowerCase', 'toLocaleUpperCase', 'toLowerCase', 'toString', 'toUpperCase', 'trim', 'valueOf',
			
			// Date Object
			'getDate', 'getDay', 'getFullYear', 'getHours', 'getMilliseconds', 'getMinutes', 'getMonth', 'getSeconds', 'getTime', 'getTimezoneOffset', 'getUTCDate', 'getUTCDay', 'getUTCFullYear', 'getUTCHours', 'getUTCMilliseconds', 'getUTCMinutes', 'getURCMonth', 'getUTCSeconds', 'getYear', 'parse', 'setDate', 'setFullYear', 'setHours', 'setMilliseconds', 'setMinutes', 'setMonth', 'setSeconds', 'setTime', 'setUTCDate', 'setUTCFullYear', 'setUTCHours', 'setUTCMilliseconds', 'setUTCMinutes', 'setUTCMonth', 'setUTCSeconds', 'setYear', 'toDateString', 'toGMTString', 'toISOString', 'toJSON', 'toLocaleDateString', 'toLocaleTimeString', 'toLocaleString', 'toString', 'toTimeString', 'toUTCString', 'UTC', 'valueOf', 'locale', 
			
			// Date.locale extension 
				'en', 
					'month_names', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December', 
					'month_names_short', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec', 
			
			// Math Object
			'LN2', 'LN10', 'LOG2E', 'LOG10E', 'PI', 'SQRT1_2', 'SQRT2', 'abs', 'acos', 'asin', 'atan', 'atan2', 'ceil', 'cos', 'exp', 'floor', 'log', 'max', 'min', 'pow', 'random', 'round', 'sin', 'sqrt', 'tan',
			
			// console Object (CHROME) 
			'assert', 'clear', 'count', 'debug', 'dir', 'dirxml', 'error', 'group', 'groupCollapsed', 'groupEnd', 'info', 'log', 'profile', 'profileEnd', 'time', 'timeEnd', 'timeStamp', 'trace', 'warn', 'debugger',
			
			// HTMLElement 
			'accessKey', 'addEventListener', 'appendChild', 'attributes', 'blur', 'childElementCount', 'childNodes', 'children', 'classList', 'className', 'click', 'clientHeight', 'clientLeft', 'clientTop', 'clientWidth', 'coneNode', 'compareDocumentPosition', 'contains', 'contentEditable', 'dir', 'firstChild', 'firstElementChild', 'focus', 'getAttribute', 'getAttributeNode', 'getElementsByClassName', 'getElementsByTagName', 'getFeature', 'hasAttribute', 'hasAttributes', 'hasChildNodes', 'id', 'innerHTML', 'insertBefore', 'isContentEditable', 'isDefaultNamespace', 'isEqualNode', 'isSameNode', 'isSupported', 'lang', 'lastChild', 'lastElementChild', 'namespaceURI', 'nextSibling', 'nextElementSibling', 'nodeName', 'nodeType', 'nodeValue', 'normalize', 'offsetHeight', 'offsetWidth', 'offsetLeft', 'offsetParent', 'offsetTop', 'ownerDocument', 'parentNode', 'parentElement', 'previousSibling', 'previousElementSibling', 'querySelector', 'querySelectorAll', 'removeAttribute', 'removeAttributeNode', 'removeChild', 'replaceChild', 'removeEventListener', 'scrollHeight', 'scrollLeft', 'scrollTop', 'scrollWidth', 'setAttribute', 'setAttributeNode', 'style', 'tabIndex', 'tagName', 'textContent', 'title', 'toString', 'item', 'length',


			// HTML events 
			'onload', 'onclick', 'onmousemove', 'onmouseenter', 'onmouseout', 'onmouseover', 'onmousewheel', 'onwheel', 'oncontextmenu', 'ondblclick', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup', 'onabort', 'onbeforeunload', 'onerror', 'onhashchange', 'onload', 'onpageshow', 'onpagehide', 'onresize', 'onscroll', 'onunload', 'onblur', 'onchange', 'onfocus', 'onfocusin', 'onfocusout', 'oninput', 'oninvalid', 'onreset', 'onselect', 'onsubmit', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'oncopy', 'oncut', 'onpaste', 'onafterprint', 'onbeforeprint', 'onabort', 'oncanplay', 'oncanplaythrough', 'ondurationchange', 'onemptied', 'onended', 'onerror', 'onloadeddata', 'onloadedmetadata', 'onloadstart', 'onpause', 'onplay', 'onplaying', 'onprogress', 'onratechange', 'onseeked', 'onseeking', 'onstalled', 'onsuspend', 'ontimeupdate', 'onvolumechange', 'onwaiting', 'animationend', 'animationiteration', 'animationstart', 'transitioned', 'onerror', 'onmessage', 'onopen', 'onmessage', 'ononline', 'onoffline', 'onpopstate', 'onshow', 'onstorage', 'ontoggle', 'onwheel', 'ontouchcancel', 'ontouchend', 'ontouchmove', 'ontouchstart', 'CAPTURING_PHASE', 'AT_TARGET', 'BUBBLING_PHASE', 'touches', 'changedTouches', 
			
			// HTML Event Object 
			'Event', 'bubbles', 'cancelable', 'currentTarget', 'defaultPrevented', 'eventPhase', 'explicitOriginalTarget', 'originalTarget', 'target', 'timestamp', 'timeStamp', 'type', 'isTrusted', 'initEvent', 'preventBubble', 'preventCapture', 'preventDefault', 'stopImmediatePropagation', 'stopPropagation', 'getPreventDefault', 
			
			// HTML UIEvent 
			'UIEvent', 'cancelBubble', 'detail', 'isChar', 'layerX', 'layerY', 'pageX', 'pageY', 'view', 'which', 'initUIEvent', 
			
			// HTML MouseEvent 
			'MouseEvent', 'altKey', 'button', 'buttons', 'clientX', 'clientY', 'ctrlKey', 'metaKey', 'movementX', 'movementY', 'region', 'relatedTarget', 'screenX', 'screenY', 'shiftKey', 'which', 'mozPressure', 'mozInputSource', 'MOZ_SOURCE_UNKNOWN', 'MOZ_SOURCE_MOUSE', 'MOZ_SOURCE_PEN', 'MOZ_SOURCE_ERASER', 'MOZ_SOURCE_CURSOR', 'MOZ_SOURCE_TOUCH', 'MOZ_SOURCE_KEYBOARD', 'getModifierState', 'initMouseEvent', 
			
			// HTML WheelEvent 
			'WheelEvent', 'deltaX', 'deltaY', 'deltaZ', 'deltaMode', 'DOM_DELTA_PIXEL', 'DOM_DELTA_LINE', 'DOM_DELTA_PAGE', 'wheelDelta', 
			
			// HTML .style 
			'style', 'css', 'alignContent', 'alignItems', 'alignSelf', 'animation', 'animationDelay', 'animationDirection', 'animationDuration', 'animationFillMode', 'animationIterationCount', 'animationName', 'animationTimingFunction', 'animationPlayState', 'background', 'backgroundAttachment', 'backgroundColor', 'backgroundImage', 'backgroundPosition', 'backgroundRepeat', 'backgroundClip', 'backgroundOrigin', 'backgroundSize', 'backfaceVisibility', 'border', 'borderBottom', 'borderBottomColor', 'borderBottomLeftRadius', 'borderBottomRightRadius', 'borderBottomStyle', 'borderBottomWidth', 'borderCollapse', 'borderColor', 'borderImage', 'borderImageOutset', 'borderImageRepeat', 'borderImageSlice', 'borderImageSource', 'borderImageWidth', 'borderLeft', 'borderLeftColor', 'borderLeftStyle', 'borderLeftWidth', 'borderRadius', 'borderRight', 'borderRightColor', 'borderRightStyle', 'borderRightWidth', 'borderSpacing', 'borderStyle', 'borderTop', 'borderTopColor', 'borderTopLeftRadius', 'borderTopRightRadius', 'borderTopStyle', 'borderTopWidth', 'borderWidth', 'bottom', 'boxDecorationBreak', 'boxShadow', 'boxSizing', 'captionSide', 'clear', 'clip', 'color', 'columnCount', 'columnFill', 'columnGap', 'columnRule', 'columnRuleColor', 'columnRuleStyle', 'columnRuleWidth', 'columns', 'columnSpan', 'columnWidth', 'content', 'counterIncrement', 'counterReset', 'cursor', 'direction', 'display', 'emptyCells', 'flex', 'flexBasis', 'flexDirection', 'flexFlow', 'flewGrow', 'flexShrink', 'flexWrap', 'cssFloat', 'font', 'fontFamily', 'fontSize', 'fontStyle', 'fontVariant', 'fontSizeAdjust', 'fontStretch', 'hangingPunctuation', 'height', 'hyphens', 'icon', 'imageOrientation', 'justifyContent', 'left', 'letterSpacing', 'lineHeight', 'listStyle', 'listStyleImage', 'listStylePosition', 'listStyleType', 'margin', 'marginBottom', 'marginLeft', 'marginRight', 'marginTop', 'maxHeight', 'maxWidth', 'minHeight', 'minWidth', 'navDown', 'navIndex', 'navLeft', 'navRight', 'navUp', 'opacity', 'order', 'orphans', 'outline', 'outlineColor', 'outlineOffset', 'outlineStyle', 'outlineWidth', 'overflow', 'overflowX', 'overflowY', 'padding', 'paddingBottom', 'paddingLeft', 'paddingRight', 'paddingTop', 'pageBreakAfter', 'pageBreakBefore', 'pageBreakInside', 'perspective', 'perspectiveOrigin', 'position', 'quotes', 'resize', 'right', 'tableLayout', 'tabSize', 'textAlign', 'textAlignLast', 'textDecoration', 'textDecorationColor', 'textDecorationLine', 'textDecorationStyle', 'textIndent', 'textJustify', 'textOverflow', 'textShadow', 'textTransform' ,'top', 'transform', 'transformOrigin', 'transformStyle', 'transition', 'transitionProperty', 'transitionDuration', 'transitionTimingFunction', 'transitionDelay', 'unicodeBidi', 'verticalAlign', 'visibility', 'whiteSpace', 'width', 'wordBreak', 'wordSpacing', 'wordWrap', 'widows', 'zIndex', 
			
			// Navigator Object 
			'navigator', 'appCodeName', 'appName', 'appVersion', 'cookieEnabled', 'geolocation', 'language', 'onLine', 'platform', 'product', 'userAgent', 'javaEnabled', 'taintEnabled',
			
			// Screen Object 
			'Screen', 'availHeight', 'availWidth', 'colorDepth', 'height', 'pixelDepth', 'width', 
			
			// History Object (BROWSER) 
			'length', 'back', 'forward', 'go', 
			
			// Location Object 
			'hash', 'host', 'hostname', 'href', 'origin', 'pathname', 'port', 'protocol', 'search', 'assign', 'reload', 'replace', 			
			
			// Window Object 
			'closed', 'defaultStatus', 'document', 'frameElement', 'frames', 'history', 'innerHeight', 'innerWidth', 'length', 'location', 'name', 'navigator', 'opener', 'outerHeight', 'outerWidth', 'pageXOffset', 'pageYOffset', 'parent', 'screen', 'screenLeft', 'screenTop', 'screenX', 'screenY', 'scrollX', 'scrollY', 'self', 'status', 'top', 'alert', 'atob', 'blur', 'btoa', 'clearInterval', 'clearTimeout', 'close', 'confirm', 'createPopup', 'focus', 'moveBy', 'moveTo', 'open', 'print', 'prompt', 'resizeBy', 'resizeTo', 'scroll', 'scrollBy', 'scrollTo', 'setInterval', 'setTimeout', 'stop',
			
			// Document Object 
			'activeElement', 'addEventListener', 'adoptNode', 'anchors', 'applets', 'baseURI', 'body', 'close', 'cookie', 'createAttribute', 'createComment', 'createDocumentFragment', 'createElement', 'createTextNode', 'doctype', 'documentElement', 'documentMode', 'documentURI', 'domain', 'domConfig', 'embeds', 'forms', 'getElementById', 'getElementsByClassName', 'getElementsByName', 'getElementsByTagName', 'hasFocus', 'head', 'images', 'implementation', 'importNode', 'inputEncoding', 'lastModified', 'links', 'normalize', 'normalizeDocument', 'open', 'querySelector', 'querySelectorAll', 'readyState', 'referrer', 'removeEventListener', 'renameNode', 'scripts', 'strictErrorChecking', 'title', 'URL', 'write', 'writeln', 'attributes', 'hasAttributes', 'nextSibling', 'nodeName', 'nodeType', 'nodeValue', 'ownerDocument', 'ownerElement', 'parentNode', 'previousSibling', 'textContent',
			
			// RegExp Object 
			'constructor', 'global', 'ignoreCase', 'lastIndex', 'multiline', 'source', 'compile', 'exec', 'test', 'toString',
			
			// jQuery
			'jQuery', 'History', 'getState', 'add', 'addBack', 'addClass', 'after', 'ajaxComplete', 'ajaxError', 'ajaxSend', 'ajaxStart', 'ajaxStop', 'ajaxSuccess', 'andSelf', 'animate', 'append', 'appendTo', 'attr', 'before', 'bind', 'blur', 'callbacks', 'add', 'disable', 'disabled', 'emtpy', 'fire', 'fired', 'fireWith', 'has', 'lock', 'locked', 'remove', 'change', 'children', 'clearQueue', 'click', 'clone', 'closest', 'contents', 'context', 'css', 'data', 'dblclick', 'deferred', 'always', 'done', 'fail', 'isRejected', 'isResolved', 'notify', 'notifyWith', 'pipe', 'progress', 'promise', 'reject', 'rejectWith', 'resolve', 'resolveWith', 'state', 'then', 'delay', 'delegate', 'dequeue', 'detach', 'die', 'each', 'empty', 'end', 'eq', 'error', 'event', 'currentTarget', 'data', 'delegateTarget', 'isDefaultPrevented', 'isImmediatePropagationStopped', 'isPropagationStopped', 'metaKey', 'namespace', 'pageX', 'pageY', 'preventDefault', 'relatedTarget', 'result', 'stopImmediatePropagation', 'stopPropagation', 'target', 'timestamp', 'type', 'which', 'fadeIn', 'fadeOut', 'fadeTo', 'fadeToggle', 'filter', 'find', 'finish', 'first', 'focus','focusin', 'focusout', 'get', 'has', 'hasClass', 'height', 'hide', 'hover', 'html', 'index', 'innerHeight', 'outerHeight', 'innerWidth', 'outerWidth', 'insertAfter', 'insertBefore', 'is', 'jQuery', 'ajax', 'ajaxPrefilter', 'ajaxSetup', 'ajaxTransport', 'boxModel', 'browser', 'Callbacks', 'contains', 'cssHooks', 'cssNumber', 'data', 'Deferred', 'dequeue', 'each', 'error', 'extend', 'fn', 'interval', 'off', 'get', 'getJSON', 'getScript', 'globalEval', 'grep', 'hasData', 'holdReady', 'inArray', 'isArray', 'isEmptyObject', 'isFunction', 'isNumeric', 'isPlainObject', 'isWindow', 'isXMLDoc', 'makeArray', 'map', 'merge', 'noConflict', 'noop', 'now', 'param', 'parseHTML', 'parseJSON', 'parseXML', 'post', 'proxy', 'queue', 'removeData', 'sub', 'support', 'trim', 'type', 'unique', 'when', 'keydown', 'keypress', 'keyup', 'last', 'length', 'live', 'load', 'map', 'mousedown', 'mouseenter', 'mouseleave', 'mousemove', 'mouseout', 'mouseover', 'mouseup', 'next', 'nextAll', 'nextUntil', 'not', 'off', 'offset', 'offsetParent', 'on', 'one', 'parent', 'parents', 'parentsUntil', 'position', 'prepend', 'prependTo', 'prev', 'prevAll', 'prevUntil', 'promise', 'prop', 'pushStack', 'queueu', 'ready', 'remove', 'removeAttr', 'removeClass', 'removeData', 'removeProp', 'replaceAll', 'replaceWith', 'resize', 'scroll', 'scrollLeft', 'scrollTop', 'select', '.serialize', 'serializeArray', 'show', 'siblings', 'size', 'slice', 'slideDown', 'slideToggle', 'slideUp', 'stop', 'submit', 'text', 'toArray', 'toggle', 'toggleClass', 'trigger', 'triggerHandler', 'unbind', 'undelegate', 'unload', 'unwrap', 'val', 'width', 'wrap', 'wrapAll', 'wrapInner', 'Tween', /*'tween',*/ 'init', 'cur', 'run', 'propHooks', '_default', 'get', 'set', 'scrollTop', 'scrollLeft', 'easing', 'linear', 'swing', 'fx', 'step', 'fn', 'noop', 'isPlainObject', 'isReady', 'expando', 'error', 'isWindow', 'isEmptyObject', 'type', 'globalEval', 'camelCase', 'nodeName', 'each', 'trim', 'makeArray', 'inArray', 'merge', 'grep', 'map', 'guid', 'proxy', 'now', 'support', 'Sizzle', 'isXML', 'setDocument', 'matches', 'matchesSelector', 'contains', 'attr', 'uniqueSort', 'getText', 'selectors', 'tokenize', 'done', 'duration', 'ajax', 'url', 'settings', 'accepts', 'async', 'beforeSend', 'cache', 'complete', 'contents', 'contentType', 'context', 'converters', 'crossDomain', 'data', 'dataFilter', 'dataType', 'error', 'global', 'headers', 'ifModified', 'isLocal', 'jsonp', 'jsonpCallback', 'method', 'mimeType', 'password', 'processData', 'scriptCharset', 'statusCode', 'success', 'timeout', 'traditional', 'type', 'url', 'username', 'xhr', 'xhrFields', 'jqXHR', 'done', 'fail', 'always', 'then', 'GET', 'XML', 'DOM', 

			//jquery history 
			'History', 'Adapter', 'bind', 'stateChange', 'pushState', 'originalEvent', 'JSON', 'sessionStorage', 'setItem', 'removeItem', 

			// tinyMCE 3.x 
			'tinyMCE', 'mode', 'theme', 'plugins', 'skin', 'init_instance_callback', 'theme_advanced_buttons1', 'theme_advanced_buttons2', 'theme_advanced_buttons3', 'theme_advanced_buttons4', 'theme_advanced_toolbar_location', 'theme_advanced_toolbar_align', 'font_size_style_values', 'keep_style', 'content_css', 'editor_css', 'inline_styles', 'theme_advanced_resize_horizontal', 'theme_advanced_resizing', 'apply_source_formatting', 'convert_fonts_to_spans', 'get', 'getContent', 
			
			// Canvas 
			'fillStyle', 'strokeStyle', 'shadowColor', 'shadowBlur', 'shadowOffsetX', 'shadowOffsetY', 'createLinearGradient', 'createPattern', 'createRadialGradient', 'addColorStop', 'lineCap', 'lineJoin', 'lineWidth', 'miterLimit', 'rect', 'fillRect', 'strokeRect', 'clearRect', 'fill', 'stroke', 'beginPath', 'moveTo', 'closePath', 'lineTo', 'clip', 'quadraticCurveTo', 'bezierCurveTo', 'arc', 'arcTo', 'isPointInPath', 'scale', 'rotate', 'translate', 'transform', 'setTransform', 'font', 'textAlign', 'textBaseline', 'fillText', 'strokeText', 'measureText', 'drawImage', 'width', 'height', 'data', 'createImageData', 'getImageData', 'putImageData', 'globalAlpha', 'globalCompositeOperation', 'save', 'restore', 'createEvent', 'getContext', 'toDataURL',
			
			// colors-list (saColorGradients-1.0.0.source.js) 
			'AliceBlue', 'AntiqueWhite','Aqua', 'Aquamarine', 'Azure', 'Beige', 'Bisque', 'Black', 'BlanchedAlmond', 'Blue', 'BlueViolet', 'Brown', 'BurlyWood', 'CadetBlue', 'Chartreuse', 'Chocolate', 'Coral', 'CornflowerBlue', 'Cornsilk', 'Crimson', 'Cyan', 'DarkBlue', 'DarkCyan', 'DarkGoldenRod', 'DarkGray', 'DarkGreen', 'DarkKhaki', 'DarkMagenta', 'DarkOliveGreen','DarkOrange', 'DarkOrchid', 'DarkRed', 'DarkSalmon', 'DarkSeaGreen', 'DarkSlateBlue', 'DarkSlateGray', 'DarkTurquoise', 'DarkViolet', 'DeepPink', 'DeepSkyBlue', 'DimGray', 'DodgerBlue', 'FireBrick', 'FloralWhite', 'ForestGreen', 'Fuchsia', 'Gainsboro', 'GhostWhite', 'Gold', 'GoldenRod', 'Gray', 'Green', 'GreenYellow', 'HoneyDew', 'HotPink', 'IndianRed', 'Indigo', 'Ivory', 'Khaki', 'Lavender', 'LavenderBlush', 'LawnGreen', 'LemonChiffon', 'LightBlue', 'LightCoral', 'LightCyan', 'LightGoldenRodYellow', 'LightGrey', 'LightGreen', 'LightPink', 'LightSalmon', 'LightSeaGreen', 'LightSkyBlue', 'LightSlateGray', 'LightSteelBlue', 'LightYellow', 'Lime', 'LimeGreen', 'Linen', 'Magenta', 'Maroon', 'MediumAquaMarine', 'MediumBlue', 'MediumOrchid', 'MediumPurple', 'MediumSeaGreen', 'MediumSlateBlue', 'MediumSpringGreen', 'MediumTurquoise', 'MediumVioletRed', 'MidnightBlue', 'MintCream', 'MistyRose', 'Moccasin', 'NavajoWhite', 'Navy', 'OldLace', 'Olive', 'OliveDrab', 'Orange', 'OrangeRed', 'Orchid', 'PaleGoldenRod', 'PaleGreen', 'PaleTurquoise', 'PaleVioletRed', 'PapayaWhip', 'PeachPuff', 'Peru', 'Pink', 'Plum', 'PowderBlue', 'Purple', 'Red', 'RosyBrown', 'RoyalBlue', 'SaddleBrown', 'Salmon', 'SandyBrown', 'SeaGreen', 'SeaShell', 'Sienna', 'Silver', 'SkyBlue', 'SlateBlue', 'SlateGray', 'Snow', 'SpringGreen', 'SteelBlue', 'Tan', 'Teal', 'Thistle', 'Tomato', 'Turquoise', 'Violet', 'Wheat', 'White', 'WhiteSmoke', 'Yellow', 'YellowGreen'
); return array_unique($r);
}
	
?>
