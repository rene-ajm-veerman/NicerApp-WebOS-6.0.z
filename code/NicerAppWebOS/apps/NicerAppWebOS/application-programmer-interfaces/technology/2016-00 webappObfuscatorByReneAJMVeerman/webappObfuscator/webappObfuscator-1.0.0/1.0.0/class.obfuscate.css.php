<?php

class webappObfuscator_obfuscate__css {

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
	
		//echo '<pre>css 221:'; var_dump($wd['output']['stages'][0]); die();
		
		// the individual files from $sourcesURLs, obfuscated, written to $codePath
		// as a subdirectory structure that mirrors the URL folder structure
		$wd['output']['stages'][1] = $obfuscator->writeOutput__stage0__walk (
			'css', $codePaths, '', $wd['output']['stages'][0], $sourcesURLs['css']
		);
		
		$wd['output__concatenated']['stages'][1] = $obfuscator->writeOutput__stage0__walk (
			'css', $codePaths, '', $wd['output__concatenated']['stages'][0]
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
				&& $gs['sourcesType'] !== 'css'
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

		// here's your actual output :
		$wd['output']['stages'][0] = $this->obfuscate__stage0__walk ('', $wd['sources']['stages'][0]);

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
		$wd = &$this->workData;
		$s = &$this->settings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		
		if (is_array($sources)) {
			$r = array();
			foreach ($sources as $k => $v) {
				if (is_string($v)) {
					reportStatus (600, '<p class="webappObfuscator__process__detail">Obfuscating CSS - stage 1 (2 stages total) - processing '.formatSourcepath($path,$k).'</p>');
					$r[$k] = $this->obfuscate__stage0__node ($path, $k, $v);
				} else {
					$r[$k] = $this->obfuscate__stage0__walk ($path.'/'.$k, $sources[$k]);
				}
			}
		} elseif (is_string($sources)) {
			reportStatus (600, '<p class="webappObfuscator__process__detail">Obfuscating CSS - stage 1 (2 stages total) - processing '.formatSourcepath($path,$k).'</p>');
			$r = $this->obfuscate__produceOutput($path, $sources, $obfuscator->workData['tokens']);
		}
		return $r;
	}

	private function obfuscate__stage0__node ($path, $k, $src) {
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

		$obfuscatedSource = $obfuscator->phpJSO_replace_tokens ($allTokens, $source);
		$wd['obfuscatedSource'] = $obfuscatedSource;
		
		if (is_string($obfuscatedSource)) {
                    $size = strlen($obfuscatedSource);
                } elseif (is_array($obfuscatedSource)) {
                    $c = jsonEncode($obfuscatedSource);
                    $size = strlen ($c);
                }

		
		
		reportStatus (500, '<p class="webappObfuscator__process__detail">Obfuscating CSS - stage 2 (2 stages total) - processing '.formatSourcepath($path).' (<span class="webappObfuscator__filesizeHumanReadable">'.filesizeHumanReadable($size).'</span>)</p>');
		
		return $obfuscatedSource;
	}
	
	
	
