<?php
class webappObfuscator_obfuscate__javascript {

	public $settings = null;
	public $workData = array();
	
	public function __construct ($settings) {
		$this->settings = $settings;
	}
	
	public function getOutput () {
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;

		$wd['output']['stages'][1] = $this->obfuscate__stage0__node ('', '', $wd);
		
		return $wd['output']['stages'][1];
		
	}
	
	public function writeOutputToDisk ($codePaths=null) {
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;
		$sourcesURLs = &$gs['sources']['urls'];

		if (!array_key_exists('output', $wd)) {
			return badResult (E_USER_ERROR, array(
				'msg' => 'webappObfuscator_obfuscate__javascript::writeOutputToDisk() : No output to write to disk --- array_key_exists("output", $wd)===false',
				'$wd' => '$wd'
			));
		}
		
		// the individual files from $sourcesURLs, obfuscated, written to $codePath
		// as a subdirectory structure that mirrors the URL folder structure
		$wd['output']['stages'][1] = $obfuscator->writeOutput__stage0__walk (
			'javascript', $codePaths, '', $wd['output']['stages'][0], $sourcesURLs['javascript']
		);
		
		$wd['output__concatenated']['stages'][1] = $obfuscator->writeOutput__stage0__walk (
			'javascript', $codePaths, '', $wd['output__concatenated']['stages'][0]
		);
		
		//echo '<pre style="color:orange;background:blue;">wJavascript->writeOutputToDisk $wd["output__concatenated"]["stages"][1]='; var_dump ($wd['output__concatenated']['stages'][1]); echo '</pre>';
		//echo '<pre style="color:orange;background:blue;">wJavascript->writeOutputToDisk $wd["output"]='; var_dump ($wd['output']); echo '</pre>';
		
		return 'results are in $my->workData["output"]["stages"][1] and $my->workData["output__concatenated"]["stages"][1]';
	}
	
	
	public function obfuscate () {
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;

		if (
			array_key_exists ('sourcesType', $gs)
			&& (
				$gs['sourcesType'] !== 'mixed'
				&& $gs['sourcesType'] !== 'javascript'
			)
		) return false;
		
		
		$wd['output'] = array(
			'stages' => array (
				0 => array()
			)
		);
		$wd['output__concatenated'] = array(
			'stages' => array (
				0 => array()
			)
		);
		
		//echo '<pre>11='; var_dump ($wd); echo '</pre>';
		//echo '<pre>12='; var_dump ($wd['sources']['stages'][2]); echo '</pre>'; //die();
	
		
		// here's your actual output :
		$wd['output']['stages'][0] = $this->obfuscate__stage0__walk ('', $wd['sources']['stages'][2]);
		
		// here's the concatenated output (as a multi-level array as per $sources structure, 
		// and concatenated at the level of each sub-array in $sources that contains only URLs 
			// (for all those URLs' obfuscated output)
		$wd['output__concatenated']['stages'][0] = $this->obfuscateConcatenated__stage0__walk ('', $wd['output']['stages'][0]);
		//echo '<pre>321='; var_dump ($wd['output__concatenated']); die();

		//echo '<pre style="color:orange;background:blue">wJavascript->obfuscate $wd='; var_dump ($wd); echo '</pre>'; 
		
	}
	
	private function obfuscateConcatenated__stage0__walk ($path, $sources) {
		$r = array();
		
		foreach ($sources as $k => $v) {
			if (is_string($v)) {
				//if (!array_key_exists($k,$r)) $r[$k] = '';
				if (is_array($r)) $r = '';
				$r .= $v;
			} elseif (is_array($v)) {
				$r[$k] = $this->obfuscateConcatenated__stage0__walk ($path.'/'.$k, $sources[$k]);
			}
		}
		
		return $r;
	}

