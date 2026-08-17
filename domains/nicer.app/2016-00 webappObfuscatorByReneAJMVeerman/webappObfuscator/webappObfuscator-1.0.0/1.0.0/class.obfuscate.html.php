<?php

class webappObfuscator_obfuscate__html {

	public $settings = null;
	public $workData = array();
	
	public function __construct ($settings) {
		$this->settings = $settings;
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
			'html', $codePaths, '', $wd['output']['stages'][0], $sourcesURLs['html']
		);
		return 'results are in $my->workData["output"]["stages"][1]';
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
				&& $gs['sourcesType'] !== 'html'
			)
		) return false;
		
		
		$wd['output'] = array(
			'stages' => array (
				0 => array()
			)
		);

		// here's your actual output :
		$wd['output']['stages'][0] = $this->obfuscate__stage0__walk ('', $wd['sources']['stages'][0]);
	}
	
	private function obfuscate__stage0__walk ($path, $sources) {
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		
		//echo '901.0--<pre>'; var_dump ($sources); echo '</pre>';
		if (is_array($sources)) {
			$r = array();
			foreach ($sources as $k => $v) {
				if (is_string($v)) {
					reportStatus (600, '<p class="webappObfuscator__process__detail">Obfuscating HTML - stage 1 (2 stages total) - processing '.$path.'/'.$k.'</p>');
					$r[$k] = $this->obfuscate__stage0__node ($path, $k, $v);
				} else {
					$r[$k] = $this->obfuscate__stage0__walk ($path.'/'.$k, $sources[$k]);
				}
			}
		} elseif (is_string($sources)) {
		
                    reportStatus (600, '<p class="webappObfuscator__process__detail">Obfuscating HTML - stage 1 (2 stages total) - processing '.$path.'/'.$k.'</p>');
                    $r = $this->obfuscate__produceOutput($path, $sources, $obfuscator->workData['tokens']);
		}
		return $r;
	}

	private function obfuscate__stage0__node ($path, $k, $src) {
	  /*echo '402:<pre>';
	  var_dump ($path);
	  var_dump ($k);
	  var_dump ($src);
	  echo '</pre>';
	  die();*/
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		
		$obfuscatedSource = $this->obfuscate__produceOutput ($path.'/'.$k, $src, $obfuscator->workData['tokens']); 
		return $obfuscatedSource;
	}	
	
	public function obfuscate__produceOutput ($path, $source, $allTokens) {
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		$wJavascript = $obfuscator->getWorker('javascript');
		if (stripos($path,'index')!==false) return $src;
		
		/*if (
		  stripos($path,'index')!==false 
		  || stripos($path,'siteTemplate')!==false 
		) return $source;*/
		
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;
		global $deadSlow______butThorough;
		
		$tokenBoundary = $obfuscator->factorySettings['tokens']['tokenBoundary'];
		$regxBoundary = $obfuscator->factorySettings['tokens']['regxBoundary'];
		$searchTokenSpecialChars = $obfuscator->factorySettings['tokens']['searchTokenSpecialChars'];
		$replaceTokenSpecialChars = $obfuscator->factorySettings['tokens']['replaceTokenSpecialChars'];
		
		$detailedDebug = false;

		$src = $source;
		if ($detailedDebug) { echo '801.0 $src=<pre style="color:#003333">'; var_dump (htmlentities($src)); echo '</pre>'; }
		
		/*if (
			stripos($path,'frontpage')!==false
			|| stripos($path,'siteTemplate')!==false
			|| stripos($path,'php')!==false
		) {
			return $src;
		}*/
		$nontags = $this->strip_tags_returnNonTags($src);
		$searches = array();
		$replaces = array();
		foreach ($nontags as $idx=>$nontag) {
			$search = $regxBoundary.$tokenBoundary.str_replace($searchTokenSpecialChars, $replaceTokenSpecialChars, $nontag).$tokenBoundary.$regxBoundary;;
			$replace = '$1`'.$idx.'`$2';
			$searches[] = $search;
			$replaces[] = $replace;
			if ($detailedDebug) { echo '800 $idx=<pre style="color:blue">'; var_dump ($idx); echo '</pre>'; }
		}
		if ($detailedDebug) { echo '801.1 $src=<pre style="color:#003333">'; var_dump (htmlentities($src)); echo '</pre>'; };
		$src = pregReplace ($searches, $replaces, $src);
		$searches = array();
		$replaces = array();
		
		if ($detailedDebug) { echo '801.2 $src=<pre style="color:#003333">'; var_dump (htmlentities($src)); echo '</pre>'; };
		$src2 = $this->strip_strings ($src, count($nontags));
		if ($detailedDebug) { echo '801.3 $src2=<pre style="color:#007777">'; var_dump (htmlentities($src2['sourceForObfuscation'])); echo '</pre>'; };
		$src = $src2['sourceForObfuscation'];
		//$stringsRemoved = $src2['stringsRemoved']; // when re-inserting, add count($nontags) to the key-index of $stringsRemoved!

		/* MOVED TO webappObfuscator::phpJSO_replace_tokens()
			$stringsToObfuscate = array();
			$stringsToLeaveUntouched = array();
			foreach ($src2['stringsRemoved'] as $k => $v) {
				if (
					stripos($v, 'http://')===false
					&& stripos ($v, 'https://')===false
					
					// someone may have custom html attributes with relative urls in 'm, exclude those... :
					&& 0 === preg_match ('#js|json|php|html$#', $v) 
				) {
					$stringsToObfuscate[] = $obfuscator->phpJSO_replace_tokens ($allTokens, $v);// does NOT work : str_replace(array_keys($allTokens), array_values($allTokens), $v);
				} else {
					$stringsToObfuscate[] = $v;
				}
			}
			//reportTokens (1, $stringsToObfuscate, '4000 html '.$path);
			/*
			reportTokens (1, $stringsToLeaveUntouched, '4001'.$completePath);
			reportTokens (1, $tokensCSShtml, '4002'.$completePath);
			
			$obfuscatedStrings = str_replace(array_keys($tokensCSShtml), array_values($tokensCSShtml), $stringsToObfuscate);
			reportTokens (1, $obfuscatedStrings, '4003'.$completePath);
			
			$obfuscatedStrings = array_merge (
				$obfuscatedStrings,
				$stringsToLeaveUntouched
			);* /
			$obfuscatedStrings = $stringsToObfuscate;
		$stringsRemoved = $obfuscatedStrings;
		*/
		$mangleStrings = true;
		if ($mangleStrings) {
			$stringsRemoved = $obfuscator->mangleStrings ($src2['stringsRemoved'], true);
		} else {
			$stringsRemoved = $src2['stringsRemoved'];
		}
		
		
		//echo '<pre>t1242='; echo htmlentities($src2['sourceForObfuscation']); echo '</pre>';
		//reportTokens( 1, $src2['stringsRemoved']); 
		//die();
		
		
		//reportTokens (1, $nontags, 'nontags');
		
		
		//echo '<pre style="color:red">302.1='.htmlentities($src).'</pre>';
	
		$src3 = $obfuscator->phpJSO_replace_tokens ($allTokens, $src); // this has never caused problems. 
		// trying to output the lines below nor marked with [55], is when problems started.
	
		//echo 't302.2<pre style="color:green">';var_dump (htmlentities($src3));echo '</pre><br/>';
		

		//echo '<pre style="color:red">35202='.htmlentities($src2).'</pre>';
		//$mangleStrings=false;
		if ($mangleStrings) {
			$stringsRemoved = $obfuscator->mangleStrings ($stringsRemoved, false);
		} else {
			//$stringsRemoved = $stringsRemoved;
		}

		
		$search = '#`\d+`#';
		$matches = array();
		preg_match_all ($search, $src3, $matches);
		
		/*
		echo '<pre>7770='.$path.' - $src3='; echo htmlentities ($src3); echo '</pre>';
		echo '<pre>7771='.$path.' - $matches[0]='; var_dump ($matches[0]); echo '</pre>';
			echo '<pre>7772='.$path.' - $nontags='; var_dump ($nontags); echo '</pre>';
		reportTokens (1, $nontags, '7772 '.$path);
		echo '<pre>7773='.$path.' - $stringsRemoved='; var_dump ($stringsRemoved); echo '</pre>';
		reportTokens (1, $nontags, '7773 '.$path);
                */
		
		$searches = array();
		$replaces = array();
		foreach ($matches[0] as $idx => $match) {
			$idxReal = (int)str_replace('`','',$match);
			$search = '#`'.$idxReal.'`#';

			/*echo '<pre>7776='.$path.' - $idxReal='; var_dump ($idxReal); echo '</pre>';
			echo '<pre>7777='.$path.' - $search='; var_dump ($search); echo '</pre>';
			if ($idx > 55) die();*/
			
			//FAIL : $search = '#'.$match.'#';
			$tst1 = (int)$idxReal-count($nontags);
			//echo '<pre>7776='.$path.' - $tst1='; var_dump ($tst1); echo '</pre>';
			$replace = '';
			if (array_key_exists($idxReal, $nontags)) {
				$replace = $nontags[$idxReal];
			} elseif ($tst1 > 0 && array_key_exists($tst1, $stringsRemoved)) {
				$replace = $stringsRemoved[$tst1];
			};
			/*$replace = (
				array_key_exists($idxReal, $nontags)
				? 
				: 	$tst1 > 0 
					&& array_key_exists($tst1,$stringsRemoved) 
					? $stringsRemoved[$tst1]
					: ''
			);*/
			if ($replace!=='') {
				$searches[] = $search;
				$replaces[] = $replace;
			}
			
		};
		
		
		//echo '<pre>7774='.$path.' - $searches='; var_dump ($searches); echo '</pre>';
		//echo '<pre>7775='.$path.' - $replaces='; var_dump ($replaces); echo '</pre>';

		//echo '<pre>7774='.$path.' - $searches[88]='; var_dump ($searches[88]); echo '</pre>';
		//echo '<pre>7775='.$path.' - $replaces[88]='; var_dump ($replaces[88]); echo '</pre>';
		//echo '<pre>7776.1='.$path.' - post pregReplace() - $src3='; echo htmlentities ($src3); echo '</pre>';
		
		
		$replaces2 = $obfuscator->phpJSO_replace_tokens ($allTokens, $replaces, true);
		/*echo '<pre>7774.1='.$path.' - $allTokens='; var_dump($allTokens); echo '</pre>';
		echo '<pre>7774.2='.$path.' - $replaces2='; var_dump($replaces2); echo '</pre>';
		*/
		
		$src3 = pregReplace ($searches, $replaces2, $src3);
		$searches = array();
		$replaces2 = array();
		//echo '<pre>7776.2='.$path.' - post pregReplace() - $src3='; echo htmlentities ($src3); echo '</pre>'; die();
		//die();

		$obfuscatedSource = $this->stripCommentsAndMinify($src3);
		$wd['obfuscatedSource'] = $obfuscatedSource;

		if (is_string($obfuscatedSource)) {
                    $size = strlen($obfuscatedSource);
                } elseif (is_array($obfuscatedSource)) {
                    $c = jsonEncode($obfuscatedSource);
                    $size = strlen ($c);
                }

		
		reportStatus (500, 
			'<p class="webappObfuscator__process__detail">Obfuscating HTML - stage 2 (2 stages total) - obfuscated '.formatSourcepath($path).' (<span class="webappObfuscator__filesizeHumanReadable">'.filesizeHumanReadable($size).'</span>)</p>'
		);
		
		
		return $obfuscatedSource;
	}
	