	public function OLD___obfuscate () {
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
		foreach ($allSources as $sourcesDescription => $sources) {
			if (is_array($sources)) {
				$wd['output'][$sourcesDescription] = array();
				foreach ($sources as $sourceDescription => $source) {
					$minified = $this->stripCommentsAndMinify($source);
						reportStatus (500, 
							'<p class="webappObfuscator__process__detail">Obfuscating CSS - stage 1 of 1 -'
							.' obfuscating $sources["css"]["'.$sourcesDescription.'"]["'.$sourceDescription.'"]'
							.' ('.filesizeHumanReadable(strlen($minified)).')</p>'
						);
				
					$output = $obfuscator->phpJSO_replace_tokens ($allTokens, $minified);
					$wd['output'][$sourcesDescription][$sourceDescription] = $output;
				}
			} else {
				$minified = $this->stripCommentsAndMinify($sources);
					reportStatus (500, 
						'<p class="webappObfuscator__process__detail">Obfuscating CSS - stage 1 of 1 -'
						.' obfuscating $sources["css"]["'.$sourcesDescription.'"]'
						.' ('.filesizeHumanReadable(strlen($minified)).')</p>'
					);
			
				$output = $obfuscator->phpJSO_replace_tokens ($allTokens, $minified);
				$wd['output'][$sourcesDescription] = $output;
			}
		}
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
				&& $gs['sourcesType'] !== 'css'
			)
		) return false;
		
		
		global $lowerMemoryUsage;
		if (
			is_null($sources) 
			&& (
				!array_key_exists('sourcesType',$gs)
				|| $gs['sourcesType']==='mixed'
			)
		) $sources = &$gs['sources']['fetched']['css'];
		if (
			is_null($sources) 
			&& array_key_exists('sourcesType',$gs)
			&& $gs['sourcesType']!=='mixed'
		) $sources = &$gs['sources'];
		
		//echo '<pre>34241='; var_dump ($sources); die();
		
		
		$wd['sources'] = array (
			'stages' => array (
				0 => $sources,
				1 => '',
				2 => ''
			)
		);
		$wd['sourceForObfuscation'] = '';
		
		
		reportStatus (300, '<p class="webappObfuscator__process__detail">Gathering tokens in CSS - stage 1 of 3 - walking sources</p>');
		if (is_string($sources)) {
			$this->preprocess__stage1__node ('', '', $sources, $wd['sources']['stages'][1], $wd['sourceForObfuscation']);

		} elseif (is_array($sources)) {
			$this->preprocess__stage1__walk ('', $sources, $wd['sources']['stages'][1], $wd['sourceForObfuscation']);
			
		} else {
			return badResult (E_USER_ERROR, array (
				'msg' => 'webappObfuscator_obfuscate__css::preprocess() : invalid stage0 settings',
				'$wd' => $wd
			));
		}
		
		
		$this->preprocess__stage1__walk ('', $sources, $wd['sources']['stages'][1], $wd['sourceForObfuscation']);
		//echo '<pre>6661='; var_dump ($wd['sources']['stages'][1]); echo '</pre>';
		
		reportStatus (300, '<p class="webappObfuscator__process__detail">Gathering tokens in CSS - stage 2 of 3 - stripping out what we don\'t need</p>');
		$this->preprocess__stage2 ($wd['sources']['stages'][1], $wd['sources']['stages'][2]);
		//echo '<pre>6662='; var_dump ($wd['sources']['stages'][2]); echo '</pre>';

		reportStatus (300, '<p class="webappObfuscator__process__detail">Gathering tokens in CSS - stage 3 of 3 - regular tokens</p>');
		$this->preprocess__stage3 ($wd['sources']['stages'][2], $wd['tokens'] );
		//echo '<pre>6663='; var_dump ($wd['tokens']); echo '</pre>';
		
		
		reportTokens (700, $wd['tokens'], 'webappObfuscator_obfuscate__css::preprocess() finalized CSS $tokens');
		
		//if ($lowerMemoryUsage) unset ($wd['sources']['stages']);
	}

	
	private function preprocess__stage1__walk ($path, &$sources, &$wdo, &$wdos) {
		foreach ($sources as $k => $v) {
 			if (is_string($v)) {
				$this->preprocess__stage1__node ($path, $k, $v, $wdo, $wdos);
			} else {
				$this->preprocess__stage1__walk ($path.'/'.$k, $sources[$k], $wdo, $work);
			}
		}
	}
	
	private function preprocess__stage1__node ($path, $k, &$source, &$wdo, &$wdos) {
		$src = $source;
		$wdo .= $src."\r\n";
		$wdos .= $src."\r\n";
	}
	
	private function preprocess__stage2 (&$source, &$wdo) {
		$r = $source;
		$r = preg_replace ('/\{[^}]+\}/', '', $r);
		$r = preg_replace ('#\/\*[^*/]+\*\/#', '', $r);
		$r = preg_replace ('#\s+#', ' ', $r);
		//$r = preg_replace ('/#/', '', $r);
		//$r = preg_replace ('/\./', '', $r);
		$wdo = $r;
	}
	
	
	private function preprocess__stage3 (&$source, &$wdo) {
		global $wo__tokens__ignoreList__allLowercase;
		global $minTokenLength;
		$r = explode(',', $source);
		$r2 = array();
		foreach ($r as $k => $v) {
			$p = preg_split('#\s+#', $v);
			//echo'<pre>66631'; var_dump ($v); var_dump ($p);echo'</pre>'; 
			$r2 = array_merge ($r2, $p);
		};
		//echo'<pre>66632'; var_dump ($r2);echo'</pre>'; 
		$r3 = array_unique($r2);
		//echo'<pre>66633'; var_dump ($r3);echo'</pre>'; 
		$r4 = array();
		foreach ($r3 as $k3 => $v3) {
			if (
				!is_numeric($v3) 
				&& (
					substr($v3, 0, 1) === '#'
					|| substr($v3, 0, 1) === '.'
				)
				&& strlen($v3) >= $minTokenLength
				&& array_search (strtolower($v3), $wo__tokens__ignoreList__allLowercase)===false
			) $r4[] = substr($v3,1);
		}
		
		//echo'<pre>66634'; var_dump ($r4);echo'</pre>'; 
		$wdo = $r4;
		return $wdo;
	}
	
	
	// OBFUSCATE() stage :
	public function stripCommentsAndMinify ($src) {
		$r = $src;
		
		$regxSearch = array(
			'#/\*.*?\*/#'//,
			//'#\s+#', // uncommenting this (and it's replacement in $regxReplace below here) will enable minification.
		);
		$regxReplace = array ( '');//, ' ' );
		
		$r = pregReplace($regxSearch, $regxReplace, $r);
		return $r;		
		
	}
}

?>