	private function obfuscate__stage0__walk ($path, $sources) {
		$r = array();
		//echo '$sources=<pre>'; var_dump ($sources);echo'</pre>'; 
		//echo '$path=<pre>'; var_dump ($path); echo '</pre>';
		//DONT WORK if (is_array($sources)) {
		foreach ($sources as $k => $v) {
			if (is_array($v)) {
				/*if (array_key_exists('tokens',$v)) { // won't output javascripts with this enabled.
				  continue;
				} else*/if (array_key_exists('sourceForObfuscation', $v)) {
                                        reportStatus (600, '<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 1 (2 stages total) - processing '.formatSourcepath($path,$k).'</p>');
                                        //echo 'node $path=<pre>'; var_dump ($path); echo '</pre>';
                                        //echo 'node $k=<pre>'; var_dump ($k); echo '</pre>';
					$r[$k] = $this->obfuscate__stage0__node ($path, $k, $v);
					//echo '<pre>15.1='; var_dump ($k); var_dump ($r[$k]); echo'</pre>'; 
				} else {
                                        //echo 'walk $path=<pre>'; var_dump ($path); echo '</pre>';
                                        //echo 'walk $k=<pre>'; var_dump ($k); echo '</pre>';
					$r[$k] = $this->obfuscate__stage0__walk ($path.'/'.$k, $sources[$k]);
					//echo '<pre>15.1.1='; var_dump($k); var_dump ($r[$k]); echo '</pre>';
				}
			} elseif (is_string($v)) {
                            reportStatus (600, '<p class="webappObfuscator__process__detail">Obfuscating Javascript - stage 1 (2 stages total) - processing '.formatSourcepath($path,$k).'</p>');
                            $r[$k] = $this->obfuscate__stage0__node ($path, $k, $v);
			  //echo '<pre>15.2='; var_dump ($k); var_dump ($v); var_dump ($r[$k]); echo '</pre>';
			}
		}
		//}
		
		return $r;
	}