/*
	public function OLD_____obfuscate () {
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		$wJavascript = $obfuscator->getWorker('javascript');
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;
		global $deadSlow______butThorough;
	
		$allSources = $wd['sources']['stages'][0];
		
		$allTokens = $obfuscator->workData['tokens'];
		$wd['output'] = array();
		if (is_array($allSources)) {
			foreach ($allSources as $sourcesDescription => $sources) {
				if (is_array($sources)) {
					$wd['output'][$sourcesDescription] = array();
					foreach ($sources as $sourceDescription => $source) {
						$wd['output'][$sourcesDescription][$sourceDescription] = $this->obfuscate__produceOutput ($source, $allTokens, $sourcesDescription, $sourceDescription);
					}
				} else {
					//var_dump ($sources);
					$wd['output'][$sourcesDescription] = $this->obfuscate__produceOutput($sources, $allTokens, $sourcesDescription, '');
				}
			}
		} elseif (is_string($allSources)) {
			
		} else {
			return badResult (E_USER_ERROR, array (
				'msg' => 'webappObfuscator_obfuscate__html::obfuscate() : invalid $allSources',
				'$wd' => $wd
			));
		}
	}
	
*/	
	public function preprocess ($sources=null) {
		$wd = &$this->workData;
		$wd['tokens'] = array();
		$wd['tokensHTML'] = array();
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		if (
			array_key_exists ('sourcesType', $gs)
			&& (
				$gs['sourcesType'] !== 'mixed'
				&& $gs['sourcesType'] !== 'html'
			)
		) return false;

		
		if (
			is_null($sources) 
			&& (
				!array_key_exists('sourcesType',$gs)
				|| $gs['sourcesType']==='mixed'
			)
		) $sources = &$gs['sources']['fetched']['html'];
		if (
			is_null($sources) 
			&& array_key_exists('sourcesType',$gs)
			&& $gs['sourcesType']!=='mixed'
		) $sources = &$gs['sources'];

		
		
		$wd['sources'] = array (
			'stages' => array (
				0 => $sources
			)
		);
		$wd['sourceForObfuscation'] = '';
		
		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in HTML - stage 1 of 3 - walking sources to find regular expression matches</p>');
		if (is_string($sources)) {
			$wd['regx'] = array( 
				'html' => $this->regxSources__node ('html', '', $sources, $wd['sourceForObfuscation'])
			);
		} elseif (is_array($sources)) {
			$wd['regx'] = array( 
				'html' => $this->regxSources__walk ($sources, 'html', $wd['sourceForObfuscation'])
			);
		} else {
			return badResult (E_USER_ERROR, array (
				'msg' => 'webappObfuscator_obfuscate__html::preprocess() : invalid stage0 settings',
				'$wd' => $wd
			));
		}

		//echo '<pre>$bla='; var_dump ($wd['regx']['html']); echo '</pre>'; die();

		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in HTML - stage 2 of 3 - walking sources to gather up tokens</p>');
		$wd['tokens'] = $this->gatherAllTokens ($wd['regx']['html'], 'html');
		$wd['tokensHTML'] = $this->gatherAllTokens ($wd['regx']['html'], 'html', array ('ids', 'classes'));
		//echo '<pre>$boooo='; var_dump ($wd['tokensHTML']); echo '</pre>'; 
		/*array_unique(array_merge(
			$this->gatherTokens ('html/ids', 'ids', $wd['regx']['html']['ids']),
			$this->gatherTokens ('html/classes', 'classes', $wd['regx']['html']['classes'])
		));*/
		
		reportStatus (700, '<p class="webappObfuscator__process__detail">Gathering tokens in HTML - stage 3 of 3 - processing '.count($wd['tokens']).' tokens</p>');
		$wd['tokens'] = $this->processAllTokens ($wd['tokens']); 
		$wd['tokensHTML'] = array_unique($this->processAllTokens ($wd['tokensHTML'])); 
		//echo '<pre>$boooo='; var_dump ($wd['tokensHTML']); echo '</pre>'; die();
		
		reportTokens (700, $wd['tokens'], 'webappObfuscator_obfuscate__html::preprocess() finalized HTML tokens');
		//echo '<pre>html tokens : '; var_dump ($wd['tokens']);
	}

	
	
	// preprocess() stage 1 :
	public function regxSources__walk ($sources, $sourceIDpath='', &$wdos) {
		$wdo = array();
		foreach ($sources as $k => $v) {
			if (is_string($v)) {
				$wdo[$k] = $this->regxSource__node ($sourceIDpath, $k, $v, $wdos);
			} else if (is_array($v)) {
				$wdo[$k] = $this->regxSources__walk ($sources[$k], $sourceIDpath.'/'.$k, $wdos);
			}
		}
		return $wdo;
	}
	
	public function regxSource__node ($sourceIDpath, $sourceID, $source, &$wdos) {
		global $reportStatusGoesToStatusfile;
		reportStatus (905, '<p class="webappObfuscator__process__node">Gathering tokens by processing regular expressions against '.$sourceIDpath.'/'.$sourceID.'</p>');
		$wdos .= $source;
		$wdo = array();
		$wdo[$sourceID] = array(
			'webappObfuscator__type' => 'sourceFile',
			'ids' => array(),
			'classes' => array(),
			'javascript_onhandlers' => array(),
			'javascript_attributes' => array(),
			'javascripts' => array()
		);
		$wdo[$sourceID]['webappObfuscator__sourceProcessed'] = $source;
		$wdo[$sourceID]['ids'] = $this->regxSource_do ($sourceIDpath, $sourceID, '#id=[\'"](.*?)[\'"]#', $source, 'ids');
		$wdo[$sourceID]['classes'] = $this->regxSource_do ($sourceIDpath, $sourceID, '#class=[\'"](.*?)[\'"]#', $source, 'classes');
		$wdo[$sourceID]['javascript_onhandlers'] = $this->regxSource_do ($sourceIDpath, $sourceID, '#\son\w*=[\'"](.*?)[\'"]#', $source, 'javascript_onhandlers');
		$wdo[$sourceID]['javascript_attributes'] = $this->regxSource_do ($sourceIDpath, $sourceID, '#\s\w*=[\'"]javascript\:?:(?!return.false.+)(.*?)(\);|\)"|\)\')#xs', $source, 'javascript_attributes');
		$wdo[$sourceID]['javascripts'] = $this->regxSource_do ($sourceIDpath, $sourceID, '#<script type=[\'"]text/javascript[\'"]>(.*?)</script>#', $source, 'javascripts');
		//if ($reportStatusGoesToStatusfile===false) { echo '<pre>webappObfuscator_obfuscate__html::regxSource__node() : $wdo='; var_dump ($wdo); echo'</pre>'; };
		
		return $wdo;
	}
	
	public function regxSource_do ($sourceIDpath, $sourceID, $regx, $source, $what) {
		$wdo = array();
		global $reportStatusGoesToStatusfile;
		//reportStatus (999, '<p class="webappObfuscator__process__regx">webappObfuscator_obfuscate__html::regxSource_do() : Processing '.$sourceIDpath.'/'.$sourceID.' against '.htmlentities($regx).' for $source = <br/>'.htmlentities($source).'</p>');
		//reportStatus (999, '<p class="webappObfuscator__process__regx">webappObfuscator_obfuscate__html::regxSource_do() : Processing '.$sourceIDpath.'/'.$sourceID.' against '.htmlentities($regx).'</p>');
		$matches = array();
		preg_match_all ($regx, $source, $matches);
		
		$detailedDebug = false;
		
		if ($detailedDebug) { echo '<pre style="color:white;background:red;">$what='; var_dump ($what); '<br/>$matches='; var_dump ($matches); echo '</pre>'; };

		if ($what==='classes') {
			foreach ($matches[1] as $k=>$v) {
			  $wdo = array_merge ($wdo, preg_split("#\s#", $v));
			  if ($detailedDebug) { echo '<pre style="color:lime;background:blue;">'; var_dump ($wdo); echo '</pre>'; };
			}
		} else {
			foreach ($matches[1] as $k=>$v) {
				if ($detailedDebug) { echo '<pre style="color:white;background:blue;">$k='; var_dump ($k); echo '<br/>$v=';var_dump ($v); echo '</pre>'; };
				if (
				  strpos($matches[0][$k],'src=')!==false
				  || strpos($matches[0][$k], 'href=')!==false
				  || strpos($v, 'http://')!==false // 20160309 13:40 EXPERIMENTAL
				) {
				  // no need to add this entry, in fact, better not add it.
				} else if (
				  array_key_exists(2, $matches)) {
					$wdo[] = $v.$matches[2][$k];
				} else {
					$wdo[] = $v;
				}
			}
		}
		if ($detailedDebug) { echo '<pre style="color:yellow;background:red;">'; var_dump ($wdo); echo '</pre>'; };
		
		//if ($reportStatusGoesToStatusfile===false) { echo '<pre class="webappObfuscator__findTokens__tokensFound">webappObfuscator_obfuscate__html::regxSource_do() : '; var_dump ($wdo); echo '</pre>'; }
		
		if (count($wdo)>0) {
			$wdo = array (
				0 => $wdo,
				'webappObfuscator__type' => 'preg_match_all'
			);				
		}
		return $wdo;
	}
	
	// preprocess() stage 2 : 
	public function gatherAllTokens(&$regxes, $path='', $filter=null) {
		$wdo = array();
		global $reportStatusGoesToStatusfile;
		//if ($reportStatusGoesToStatusfile===false)  { echo '<pre>gatherAllTokens() : $regxes='; var_dump ($regxes); echo '</pre>'; };
		
		foreach ($regxes as $k => $v) {
			if (
				$k !== 'webappObfuscator__type'
				&& $k !== 'webappObfuscator__sourceProcessed'
			) {
				reportStatus (900, '<p class="webappObfuscator__process__node">Gathering tokens in '.$path.'/'.$k.'</p>');
			}
			
			if (
				is_array($v)
				&& array_key_exists('webappObfuscator__sourceProcessed', $v)
			) {
				//reportStatus (900, '<p class="webappObfuscator__findTokens__sourceToProcess">Gathering tokens in $path="'.$path.'/'.$k.'" ; for this source :<br/>'.htmlentities($v['webappObfuscator__sourceProcessed']).'</p>');
				reportStatus (900, '<p class="webappObfuscator__findTokens__sourceToProcess">Gathering tokens in '.$path.'/'.$k.'</p>');
			}
			
			if (
				is_array($v)
				&& array_key_exists('webappObfuscator__type', $v)
				&& $v['webappObfuscator__type'] === 'preg_match_all'
			) {
				//echo '<pre style="color:green;">$k,filter=';var_dump ($k); var_dump ($filter);echo'</pre>';
				if (is_null($filter) || array_search($k,$filter)!==false) $wdo[$k] = $this->gatherTokens ($path, $k, $v, $filter);
			} else if (is_array($v)) {
				$wdo[$k] = $this->gatherAllTokens ($regxes[$k], $path.'/'.$k, $filter);
			}
		}
		
		//if ($reportStatusGoesToStatusfile===false)  { echo '<pre>webappObfuscator_obfuscate__html::gatherAllTokens() : $wdo='; var_dump ($wdo); echo '</pre>'; };
		return $wdo;
	}
	
	public function gatherTokens ($path, $k, $v, $filter=null) {
		global $reportStatusGoesToStatusfile;
		$target = &$v[count($v)-2];
		//if ($reportStatusGoesToStatusfile===false) { echo '<pre>gatherTokens() : $target='; var_dump ($target); echo '</pre>'; };
		$tokensFound = array();
		if (is_array($target) && count($target)>0) {
			$tokensFound = array_merge ($tokensFound, $target);
		}
		reportStatus (900, '<p class="webappObfuscator__process">Gathered '.count($tokensFound).' tokens in '.$path.'/'.$k);// .') for '.htmlentities(json_encode($v)).'</p>');
		reportTokens (912, $tokensFound, 'webappObfuscator_obfuscate__html::gatherTokens()');
		return $tokensFound;
	}
	
	// preprocess() stage 3 :
	public function processAllTokens ($wdo) {
		global $wo__tokens__ignoreList__allLowercase;
		global $reportStatusGoesToStatusfile;
	
		$s = &$this->settings;
		//echo '<pre>wHTML'; var_dump ($s);
		$obfuscator = &$s['obfuscator'];
		$wJavascript = $obfuscator->getWorker('javascript');
		global $minTokenLength;
	
		$r = array();
		$r1 = array();
		$r2 = array();
		//if ($reportStatusGoesToStatusfile===false) { echo '<pre>processAllTokens(): $wdo='; var_dump ($wdo); echo '</pre>'; };
		
		//echo '<pre>325100='; var_dump ($wdo); echo '</pre>';
		
		foreach ($wdo as $k => $v) {
			foreach ($v as $k1 => $v1) {
				foreach ($v1 as $k2 => $v2) {
					if (
						is_string($v2)
						&& strpos ($v2, 'eval')===false
					) {
						$s = preg_split ('#[\(\)\'"\s]+#', $v2);
						//var_dump ($s);
						if (count($s)>0) {
							$r = array_merge ($r, $s);
						} else {
							$r3 = array ( 0 => $v2 );
							$r = array_merge ($r, $r3);
						}
					} elseif (
						is_array($v2)
					) {
						$r = array_merge ($r, $v2);
					}
				}
			}
		}
		foreach ($r as $k => $v) {
			if (
				$v !==','
				&& $v !==';'
			) $r1[] = $v;
		};
		
		
		//reportStatus ('<p class="webappObfuscator__process__detail">Gathering tokens in HTML - stage 3 of 3 - processAllTokens() after first foreach loop has '.count($r1).' tokens to process</p>');
		//if ($reportStatusGoesToStatusfile===false) { echo '<pre class="webappObfuscator__findTokens__tokensFound">$r1 = '; var_dump ($r1); echo '</pre>'; }
		
		foreach ($r1/*TODO was $r -- strange*/ as $k => $v) {
			if (
				strpos($v, ');')===false
				&& substr($v, strlen($v)-1, 1)!==')'
			) {
				$p = explode('.', $v);
				$r2 = array_merge($r2, $p);
			} else if (is_numeric($v)) {
				// DONT ADD!
			} else {
			
				/* probably a javascript string, but assuming your setup of unobfuscated javascript, javascript whitelists, 
				 * and obfuscated javascript found in <script type="text/javascript"> (with src="" and/or(??!) without src="")
				 * provides all the tokens found in the html attributes 
				 * ( onclick=""/onmouseover=""/etc, href="javascript:bla.blie()" ),
				 * we don't need to scan these javascript strings (code-fragments) for additional tokens.
				 * i don't think this will be *ever* necessary. 
				 * what worked un-obfuscated, will work obfuscated, without having to deep-parse here for more tokens.
				 */
			}
		};
		//reportStatus (910, '<p class="webappObfuscator__findTokens__counts">webappObfuscator_obfuscate__html::processAllTokens() : $r2 : '.count($r2).' tokens found</p>');//, '.count($stringsRemoved).' stringsRemoved</p>');
		
		
		$r7 = array();
		foreach ($r2 as $k2=>$v2) {
			if (
				strlen($v2) >= $minTokenLength
				&& array_search (strtolower($v2), $wo__tokens__ignoreList__allLowercase)===false
			) $r7[] = $v2;
		}

		reportStatus (910, '<p class="webappObfuscator__findTokens__counts">webappObfuscator_obfuscate__html::processAllTokens() : '.count($r7).' tokens found</p>');//, '.count($stringsRemoved).' stringsRemoved</p>');

		return $r7;
	}
	
	
	
	// OBFUSCATE STAGE : 
	
	public function stripCommentsAndMinify ($src) {
		$r = $src;
		
		$regxSearch = array(
			'#<!-- .*? -->#'//,
			//'#\s+#', // uncommenting this (and it's replacement in $regxReplace below here) will enable minification.
		);
		$regxReplace = array ( '');//, ' ' );
		
		$r = pregReplace($regxSearch, $regxReplace, $r);
		return $r;		
	}
	
	public function strip_strings ($source, $beginIdx) {
		$r = array();
		$stringsRemoved = array();
		$stringsRemovedCount = -1;
		$sourceForObfuscation = '';
		$debugI = false;
		
		for ($i=0; $i<strlen($source); $i++) {
			$c = substr($source, $i, 1);
			
			$c4 = substr($source, $i, 4);
			
			if (
				$c4 === '<!--'
			) {
				$j = $i + 5;
				$d3 = substr ($source, $j, 3);
				while ($d3 !== '-->' && $j < strlen($source)) {
					$j++;
					$d3 = substr ($source, $j, 3);
				}
				$i = $j + 3;
				$c = substr($source, $i, 1);
			}
			
			// $cBacklook = strtolower(substr($source, $i-15, 16));
			if (
				(
					$c === '\''
					|| $c == '"'
				) 
				/* && (
					0 === preg_match ('#id\s*\=\s'.$c.'#', $cBacklook)
					&& 0 === preg_match ('#class\s*\=\s'.$c.'#', $cBacklook)
				) */
				
			) {
				$j = $i + 1;
				$d = substr($source, $j, strlen($c));
				$stringToRemove = $c;
				
				if ($debugI!==false) {
					echo '<span style="color:purple;">'; echo ($c); echo '</span>';
				}
				if ($debugI!==false) {
					echo '<span style="color:red;">'; echo ($d); echo '</span>';
				}
				
				while ($d !== $c && $j < strlen($source)) {
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
				if ($debugI!==false) {
					echo '<span style="color:purple;">'; echo ($d); echo '</span>';
				}
				
				$stringsRemovedCount++;
				$stringsRemoved[$stringsRemovedCount] = $stringToRemove;
				$tmp = '`'.($beginIdx+$stringsRemovedCount).'`';
				$sourceForObfuscation .= $tmp;
				
				if ($debugI!==false) {
					$dbg1 = array (
						'dbg' => htmlentities($dbg),
						'$stringToRemove' => htmlentities($stringToRemove)
					);
					if ($debugI!==false && $i>$debugI && $i<$debugI+1000) { echo '<pre style="color:orange;font-weight:bold;">$dbg1.1=';var_dump ($dbg1);echo'</pre>'; }
				}
				
				$i = $j;
				//$sourceForObfuscation .= substr ($source, $i, 1);
				
				continue;
			} else {
				$sourceForObfuscation .= $c;
			}
					
			
			
		}
		
		$r = array (
			'sourceForObfuscation' => $sourceForObfuscation,
			'stringsRemoved' => $stringsRemoved
		);
		return $r;
		
	}
	
	public function strip_tags_returnNonTags ($src) {
		$r = array();
		$l = strlen($src);
		$sf = '';
		$tl = 0;
		$isScript = false;
		for ($i=0; $i<$l; $i++) {
			$c = substr($src, $i, 1);
			$scriptTest = substr($src, $i, 6);
			if ($scriptTest === 'script') $isScript = true;
			if ($c=='\\') {
			} elseif ($c=='<') {
				$tl++;
				if ($sf!=='') {
					if (!$isScript) $r[] = $sf;
					if ($isScript) $isScript = false;
				}
				$sf = '';
			} elseif ($c=='>') {
				$tl--;
			} elseif ($tl===0) {
				$sf .= $c;
			}
		}
		if ($tl===0 && $sf!=='') $r[] = $sf;
		
		//echo '<pre style="color:red;">rnt='; var_dump ($r);  die();
		
		$r2 = array();
		foreach ($r as $idx=>$nontag) {
			if (
				0===preg_match('#^\s+$#', $nontag)
			) $r2[] = trim($nontag);
		}
		
		//echo '<pre style="color:red;">rnt2='; var_dump ($r);  
		
		return $r2;
	}
	
	
}
?>