	public function obfuscate__stage0__node ($path, $k, $src) {
		$completePath = $path.'/'.$k;
		
		//echo '$src=<pre>'; var_dump ($src); echo '</pre>'; die();
		//echo 'node() $path=<pre>'; var_dump ($path); echo '</pre>';
		//echo 'node() $k=<pre>'; var_dump ($k); echo '</pre>';
		
		//echo '<pre>32510.0='; var_dump(htmlentities($src['sourceForObfuscation'])); echo '</pre>';
		//echo '103.1 - wJavascript->obfuscate__stage0__node() - $src=<pre>'; var_dump ($completePath); var_dump ($src); echo  '</pre>';
		
		/* whats in $src? this :
		$r = $this->getTokensAndStrings($source);
			$r = array (
				'tokens' => $tokens,
				'sourceForObfuscation' => $sourceToObfuscate,
				'stringsRemoved' => $stringsRemoved
				//'sourceMinusStrings' => $sourceMinusStrings,
				//'stringsRemoved' => $stringsRemoved
			);
		*/
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		
		if (
		  array_key_exists('tokens__css_html', $obfuscator->workData)
		  && is_array($obfuscator->workData['tokens__css_html'])
		) {
		  $tokensCSShtml = $obfuscator->workData['tokens__css_html'];
		} else {
		  $tokensCSShtml = array();
		}
		$allTokens = $obfuscator->workData['tokens'];
		/*$allTokensFixed = array();
		
		foreach ($allTokens as $k => $v) {
		  $allTokensFixed[$k] = '#'.$v.'#';
		}
		
		$allTokens = $allTokensFixed;
		*/
		
		//$wd['src'] = $src;
		

		$mangleStrings = false; // MANGLING (BEFORE-OBFUSCATING) DOES NOT WORK
		if (is_string($src)) {
                    $src = array (
                        'sourceForObfuscation' => $src 
                    );
		} elseif (!is_array($src)) {
		  $src = array();
		}
		
		if (!array_key_exists('stringsRemoved', $src)) {
		  $src['stringsRemoved'] = array();
		} else {
		  if ($mangleStrings) {
			  $stringsRemoved = $obfuscator->mangleStrings ($src['stringsRemoved'], true);
		  } else {
			  $stringsRemoved = $src['stringsRemoved'];
		  }
		}



		$obfuscateStrings = true; // DONT touch.
		if ($obfuscateStrings) {
			
			/* MOVED TO webappObfuscator::phpJSO_replace_tokens() :
			$stringsToObfuscate = array();
			$stringsToLeaveUntouched = array();
			foreach ($src['stringsRemoved'] as $k => $v) {
				if (
					stripos($v, 'http://')===false
					&& stripos ($v, 'https://')===false
					
					// exclude any URLs leading to scripts etc from obfuscation.
					&& 0 === preg_match ('#js|json|php|html$#', $v)
				) {
					$stringsToObfuscate[] = $obfuscator->phpJSO_replace_tokens ($allTokens, $v);;// does NOT work : str_replace(array_keys($tokensCSShtml), array_values($tokensCSShtml), $v);
				} else {
					$stringsToObfuscate[] = $v;
				}
			}
			*/

			/* FAILED fork :
			//reportTokens (1, $stringsToObfuscate, '4000 javascript '.$completePath);
			/*
			reportTokens (1, $stringsToLeaveUntouched, '4001'.$completePath);
			reportTokens (1, $tokensCSShtml, '4002'.$completePath);
			
			$obfuscatedStrings = str_replace(array_keys($tokensCSShtml), array_values($tokensCSShtml), $stringsToObfuscate);
			reportTokens (1, $obfuscatedStrings, '4003'.$completePath);
			
			$obfuscatedStrings = array_merge (
				$obfuscatedStrings,
				$stringsToLeaveUntouched
			);*/
			
			
			//reportTokens (1, $stringsRemoved, '4001.1 javascript $stringsRemoved'.$completePath);
			$obfuscatedStrings = $obfuscator->phpJSO_replace_tokens ($allTokens, $stringsRemoved);
			//reportTokens (1, $obfuscatedStrings, '4001.2 javascript $obfuscatedStrings'.$completePath);
			
		} else {
			// FAILED.... u need strings obfuscated.
			$obfuscatedStrings = $stringsRemoved;
		}	
			
		//echo '<pre>32510='; var_dump(htmlentities($src['sourceForObfuscation'])); echo '</pre>';
		//echo '<pre>32510.1='; var_dump($allTokens); echo '</pre>';
		
		//echo '<pre>32511='; var_dump($src ); echo '</pre>'; die();
		if (array_key_exists('sourceForObfuscation', $src)) {
		  $obfuscatedSource = $obfuscator->phpJSO_replace_tokens($allTokens, $src['sourceForObfuscation']); // FAILS MISERABLY : str_replace(array_keys($allTokens), array_values($allTokens), $src['sourceForObfuscation']);
		} else if (is_string($src)) {
		  $obfuscatedSource = $obfuscator->phpJSO_replace_tokens($allTokens, $src);
		};
		
		//GOOD AT echo '$obfuscatedSource=<pre>'; var_dump ($obfuscatedSource); echo '</pre>'; die();
		
		//reportTokens (1, $obfuscatedStrings, '4004'.$completePath);
		if (false) { echo 'class.obfuscate.javascript.php 4004 $obfuscatedStrings=<pre>'; var_dump ($obfuscatedStrings); echo '</pre>'; }

		if ($mangleStrings) {
			$obfuscatedStrings = $obfuscator->mangleStrings ($obfuscatedStrings, false);
		} else {
			$obfuscatedStrings = $obfuscatedStrings;
		}
		//$wd['obfuscatedSource001'] = $obfuscatedSource;

		//echo '$obfuscatedSource=<pre>'; var_dump ($obfuscatedSource); echo '</pre>'; die();
		
		if ($obfuscateStrings && is_array($obfuscatedStrings) ) {
			$search = array();
			foreach ($obfuscatedStrings as $idx => $obfuscatedString) {
				$search[] = '`'.$idx.'`';
			}
			
			//echo '<pre>32512='; var_dump($obfuscatedSource); echo '</pre>';
			
			$obfuscatedSource = str_replace ($search, array_values($obfuscatedStrings), $obfuscatedSource);
			
			//echo '<pre>32513='; var_dump($obfuscatedSource); echo '</pre>';
		} else {
		  //EVIL?? $obfuscatedSource = '';
		}
		
		//echo '$obfuscatedSource=<pre>'; var_dump ($obfuscatedSource); echo '</pre>'; die();
		//var_dump (strlen($obfuscatedSource)); die();
		
		if (is_string($obfuscatedSource)) {
                    $size = strlen($obfuscatedSource);
                } elseif (is_array($obfuscatedSource)) {
                    $c = jsonEncode($obfuscatedSource);
                    $size = strlen ($c);
                }
                    
		reportStatus (500, 
			'<p class="webappObfuscator__process__detail">Obfuscating Javascript- stage 2 (2 stages total) - obfuscated '.formatSourcepath($path, $k)
			.' (<span class="webappObfuscator__filesizeHumanReadable">'.filesizeHumanReadable($size).'</span>)</p>'
		);
		
		//echo '<pre style="color:brown;font-weight:bold;">stage2_new $r='; echo htmlentities($obfuscatedSource); echo '</pre>';die();
		
		$wd['obfuscatedSource__final'] = $obfuscatedSource;
		return $obfuscatedSource;
	}
	
	public function preprocess ($sources=null) {
		$wd = &$this->workData;
		$wd['tokens'] = array();
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		if (
			array_key_exists ('sourcesType', $gs)
			&& (
				$gs['sourcesType'] !== 'mixed'
				&& $gs['sourcesType'] !== 'javascript'
			)
		) return false;
		
		if (
			is_null($sources) 
			&& (
				!array_key_exists('sourcesType',$gs)
				|| $gs['sourcesType']==='mixed'
			)
		) $sources = &$gs['sources']['fetched']['javascript'];
		if (
			is_null($sources) 
			&& array_key_exists('sourcesType',$gs)
			&& $gs['sourcesType']!=='mixed'
		) $sources = &$gs['sources'];
		//echo '<pre>53241='; var_dump ($sources); die();
		global $lowerMemoryUsage;

		
		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 1 of 3 - walking sources</p>');
		$wd = array(
			'sources' => array (
				'stages' => array (
					0 => $sources
				)
			)
		);
		
		if (is_string($sources)) {
			$wd['sources']['stages'][1] = $this->preprocess__stage1__node ('', '', $sources);
		} elseif (is_array($sources)) {
			$wd['sources']['stages'][1] = $this->preprocess__stage1__walk ('', $sources);
		} else {
			return badResult (E_USER_ERROR, array (
				'msg' => 'webappObfuscator_obfuscate__javascript::preprocess() : invalid stage0 settings',
				'$wd' => $wd
			));
		}

		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 2 of 3 - walking sources</p>');
		if (is_string($sources)) {
			$wd['sources']['stages'][2] = $this->preprocess__stage2__node ('', '', $wd['sources']['stages'][1]);
		} elseif (is_array($sources)) {
			$wd['sources']['stages'][2] = $this->preprocess__stage2__walk ('', $wd['sources']['stages'][1]);
		}

		if (is_string($sources)) {
			$wd['tokens'] = $wd['sources']['stages'][2]['tokens'];
			$wd['sourceForObfuscation'] = $wd['sources']['stages'][2]['sourceForObfuscation'];
			$wd['stringsRemoved'] = $wd['sources']['stages'][2]['stringsRemoved'];
		} elseif (is_array($sources)) {
			$wd['tokens'] = $this->preprocess__stage3__walk ('', $wd['sources']['stages'][2], 'tokens');
			$wd['sourceForObfuscation'] = $this->preprocess__stage3__walk ('', $wd['sources']['stages'][2], 'sourceForObfuscation');
			$wd['stringsRemoved'] = $this->preprocess__stage3__walk ('', $wd['sources']['stages'][2], 'stringsRemoved');
		}

		/* 2015 July 8th : unsure what can get unset at this runtime point..
		if ($lowerMemoryUsage) {
			unset ($wd['sources']['stages'][1]);
		}*/
		
		//echo '<pre>preprocess $wd=';foreach ($wd as $k=>$v) { var_dump($k); } 

		$this->workData = $wd;
	}
	
	private function preprocess__stage1__walk ($path, $sources, $output='') {
		$r = array();
		foreach ($sources as $k => $v) {
 			if (is_string($v)) {
			reportStatus (500, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 1 - processing '.$k.'</p>');
				$r[$k] = $this->preprocess__stage1__node ($path, $k, $v);
			} if (is_array($v)) {
				$r[$k] = $this->preprocess__stage1__walk ($path.'/'.$k, $sources[$k], $output);
			}
		}
		
		return $r;
	}
	
	private function preprocess__stage1__node ($path, $k, $src) {
		//echo '505.1 - $src =<pre>'; var_dump (htmlentities($src)); echo '</pre>';
		$src = preg_replace('#var\s*\r\n#', 'var ', $src);
		//echo '505.2 - $src =<pre>'; var_dump (htmlentities($src)); echo '</pre>';
		//die();
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
	
	private function preprocess__stage2__walk ($path, $sources, $output='') {
		$r = array();
		foreach ($sources as $k => $v) {
 			if (is_string($v)) {
			reportStatus (500, '<p class="webappObfuscator__process__detail">Gathering tokens in Javascript - stage 1 - processing '.$k.'</p>');
				$r[$k] = $this->preprocess__stage2__node ($path, $k, $v);
			} if (is_array($v)) {
				$r[$k] = $this->preprocess__stage2__walk ($path.'/'.$k, $sources[$k], $output);
			}
		}
		
		return $r;
	}

	private function preprocess__stage2__node ($path, $k, $src) {
		/*$regxSearch = array (
			'#//.*[\r\n]#',
			'#\/\*[\s\S]*?\*\/#'
		);
		$regxReplace = array ('', '');
		$src = pregReplace ($regxSearch, $regxReplace, $source);
		*/
	
	
		//echo '<pre style="color:green;font-weight:bold;">stage2_new '.$path.'/'.$k.'; $r='; echo htmlentities($src); echo '</pre>';	
		$r = $this->getTokensAndStrings($src);
		
		//echo '<pre style="color:brown;font-weight:bold;">stage2_new '.$path.'/'.$k.'; $r='; echo htmlentities($r['sourceForObfuscation']); echo '</pre>';	
		
		return $r;
	}
	
	private function preprocess__stage3__walk ($path, $sources, $what) {
		$r = array();
	
		foreach ($sources as $k => $v) {
			//echo '<pre>6668 path='; var_dump ($path.'/'.$k); echo '</pre>';
			if (is_array($v)) {
				if (array_key_exists('sourceForObfuscation',$v)) {
					if (is_array($v[$what])) {
						$r = array_merge ($r, $v[$what]);
					} elseif (is_string($v[$what])) {
						$r[$k] = $v[$what];
					}
				} else {
					$r = array_merge($r, $this->preprocess__stage3__walk ($path.'/'.$k, $v, $what));
				}
			}
		}
		
		return $r;
	}
	
	// --- helper functions :
	
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
	
	public function getTokensAndStrings ($source) {
		//echo '<pre style="color:#030">$source='; echo htmlentities($source); echo '</pre>';
		global $wo__tokens__ignoreList;
	
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
		$debugI2 = false;
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
			$dbg = substr($source, $i - 200, 200) . ' ___ ' . $c . ' ___ ' . substr($source, $i+1, 200);
			
			//if ($c3==='var') $debugI = $i;
			//$line = substr($ource, $i - 50).substr($source,$i,50);

			// skip multi-line comments 
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
				continue;
			} 
			
			/* uncomment to track where normal single-line comments are being processed..
			if (
				$c2=='//'
				&& (
					$modes['inString'] === true
					|| $modes['inRegexp'] === true
				)
			) {
				$debugI = $i;
			}*/
			
			
			// skip single-line comments
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
				continue;
			} 
		
		
		
		
			/*
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
			};*/
			
			
			// debugging for string detection :
				if (false) {
					$s = "s.divName==='---all---'";
					if (substr($source, $i + 1, strlen($s))===$s) $debugI = $i;
				}
				$dbg1 = array (
					'dbg' => htmlentities($dbg),
					'modes' => json_encode($modes),
					'stringModes' => json_encode($stringModes),
					'commentModes' => json_encode($commentModes),
					'sourceToObfuscate' => substr($sourceToObfuscate, strlen($sourceToObfuscate)-100),
					'stringsRemoved' => $stringsRemoved
					//'$stringToRemove' => htmlentities($stringToRemove)
				);
				if ($debugI!==false && $i>$debugI && $i<$debugI+1000) { echo '<br/>'; };
				if ($debugI!==false && $i>$debugI && $i<$debugI+1000) { echo '<pre style="color:yellow;background:blue;font-weight:bold;">$dbg1=';var_dump ($dbg1);echo'</pre>'; }
				if ($debugI!==false && $i>$debugI + 1000) die();
			
			// string detection : skip URLs
			if (
				(	
				  $c === '\''
				  || $c == '"'
				)
				&& substr(strtolower($source),$i+1,7)==='http://'
				&& substr(strtolower($source),$i+1,8)==='https://'
			) {
				if (substr (strtolower($source), $i+1, 7) === 'http://') $j = $i + 8;
				if (substr (strtolower($source), $i+1, 8) === 'https://') $j = $i + 9	;
				//echo '7776 substr (strtolower($source), $i+1, 7) = '.substr (strtolower($source), $i+1, 7).'<br/>';
				$d = substr ($source, $j, 1);
				$d2 = substr($source, $j, 2);
				while (
				  $d!=='\''
				  && $d2!=='\\\''
				) {
					$d = substr ($source, $j, 1);
					$d2 = substr($source, $j, 2);
					if ($d2==='\\\'') {
					  $j = $j+2;
					  continue;
					}
					if ($d==='\'') {
					  $sourceToObfuscate .= substr($source, $i, $j);
					  $i = $j;
					  break;
					}
					$j++;
				}
			}
			
			
			// string detection : (string removal from output of source, into $stringsRemoved[])
			if (
				  $c === '\''
				  || $c == '"'
			) {
				$j = $i + 1;
				$d = substr($source, $j, strlen($c));
				$stringToRemove = $c;
				
				//if (strpos($dbg,'url')!==false) $debugI=$j; else $debugI=false;
				//if (strpos($dbg, 'url : ')!==false) $debugI = $j;
				
				if ($debugI!==false) {
					echo '<span style="color:purple;">'; echo ($c); echo '</span>';
				}
				if ($debugI!==false) {
					echo '<span style="color:red;">'; echo ($d); echo '</span>';
				}
				
				while ($d !== $c) {
					while ($d==='\\') { // string escaping is a biatch straight from hell, i'm tellin ya..
						if ($debugI!==false) {
							echo '<span style="color:navy;">'; echo ($d); echo '</span>';
						}
						$hasEscaped = true;
						$stringToRemove .= $d;
						$j = $j + 1;
						$d = substr($source, $j, 1);
						$stringToRemove .= $d;
						if ($debugI!==false) {
							echo '<span style="color:navy;">'; echo ($d); echo '</span>';
						}
						$j++;
						$d = substr($source, $j, 1);
					}
					if ($d===$c) {

					
					  $stringToRemove .= $c;
					  //echo '810.1 <pre style="color:lime;background:blue;">$c ='; var_dump ($c); echo '</pre>'; die();
					  $j++;
					  $d = substr($source, $j, 1);
						
						
						break;
					} else {
						$stringToRemove.=$d;
						if ($debugI!==false) {
							echo '<span style="color:green;">'; echo ($d); echo '</span>';
						}
						$j++;
						$d = substr($source, $j, 1);
					}
				}
				$stringToRemove .= $d;
			  	//echo 't701:<pre style="color:purple">'.htmlentities($stringToRemove).'</pre>';
				if ($debugI!==false) {
					echo '<span style="color:purple;">'; echo ($d); echo '</span>';
				}
				
				
				if (strlen($stringToRemove)===2) {
				  $sourceToObfuscate .= $stringToRemove;
				} elseif (
				  substr($stringToRemove,1,1)!=='/'
				  
				) {
				  $stringsRemovedCount++;
				  $stringsRemoved[$stringsRemovedCount] = $stringToRemove;
				  $tmp = '`'.$stringsRemovedCount.'`';
				  $sourceToObfuscate .= $tmp;
				} else { 
				  $sourceToObfuscate .= $stringToRemove;
				}
				
				if ($debugI!==false) {
					$dbg1 = array (
						'dbg' => htmlentities($dbg),
						'$stringToRemove' => htmlentities($stringToRemove)
					);
					if ($debugI!==false && $i>$debugI && $i<$debugI+1000) { echo '<pre style="color:orange;background:blue;font-weight:bold;">$dbg1.1=';var_dump ($dbg1);echo'</pre>'; }
				}
				
				$i = $j ;
				//$sourceToObfuscate .= substr ($source, $i, 1);
				
				continue;
			}
			
			// regular expression detection:
			
			// remove all     new RegExp ("/..../");    from output of source and put them in $stringsRemoved
			if (
				substr ($source, $i, 8) === 'RegExp ('
				|| substr ($source, $i, 7) === 'RegExp('
			) {
				//echo '<br/>';
				//echo '66601=';
				// scan ahead to final ); sequence
				$parenthesisLevel = 1;
				if (substr ($source, $i, 8) === 'RegExp (') $j = $i + 9;
				if (substr ($source, $i, 7) === 'RegExp(') $j = $i + 8	;
				while ($parenthesisLevel!==0) {
					$d = substr ($source, $j, 1);
					if ($d==='(') $parenthesisLevel++;
					if ($d===')') $parenthesisLevel--;
					//echo '<span style="color:yellow;background:red;">'.$d.'</span><span style="color:white;background:red;">'.$parenthesisLevel.'</span>';
					$j++;
				}
				//echo '<br/>';

				// we don't want short tokens (token minimum length is just 2 characters!)
				// to mess up our regexpzzz
				$stringToRemove = substr ($source, $i, $j - $i + 1);
				//echo '<br/>';
				//echo '66602='.$stringToRemove;
				//echo '<br/>';
				$stringsRemovedCount++;
				$stringsRemoved[$stringsRemovedCount] = $stringToRemove;
				$tmp = '`'.$stringsRemovedCount.'`';
				$sourceToObfuscate .= $tmp;

				
				$i = $j;
				//if ($stringToRemove=='RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");') $debugI = $i;
				continue;
			}
			
			// remove normal inline regular expressions aka var regx = /^bla$/; from output of source and put them into $stringsRemoved
			if (
				$c === '/'
				//&& substr($source, $i-3, 3) !== '\' /'
			) {
			
				if (false) {
					$dbg1 = array (
						'dbg' => htmlentities($dbg),
						'$stringToRemove' => htmlentities($stringToRemove)
					);
					$s = '/\//g,\'_\') + \'--\'';
					if (substr($source, $i, strlen($s))===$s) $debugI2 = $i;
					if ($debugI2!==false && $i>$debugI2 && $i<$debugI2+1000) { echo '<br/>'; };
					if ($debugI2!==false && $i>$debugI2 && $i<$debugI2+1000) { echo '<pre style="color:yellow;background:blue;font-weight:bold;">$dbg1=';var_dump ($dbg1);echo'</pre>'; }
					if ($debugI2!==false && $i>$debugI2 + 1000) die();
				}
			
				
			
			
				$s = [ '/;', '/,', '/)', '/g', '/i', '/m', '/\w*\)'];
				$disqualify = [ "\r", "\n" ];
				$j = $i + 1;
				$d = substr($source, $j, 1);
				$ds = substr($source, $j, 2);
				$stringToRemove = $c;
				
				if ($debugI2!==false) {
					echo '<span style="color:purple;">'; echo ($c); echo '</span>';
				}
				if ($debugI2!==false) {
					echo '<span style="color:red;">'; echo ($d); echo '</span>';
				}
				
				while (
					array_search($ds, $s)===false
					&& array_search($d, $disqualify)===false
				) {
					if (array_search($d, $disqualify)!==false) {
						break;
					}
				
				
					while ($d==='\\') { // string escaping is a biatch straight from hell, i'm tellin ya..
						if ($debugI2!==false) {
							echo '<span style="color:navy;">'; echo ($d); echo '</span>';
						}
						$hasEscaped = true;
						$stringToRemove .= $d;
						$j = $j + 1;
						$d = substr($source, $j, 1);
						$ds = substr($source, $j, 2);
						$stringToRemove .= $d;
						if ($debugI2!==false) {
							echo '<span style="color:navy;">'; echo ($d); echo '</span>';
						}
						$j++;
						$d = substr($source, $j, 1);
						$ds = substr($source, $j, 2);
					}
					if (array_search($ds,$s)!==false) {
						break;
					} else {
						$stringToRemove.=$d;
						if ($debugI2!==false) {
							echo '<span style="color:green;">'; echo ($d); echo '</span>';
						}
						$j++;
						$d = substr($source, $j, 1);
						$ds = substr($source, $j, 2);
					}
				}
				if (array_search($d, $disqualify)===false) {
					$stringToRemove .= $ds; // TEST!!
					if ($debugI2!==false) {
						echo '<span style="color:purple;">'; echo ($d); echo '</span>';
					}
					
					// we don't want short tokens (token minimum length is just 2 characters!)
					// to mess up our regexpzzz
					$stringsRemovedCount++;
					$stringsRemoved[$stringsRemovedCount] = $stringToRemove;
					$tmp = '`'.$stringsRemovedCount.'`';
					$sourceToObfuscate .= $tmp;
				} else {
					$sourceToObfuscate .= $stringToRemove;
				}
				
				
				$i = $j + 1;
				//$sourceToObfuscate .= substr ($source, $i, 1);
				
				continue;
			};			
			
			// token detection :
			/*
			if (
				/*$modes['inComment']===false
				&& $modes['inRegexp']===false
				&& $modes['inString'] === false
				$modes['inJavascript'] === true
			) { */
				//if ($token==='var') {
				//  echo '506.1 array_search ($c, $tokenDelimiters)=<pre>';var_dump(array_search ($c, $tokenDelimiters));echo'</pre>';die();
				//}
				//echo '507.1 $c=<pre>';var_dump ($c); var_dump (array_search ($c, $tokenDelimiters)); echo '</pre>';
				if (array_search ($c, $tokenDelimiters)===false) {
					$token .= $c;
					//$sourceToObfuscate .= ' ';
				} else {
					//$tkn = trim(preg_replace('#var#','',$token));
					$tkn = trim($token);
					if (
					  array_search ($tkn, $wo__tokens__ignoreList)!==false
					) {
					  //$sourceToObfuscate .= $token;
					  if (false) {
					    $dbg3 = array (
					      '$tkn' => $tkn,
					      '$token' => $token,
					      '$dbg' => $dbg,
					      '$sourceToObfuscate' => $sourceToObfuscate
					    );
					    echo '<pre style="color : #009900">$dbg3=';var_dump($dbg3);echo'</pre>';
					  }
					  
					} else if (
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
							$dbg2 = array (
							  'sourceToObfuscate' => $sourceToObfuscate
							);
							echo '<pre style="color:green;">'; var_dump ($dbg1); echo '</pre>';
							echo '<pre style="color:blue;">'; var_dump ($dbg2); echo '</pre>';
							
							$tokenCount++;
						} else {
							$tokens[] = $tkn;
						}
					}
					if (false) {
					  $dbg4 = array (
					    '$tkn' => $tkn,
					    '$token' => $token,
					    '$dbg'=> $dbg,
					    '$sourceToObfuscate' => $sourceToObfuscate,
					  );
					  echo '<pre style="color : #990000">$dbg4=';var_dump($dbg3);echo'</pre>';
					}
					$token = '';
				}
			// }
			
			/*
			if (
				(
					$modes['inJavascript']===true
					|| $modes['inString']===true
					|| $modes['inRegexp']===true
				)
				
			) {
				/*
				if ($modes['inString']===true) {
					if ($stringModes['mustKeepInSource']===true) {
						$sourceToObfuscate .= $c;
					} else {
						$stringsRemoved[$stringsRemovedCount] .= $c;
					}
				} else { */
					$sourceToObfuscate .= $c;
				// }
				
				/*if ($c2==='{}' || $c==='}') {
					echo '<pre style="color:red;font-weight:bold;">123='.htmlentities($sourceToObfuscate).'</pre>';
				};*/

			// }

			if (false) {
				$dbg1 = array (
					'dbg' => htmlentities($dbg),
					'modes' => json_encode($modes),
					'stringModes' => json_encode($stringModes),
					'commentModes' => json_encode($commentModes)
				);
				$s = '<p><a href="javascript:window.parent.window.sa.site.code.pushState';
				if (substr($source, $i, strlen($s))===$s) $debugI = $i;
				if ($debugI!==false && $i>$debugI && $i<$debugI+70) { echo '<pre style="color:orange;font-weight:bold;">$dbg1=';var_dump ($dbg1);echo'</pre>'; }
				if ($debugI!==false && $i>$debugI + 70) die();
			}
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
			'sourceForObfuscation' => $sourceToObfuscate,
			'stringsRemoved' => $stringsRemoved
		);
		//echo '<pre style="color:#000099">';var_dump ($sourceToObfuscate);echo'</pre>';die();
		
		reportTokens (700, $r['tokens'], 'webappObfuscator_obfuscate__javascript::getTokensAndStrings() : finalized Javascript tokens'); 
		//die(); 
		
		return $r;
	}
};
?>
