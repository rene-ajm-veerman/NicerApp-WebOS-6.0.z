<?php
//require_once(dirname(__FILE__).'/webappObfuscator.globals.php');


require_once(dirname(__FILE__).'/../../errorHandling_php/boot.php');
require_once(dirname(__FILE__).'/1.0.0/functions.php');
require_once(dirname(__FILE__).'/1.0.0/class.obfuscate.css.php');
require_once(dirname(__FILE__).'/1.0.0/class.obfuscate.html.php');
require_once(dirname(__FILE__).'/1.0.0/class.obfuscate.javascript.php');
//require_once(dirname(__FILE__).'/1.0.0/class.obfuscate.json.php');

require_once(dirname(__FILE__).'/1.0.0/class.website.php');

require_once(dirname(__FILE__).'/1.0.0/token.whitelist.misc.php'); // anything that don't fit in it's own category can go in this file

require_once(dirname(__FILE__).'/1.0.0/token.whitelist.seductiveapps.php'); 
//require_once(dirname(__FILE__).'/1.0.0/token.whitelist.filesystem.php'); 

require_once(dirname(__FILE__).'/1.0.0/token.whitelist.browser.firefox.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.browser.internetExplorer.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.canvas.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.company.google.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.css.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.html.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.javascript.php');
//require_once(dirname(__FILE__).'/1.0.0/token.whitelist.json.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.lib.jquery.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.lib.jquery-history.php');
require_once(dirname(__FILE__).'/1.0.0/token.whitelist.lib.tinymce.php');

// $wo__ === Webapp Obfuscator global variable
global $wo__ignoreList__site;
if (!isset($wo__ignoreList__site)) $wo__ignoreList__site = array();
$wo__tokens__ignoreList = array_unique(array_merge(
	// ignoreList === whitelist
	$wo__ignoreList__site,
	//$wo__ignoreList__filesystem, 
	$wo__ignoreList__misc, 
	$wo__ignoreList__browser__firefox,
	$wo__ignoreList__browser__internetExplorer,
	$wo__ignoreList__canvas, // language
	$wo__ignoreList__company__google,
	$wo__ignoreList__css, // language
	$wo__ignoreList__html, // language
	//$wo__ignoreList__json, // language or dataformat? both. hence no specification of that in the variable name.
	$wo__ignoreList__javascript, // language
	$wo__ignoreList__lib__jquery,
	$wo__ignoreList__lib__jquery_history,
	$wo__ignoreList__seductiveapps, // seductiveapps.com
	$wo__ignoreList__lib__tinymce // richtext aka wysiwyg texteditor for in the browser.. tinymce.moxiecode.com
)); 
global $wo__tokens__ignoreList;
$wo__tokens__ignoreList__allLowercase = array_map ('strtolower', $wo__tokens__ignoreList);
global $wo__tokens__ignoreList__allLowercase;




class webappObfuscator {
	
	public $factorySettings = null;
	public $clientSettings = null;
	private $workClasses = array();
	public $workData = array();
	public $output = null;

	public function __construct ($settings) {
		$this->factorySettings = $settings;
	
		$settingsHTML = array(
			'obfuscator' => &$this,
			'globalSettings'  => &$this->clientSettings
		);
		$this->workClass['obfuscate__html'] = array (
			'settings' => $settingsHTML,
			'class' => new webappObfuscator_obfuscate__html ($settingsHTML)
		);
		
		$settingsCSS = array(
			'obfuscator' => &$this,
			'globalSettings'  => &$this->clientSettings
		);
		$this->workClass['obfuscate__css'] = array (
			'settings' => $settingsCSS,
			'class' => new webappObfuscator_obfuscate__css ($settingsCSS)
		);
		
		$settingsJSON = array(
			'obfuscator' => &$this,
			'globalSettings'  => &$this->clientSettings
		);
		/*
		$this->workClass['obfuscate__json'] = array (
			'settings' => $settingsJSON,
			'class' => new webappObfuscator_obfuscate__json ($settingsJSON)
		);*/
		
		$settingsJavascript = array(
			'obfuscator' => &$this,
			'globalSettings'  => &$this->clientSettings
		);
		$this->workClass['obfuscate__javascript'] = array (
			'settings' => $settingsJavascript,
			'class' => new webappObfuscator_obfuscate__javascript ($settingsJavascript)
		);
		
		$this->prepareObfuscatedToken_characterSourcePool();
	}
	
	public function clearCachefiles () { //TODO : fill in
	
	}
	
	public function fetchSources () { // TODO : fill in
	}
	
	public function setClientSettings ($settings) {
	  $this->clientSettings = $settings;
	  //echo '$this->clientSettings=<pre>'; var_dump($settings); echo '</pre>'; die();
	}

	public function getWorker ($language) {
		$id = 'obfuscate__'.$language;
		return $this->workClass[$id]['class'];
	}
	
	public function getWorkerSettings ($language) {
		$id = 'obfuscate__'.$language;
		return $this->workClass[$id]['settings'];
	}
	
	public function readTokens ($codePaths=null) {
		$r = array('details'=>array());
		
		$codePathCheck = $this->checkcodePaths($codePaths, 'webappObfuscator::readTokens()');
		if (good($codePathCheck)) { // see webappObfuscator/webappObfuscator-1.0.0/functions__internalErrorHandling.php
			$codePaths = result($codePathCheck);
		} else {
			return false;
		}
		//echo '$codePaths=<pre>'; var_dump ($codePaths); echo '</pre>'; die();
		
		$readCmds = array (
			'tokens' => $codePaths['secretOutput'].'/all.webappObfuscator__obfuscatedTokens.json',
			'tokens__css_html' => $codePaths['secretOutput'].'/css_html.webappObfuscator__obfuscatedTokens.json'
		);
		if (array_key_exists('tokens', $this->workData)) {
                    $r['success'] = true;
                    $r['msg'] = 'already have data in workmemory - did not read any files.';
                    return goodResult($r); // see webappObfuscator/webappObfuscator-1.0.0/		
		}
		//echo '$r=<pre>'; var_dump ($r); echo '</pre>'; die();
		//echo '$readCmds=<pre>'; var_dump ($readCmds); echo '</pre>'; die();
		
		foreach ($readCmds as $k => $filepath) {
			if (!file_exists($filepath)) {
				return false;
				
				/*return badResult (E_USER_NOTICE, array ( // see webappObfuscator/webappObfuscator-1.0.0/functions__internalErrorHandling.php
					'msg' => 'webappObfuscator::readTokens() file "'.$filepath.'" does not exist!'
				));*/
			}
			$jsonAsPHParray = jsonDecode(file_get_contents($filepath),true);
			if (!is_array($jsonAsPHParray)) {
				return badResult (E_USER_ERROR, array (
					'msg' => 'webappObfuscator::readTokens() file "'.$filepath.'" is not valid JSON!',
					'$jsonAsPHParray' => $jsonAsPHParray,
					'json_last_error()' => wo_php_json_last_error_humanReadable(json_last_error())
				));
			}
			
			$this->workData[$k] = $jsonAsPHParray;
			$r['details']['loaded '.$k] = array (
				'from file' => $filepath,
				'$my->workData["'.$k.'"]' => $jsonAsPHParray
			);
		}

		$r['success'] = true;
		$r['msg'] = 'read in '.count($readCmds).' JSON files.';
		$r['$readCmds'] = $readCmds;
		return goodResult($r); // see webappObfuscator/webappObfuscator-1.0.0/functions__internalErrorHandling.php
	}	

	public function checkcodePaths ($paths=null, $calledWhere) {
		$instructions = 'All paths must be a string containing a full harddisk filepath (starting at the root of the filesystem';
		if (is_null($paths)) {
			if (
				array_key_exists('paths',$this->clientSettings)
				&& array_key_exists('secretOutput',$this->clientSettings['paths'])
				&& array_key_exists('publicOutput',$this->clientSettings['paths'])
			) {
				$paths = $this->clientSettings['paths'];
			}
		} else if (is_array($paths)) {
			if (
				array_key_exists('paths',$paths)
				&& array_key_exists('secretOutput',$paths)
				&& array_key_exists('publicOutput',$paths)
			) {
				$paths = $paths['paths'];
			}
		}

		foreach ($paths as $pathDescription => $path) {
			if (
				!is_string($path)
				|| $path===''
				|| (
					substr($path,0,1)!=='/' // unizzz
					&& substr($path,0,2)!=='\\\\' // windowze (network path)
					&& substr($path,1,2)!==':\\' // windowze (drive letter path)
				)
			) {
				return badResult (E_USER_ERROR, array(
					'msg' => $calledWhere.' : '.$instructions,
					'$paths' => $paths,
					'$pathDescription' => $pathDescription,
					'$path' => $path
				));
			}
		}
		$r = array (
			'secretOutput' => $paths['secretOutput'],
			'publicOutput' => $paths['publicOutput']
		);
		return goodResult($r);
	}
	

	
	
	public function writeTokensToDisk ($writeCmds=null) {
		global $useUnicodeIdentifiers;
	
		if (is_null($writeCmds)) {
			$writeCmds = array (
				'all' => array (
					'tokens' => $this->workData['tokens'],
					'filepath' => $this->clientSettings['paths']['secretOutput'].'/all.webappObfuscator__obfuscatedTokens.json'
				),
				'css_html' => array (
					'tokens' => $this->workData['tokens__css_html'],
					'filepath' => $this->clientSettings['paths']['secretOutput'].'/css_html.webappObfuscator__obfuscatedTokens.json'
				)
			);
		}
		
		foreach ($writeCmds as $wcID => $wc) {
			$tokens = $wc['tokens'];
			if ($useUnicodeIdentifiers) jsonPrepareUnicode($tokens);
			
			$fp = $wc['filepath'];
			createDirectoryStructure (dirname($fp));
			reportStatus (300, 
				'<p class="webappObfuscator__process__writeOutput" class="webappObfuscator__process">'
				.'Writing out obfuscation output (token translation list)<br/>'
				.'<span class="webappObfuscator__outputFilename">"'.$fp.'"</span><br/>'
				.'</p>'
			);
			$json = jsonEncode($tokens);
			$result = file_put_contents ($fp, $json);
			$newKey = "file_put_contents ('$fp',".' $json)';
			$writeCmds[$wcID][$newKey] = $result;
		}
		return $writeCmds;
	}

	
	public function writeOutputToDisk ($codePaths=null) {
		foreach ($this->workClass as $wcID => $worker) {
			$worker['class']->writeOutputToDisk ($codePaths);
		}
	}
	
	
	public function writeOutput__stage0__walk ($programmingLanguageName, $codePaths, $sourceIDpath, $sources, $sourcesURLs=null) {
		$wd = &$this->workData;
		$s = &$this->clientSettings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;

		$codePathCheck = $this->checkcodePaths($codePaths, 'webappObfuscator::writeOutput__stage0__walk("'.$programmingLanguageName.'", .....)');
		if (good($codePathCheck)) {
			$codePaths = result($codePathCheck);
		} else {
			return false;
		}
		
		
		// for anything else than javascript obfuscation, use $k in $sources as the output filename 
			//(not the URL where the $sources node came from).
		if (
			$programmingLanguageName!=='javascript'
			&& $programmingLanguageName!=='json'
			&& $programmingLanguageName!=='css'
		) $sourcesURLs = null; 
		
		reportStatus (1, '<h1 class="webappObfuscator__process__writeOutput webappObfuscator__process__detail__scanningNextPath">Writing '.$programmingLanguageName.' output ('.$sourceIDpath.') </h1>');
		
		//echo '<pre style="color:orange;background:blue;">$sourcesURLs='; var_dump ($sourcesURLs); echo '</pre>';
		//echo '<pre style="color:orange;background:blue;">$sources='; var_dump ($sources); echo '</pre>';
		
		
		$r = array();
		if (is_array($sources)) {
                    foreach ($sources as $k => $v) {
                        // hm... $sourcesURLs2 = is_array($sourcesURLs) ? $sourcesURLs[$k] : is_string($sourcesURLs) ? $sourcesURLs : null;
                        $sourcesURLs2 = $sourcesURLs[$k];
                        //echo '<pre>$obfuscator->writeOutput__stage0__walk() : $sourcesURLs2='; var_dump ($sourcesURLs2); echo '$k='; var_dump ($k); 	echo '</pre>';
                        if (is_string($v)) {
                                reportStatus (1, '<p class="webappObfuscator__process__writeOutput webappObfuscator__process__detail">Writing '.$programmingLanguageName.' output '.formatSourcepath($sourceIDpath, $k).'</span> to :<br/>');
                                $r[$k] = $this->writeOutput__stage0__node ($programmingLanguageName, $codePaths, $sourceIDpath, $k, $v, $sourcesURLs2);
                                reportStatus (1, '</p>');
                        } if (is_array($v)) {
                                $r[$k] = $this->writeOutput__stage0__walk ($programmingLanguageName, $codePaths, $sourceIDpath.'/'.$k, $v, $sourcesURLs2);
                        }
                    }
		} elseif (is_string($sources)) {					                                 
                    reportStatus (1, '<p class="webappObfuscator__process__writeOutput webappObfuscator__process__detail">Writing '.$programmingLanguageName.' output '.formatSourcepath($sourceIDpath).' to:<br/>');
                    $r = $this->writeOutput__stage0__node ($programmingLanguageName, $codePaths, '', '', $sources);
                    reportStatus (1, '</p>');
		}
		return $r;
	}
	
	public function writeOutput__stage0__node ($programmingLanguageName, $codePaths, $sourceIDpath, $k, $sourceObfuscated, $sourceURL=null) {
		$wd = &$this->workData;
		$s = &$this->clientSettings;
		$gs = &$s['globalSettings'];
		$obfuscator = &$s['obfuscator'];
		global $lowerMemoryUsage;
		global $reportStatusGoesToStatusfile;
		//echo '<pre style="color:orange;background:blue;">$programmingLanguageName='; var_dump ($programmingLanguageName); echo '</pre>';

		$sourceIDpathComplete = $sourceIDpath.'/'.$k;
		$codePath = $codePaths['publicOutput'];
		//echo '<pre style="color:orange;background:blue;">$sourceURL='; var_dump ($sourceURL); echo '</pre>';
		
		if (!is_null($sourceURL)) {
			$sourceRelativeURL = $sourceURL;
			$sourceRelativeURL = str_replace ($gs['sourceServer'], '', $sourceRelativeURL);
			$sourceRelativeURL = str_replace ('http://', '', $sourceRelativeURL);
			$sourceRelativeURL = str_replace ('https://', '', $sourceRelativeURL);
			
			/*
			echo '<pre>201 $codePaths='; var_dump ($codePaths); echo '</pre>';
			echo '<pre>201 $programmingLanguageName='; var_dump ($programmingLanguageName); echo '</pre>';
			echo '<pre>201 $sourceRelativeURL='; var_dump ($sourceRelativeURL); echo '</pre>';
			*/
			
			$outputPath = $codePath.'/'.$programmingLanguageName.'/'.$sourceRelativeURL;
		} else {
			$ext = $programmingLanguageName; 
			if ($programmingLanguageName==='javascript') $ext = 'js';
			$outputPath = $codePath.'/'.$programmingLanguageName.'/'.$sourceIDpathComplete.'.'.$ext;
		}

		//$cds = createDirectoryStructure (dirname($outputPath));
		//echo '<pre style="color:orange;background:blue;">$cds='; var_dump ($cds); echo '</pre>';

		$sourceObfuscated = html_entity_decode($sourceObfuscated);
		createDirectoryStructure (dirname($outputPath));
		$bytesWritten = file_put_contents ($outputPath, $sourceObfuscated);
		
		$r = array (
			"resultFor:::bytesWrittenToFile:::file_put_contents ('$outputPath'))" => $bytesWritten
		);
		reportStatus (300, 
                    '<span class="webappObfuscator__outputFilename">" '.$outputPath.'</span> " (<span class="webappObfuscator__filesizeHumanReadable">'.filesizeHumanReadable($bytesWritten).'</span>)<br/>'
		);
		//echo '2341.1 = <pre style="color:yellow;background:navy;">'; var_dump ($sourceObfuscated); echo '</pre>';
		//echo '2341.2 = <pre style="color:lime;background:navy;">'; var_dump ($r); echo '</pre>';
		return $r;
	}
	
	public function obfuscateString ($source, $sourceType=null) {
            $wd = &$this->workData;
            //echo '$wd=<pre>'; var_dump ($wd); echo '</pre>'; die();
            global $cacheFile_workData;
            global $lowerMemoryUsage;
            
            if (is_string($sourceType)) {
                $this->clientSettings['sourcesType'] = $sourceType;
            }

            $gs = &$this->clientSettings;
            if (!array_key_exists('tokens',$wd)) {
                return false;
                return badResult (E_USER_ERROR, '$wd["tokens"] has not been loaded with $this->readTokens();');
            }
            
            if (
                $this->clientSettings['sourcesType'] === 'mixed'
                || $this->clientSettings['sourcesType'] === 'css'
            ) $wCSS = $this->getWorker ('css');
            if (
                $this->clientSettings['sourcesType'] === 'mixed'
                || $this->clientSettings['sourcesType'] === 'html'
            ) $wHTML = $this->getWorker ('html');
            if (
                $this->clientSettings['sourcesType'] === 'mixed'
                || $this->clientSettings['sourcesType'] === 'javascript'
            ) $wJavascript = $this->getWorker ('javascript');
            if (
                $this->clientSettings['sourcesType'] === 'mixed'
                || $this->clientSettings['sourcesType'] === 'json'
            ) $wJSON = $this->getWorker ('json');
            
            if ($sourceType==='css') {
                $r = $wCSS->obfuscate__produceOutput('webappObfuscator->obfuscateString()', $source, $this->workData['tokens']);
            } 
            elseif ($sourceType==='html') {
                $r = $wHTML->obfuscate__produceOutput('webappObfuscator->obfuscateString()', $source, $this->workData['tokens']);
            } 
            elseif ($sourceType==='javascript') {
                //echo '$wJavascript=<pre>'; var_dump ($wJavascript); echo '</pre>'; die();
                $rPrecursor = $wJavascript->getTokensAndStrings($source);
                $r = $wJavascript->obfuscate__stage0__node ('', 'webappObfuscator->obfuscateString()', $rPrecursor);
            } 
            elseif ($sourceType==='json') {
                $r = $wJSON->obfuscate__produceOutput('webappObfuscator->obfuscateString()', $source, $this->workData['tokens']);
            }
            return $r;
	}
	
	public function obfuscate ($sources=null, $sourcesType=null) {
		/* PARAMETERS : 
			$sourcesType possible values : 
				'html'     		------> $sources must be string value
				'css' 			------> $sources must be string value
				'javascript'	------> $sources must be string value
				'json'			------> $sources must be string value
				'mixed'   		---> $sources must be as if $sources===null (multi-level array)
		*/
		global $wo__tokens__ignoreList;
		if (
		  !isset($wo__ignoreList__site)
		  || !is_array($wo__ignoreList__site)
		) {
		  $wo__ignoreList__site = array();
		}
		$wo__tokens__ignoreList = array_unique(array_merge(
		  $wo__tokens__ignoreList,
		  $wo__ignoreList__site
		));
		$wo__tokens__ignoreList__allLowercase = array_map ('strtolower', $wo__tokens__ignoreList);
		global $wo__tokens__ignoreList__allLowercase;
		
		//var_dump ($sourcesType); die();
		
		if (
			is_string($sources) 
			|| is_array($sources)
		) {
			$this->clientSettings['sources'] = $sources;
		};
		if (!array_key_exists('sources', $this->clientSettings)) {
			return badResult (E_USER_ERROR, 'webappObfuscator::obfuscate() : user did not provide any sources to obfuscate.');
		}
		if (is_string($sourcesType)) {
			$this->clientSettings['sourcesType'] = $sourcesType;
		} 
		
		//var_dump (array_key_exists('sourcesType', $this->clientSettings));
		//var_dump (is_array($this->clientSettings['sources']));
		
		if (!array_key_exists('sourcesType', $this->clientSettings)) {
			if (is_array($this->clientSettings['sources'])) {
				$this->clientSettings['sourcesType'] = 'mixed';
			} elseif (is_string($this->clientSettings['sources'])) {
				return badResult (E_USER_ERROR, 
					'webappObfuscator::obfuscate() : $this->clientSettings["sourcesType"]=\''.$this->clientSettings['sourcesType']
					.'\' --- INVALID for : is_string($this->clientSettings["sources"])===true, must be one of : css, html, javascript, json'
				);
			} else {
				return badResult (E_USER_ERROR, array(
					'msg' => 'webappObfuscator::obfuscate() : invalid $this->clientSettings',
					'$this->clientSettings', $this->clientSettings
				));
			}
		}
		
		$gs = &$this->clientSettings;
		
		//echo '101.4 - $gs=<pre>'; var_dump ($gs); echo '</pre>';
		
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'css'
		) $wCSS = $this->getWorker ('css');
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'html'
		) $wHTML = $this->getWorker ('html');
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'javascript'
		) $wJavascript = $this->getWorker ('javascript');
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'json'
		) $wJSON = $this->getWorker ('json');
		$wd = &$this->workData;
		global $cacheFile_workData;
		global $lowerMemoryUsage;

		reportStatus (300, '<h1 class="webappObfuscator__calculating">Gathering tokens</h1>');
		
		
		if (!array_key_exists('tokens',$wd)) {
		
		if ( // OBFUSCATE (or not!) JSON data first, as the possible whitelisted JSON keys should be processed first
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'json'
		) $wJSON->preprocess(); 
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'css'
		) $wCSS->preprocess();
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'html'
		) $wHTML->preprocess();
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'javascript'
		) $wJavascript->preprocess();
		//echo 'suc6'; die();
		
		global $webappObfuscator__clientSettings;
		//echo '$this->clientSettings=<pre>'; var_dump($webappObfuscator__clientSettings); echo '</pre>';// die();
		// F-ing weird -----> echo '$this->clientSettings=<pre>'; var_dump($this->clientSettings['do_obfuscate']); echo '</pre>'; die();
		
		//$webappObfuscator__clientSettings
		
		
			$wd = array (
				'tokens' => array_unique(array_merge (
                                    $this->clientSettings['sourcesType'] === 'mixed'
                                    || $this->clientSettings['sourcesType'] === 'css'	
                                        ? $wCSS->workData['tokens'] 
					: array(),
					  
                                    $this->clientSettings['sourcesType'] === 'mixed'
                                    || $this->clientSettings['sourcesType'] === 'html'	
                                        ? $wHTML->workData['tokens']
                                        : array(),
					  
                                    $this->clientSettings['sourcesType'] === 'mixed'
                                    || $this->clientSettings['sourcesType'] === 'javascript'	
                                        ? $wJavascript->workData['tokens']
                                        : array(),
					  
                                    $this->clientSettings['sourcesType'] === 'mixed'
                                    || $this->clientSettings['sourcesType'] === 'json'	
                                        ? $wJSON->workData['tokens']
                                        : array()
				))
                        );
			/*
			$wd = array (
				'tokens' => array_unique(array_merge (
                                    array_search('css', $this->clientSettings['obfuscate'], true)!==false
                                    ?	$this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'css'	
					  ? $wCSS->workData['tokens'] 
					  : array()
                                    : array(),
					  
                                    array_search('html', $this->clientSettings['obfuscate'], true)!==false
                                    ?   $this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'html'	
					  ? $wHTML->workData['tokens']
					  : array()
                                    : array(),
					  
                                    array_search('js', $this->clientSettings['obfuscate'], true)!==false
                                    ?   $this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'javascript'	
					  ? $wJavascript->workData['tokens']
					  : array()
                                    : array(),
					  
                                    array_search('json', $this->clientSettings['obfuscate'], true)!==false
                                    ?   $this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'json'	
					? $wJSON->workData['tokens']
					: array()
                                    : array()
				))
				, 'tokens_whiteListed' => array_unique(array_merge(
                                    array_search('css', $this->clientSettings['obfuscate'], true)===false
                                    ?	$this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'css'	
					  ? $wCSS->workData['tokens'] 
					  : array()
                                    : array(),
					  
                                    array_search('html', $this->clientSettings['obfuscate'], true)===false
                                    ?   $this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'html'	
					  ? $wHTML->workData['tokens']
					  : array()
                                    : array(),
					  
                                    array_search('js', $this->clientSettings['obfuscate'], true)===false
                                    ?   $this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'javascript'	
					  ? $wJavascript->workData['tokens']
					  : array()
                                    : array(),
					  
                                    array_search('json', $this->clientSettings['obfuscate'], true)===false
                                    ?   $this->clientSettings['sourcesType'] === 'mixed'
					|| $this->clientSettings['sourcesType'] === 'json'	
					? $wJSON->workData['tokens']
					: array()
                                    : array()
				))
				//, 'sourceForObfuscation' => array_merge(allworkers[sourceForObfuscation])
			);
			*/
			
			
			
			/*if (
				array_key_exists('lowerMemoryUsage', $this->clientSettings) 
				&& $this->clientSettings['lowerMemoryUsage']===true
			) {
				if (array_key_exists('sourceForObfuscation',$wCSS->workData)) unset ($wCSS->workData['sourceForObfuscation']);
				if (array_key_exists('sourceForObfuscation',$wHTML->workData)) unset ($wHTML->workData['sourceForObfuscation']);
				// NEED THIS AFTERALL if (array_key_exists('sourceForObfuscation',$wJavascript->workData)) unset ($wJavascript->workData['sourceForObfuscation']);
				if (array_key_exists('sourceForObfuscation',$wJSON->workData)) unset ($wJSON->workData['sourceForObfuscation']);
				if (array_key_exists('sourceForObfuscation',$this->workData)) unset ($this->workData['sourceForObfuscation']);
			};*/
			
			$wd['tokens'] = $this->cleanupTokenList ($wd['tokens']);
			usort($wd['tokens'],'sortByStringLength');
			
			//echo '<pre>pre-obfuscate : '; var_dump ($wd['tokens']); die();
			
                        reportStatus (300, '<h1 class="webappObfuscator__calculating">Obfuscating tokens ('.count($wd['tokens']).' tokens to do)</h1>');
                        $wd['tokens'] = $this->obfuscateTokens ($wd['tokens'], $wd['sourceForObfuscation']);
			
			reportTokens (300, $wd['tokens'], 'webappObfuscator::obfuscate() : finalized token list');
			//echo 'suc6-1';die();
			
			// handy to have these 2 seperate i guess.. :
			if (
			  $this->clientSettings['sourcesType'] === 'mixed'
			  || $this->clientSettings['sourcesType'] === 'css'	
			) {
			  $wd['tokens__css'] = $this->findObfuscatedTokens ($wd['tokens'], array (
				  $wCSS->workData['tokens']
			  ));
			};
			
			if (
			  $this->clientSettings['sourcesType'] === 'mixed'
			  || $this->clientSettings['sourcesType'] === 'html'	
			) {
			  $wd['tokens__html'] = $this->findObfuscatedTokens ($wd['tokens'], array (
				  $wHTML->workData['tokensHTML']
			  ));
			};
			
			// used for obfuscation of strings inside javascript
			if (
			  $this->clientSettings['sourcesType'] === 'mixed'
			) {
			  $wd['tokens__css_html'] = array_unique(array_merge(
				  $wd['tokens__css'],
				  $wd['tokens__html']
			  ));
			}
		}
		
		//echo '101.3 - $this->clientSettings<pre>'; var_dump ($this->clientSettings); echo '</pre>';
		reportStatus (300, '<h1 class="webappObfuscator__calculating">Obfuscating code with obfuscated tokens</h1>');
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'html'
		) $wHTML->obfuscate();
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'css'
		) $wCSS->obfuscate();
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'javascript'
		) $wJavascript->obfuscate();
		if (
		  $this->clientSettings['sourcesType'] === 'mixed'
		  || $this->clientSettings['sourcesType'] === 'json'
		) $wJSON->obfuscate();
		
		$output__sources = 
			is_array($gs['sources'])
			&& array_key_exists('urls', $gs['sources'])
			? $gs['sources']['urls']
			: $gs['sources'];
		
		$output = array (
			'sources' => $output__sources,
			'workData__workers' => array (
			  'html' => 
			    $this->clientSettings['sourcesType'] === 'mixed'
			    || $this->clientSettings['sourcesType'] === 'html'
			      ? $wHTML->workData
			      : array(),
			    
			  'css' => 
			    $this->clientSettings['sourcesType'] === 'mixed'
			    || $this->clientSettings['sourcesType'] === 'html'
			      ? $wCSS->workData
			      : array(),
			    
			  'javascript' => 
			    $this->clientSettings['sourcesType'] === 'mixed'
			    || $this->clientSettings['sourcesType'] === 'html'
			      ? $wJavascript->workData
			      : array(),
			      
			  'json' => 
			      $this->clientSettings['sourcesType'] === 'mixed'
			      || $this->clientSettings['sourcesType'] === 'html'
				? $wJSON->workData
				: array()
			),
			'workData' => $wd
		);
		//echo '<pre style="color:lime;background:blue">';var_dump ($output);echo '</pre>'; die();
		
		$this->output = $output;

                if (
                    is_string($cacheFile_workData)
                    && $cacheFile_workData!=''
                ) {
                    $cache_output = $output;
                    jsonPrepareUnicode ($cache_output);
                    $json = jsonEncode($cache_output);
                    reportStatus (300, '<p class="webappObfuscator__writeCache">Writing output to cache file "'.$cacheFile_workData.'" ('.filesizeHumanReadable(strlen($json)).')</p>');
                    createDirectoryStructure (dirname($cacheFile_workData));
                    file_put_contents ($cacheFile_workData, $json);
		}

		//$wd['obfuscatedSource'] = $output;
		return $output;
	}
	
	/*public function obfuscateArray ($arr, $tokens, $r = array(), $path='', $dbg=1) {
		// takes forever, runs out of memory 
		return false;
	
		$wd = &$this->workData;
		if (is_array($arr)) {
			foreach ($arr as $k => $v) {
				//echo '<pre>$k1=';var_dump ($k); echo '$v1='; var_dump ($v); echo '</pre>';	
				$k2 = $k;
				$this->phpJSO_replace_tokens($tokens, $k2);
				if (is_array($v)) {
					echo '<pre>$k=';var_dump ($k); echo '</pre>';	
					$r[$k2] = $this->obfuscateArray($arr[$k], $tokens, $r, $path.'/'.$k, ++$dbg);
					var_dump ($path); var_dump ($dbg);
					if ($dbg==20) die();
				} else {
					$v2 = $v;
					$this->phpJSO_replace_tokens($tokens, $v2);
					echo '<pre>$k=';var_dump ($k); echo '$v='; var_dump ($v); echo '</pre>';	
					$r[$k2] = $v2;
				}
			}
		}
		return $r;
	}*/
	
	public function findObfuscatedTokens ($allTokens_obfuscated, $unobfuscatedTokensLists) {
		$r = array();
		foreach ($unobfuscatedTokensLists as $utlIdx => $unobfuscatedTokensList) {
			foreach ($unobfuscatedTokensList as $utIdx => $unobfuscatedToken) {
				if (array_key_exists($unobfuscatedToken, $allTokens_obfuscated)) {
					$r[$unobfuscatedToken] = $allTokens_obfuscated[$unobfuscatedToken];
				} else {
					$r[$unobfuscatedToken] = $unobfuscatedToken;
				}
			}
		}
		return $r;
	}
	
	private function cleanupTokenList ($tokens) {
		// array_unique() has been called on $tokens.
		global $wo__tokens__ignoreList__allLowercase;
		//echo 't7901'; var_dump ($wo__tokens__ignoreList__allLowercase);die();
		
		$r = array();
		foreach ($tokens as $idx => $token) {
			if (
				$token !== ''
				
				&& !is_numeric($token)
				
				&& array_search(strtolower($token), $wo__tokens__ignoreList__allLowercase)===false
				
				&& (
				// no HTML escape sequences like &amp; as tokens
					0 === preg_match ('#^&[A-Za-z0-9]{3,4};$#', $token)
				)
				&& (
				// no HTML color IDs as tokens
					0 === preg_match ('_^#[A-Fa-f0-9]{3}|[A-Fa-f0-9]{6}$_', $token)
					//	&& 0 === preg_match( '_[A-Fa-f0-9][A-Fa-F0-9][A-Fa-F0-9][A-Fa-F0-9][A-Fa-F0-9][A-Fa-F0-9]_', $token)
				)
				&& (
				// dont use relative URLs for tokens
					0===preg_match('#[><\'"\\{\\}\\(\\)\\[\\]\\\\]#', $token)
				)
				&& (
				// dont use URLs for tokens..
					// many thanks to 
						// @diegoperini (author of the regexp below)
						// https://mathiasbynens.be/demo/url-regex (guy who tested a ton of regexp)
					0===preg_match('_^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/[^\s]*)?$_iuS', $token)
				)
			) $r[] = $token;
		}
		
		return $r;
	}
	
	public function prepareObfuscatedToken_characterSourcePool () {
	
		global $randomStringJSO_combinationsHadAtCurrentLength;
		global $randomStringJSO_currentLength;
		global $randomStringJSO_sourcepool;
		global $ES5identifier__charPool__startChar;
		global $ES5identifier__charPool;
		global $asciiIdentifier__charPool;
		global $useUnicodeIdentifiers;
	
		if ($useUnicodeIdentifiers) {
			reportStatus (1, '<p class="webappObfuscator__configInfo">Will produce unicode output (obfuscated identifiers/tokens)</p>');

			// many thanks to https://mathiasbynens.be/notes/javascript-identifiers and the pages listed below here..
			$ES5identifier__charPool__startChar__files = array (
				'Uppercase_letter' => 'https://codepoints.net/search?gc=Lu',
				'Lowercase_letter' => 'https://codepoints.net/search?gc=Ll',
				'Titlecase_letter' => 'https://codepoints.net/search?gc=Lt',
				'Modified_letter' => 'https://codepoints.net/search?gc=Lm',
				'Other_letter' => 'https://codepoints.net/search?gc=Lo',
				'Letter_number' => 'https://codepoints.net/search?gc=Nl'
			);
			$ES5identifier__charPool__files = array (
				'Non-spacing_mark' => 'https://codepoints.net/search?gc=Mn',
				'Spacing-combining_mark' => 'https://codepoints.net/search?gc=Mc',
				'Decimal_digit_number' => 'https://codepoints.net/search?gc=Nd',
				'Connector_puctuation' => 'https://codepoints.net/search?gc=Pc'
			);

			$cacheFile___ES5identifier__charPool__startChar = 
				dirname(__FILE__).'/1.0.0/cache_unicodeChars/ES5identifier__charPool__startChar.unicode.txt';
			$cacheFile___ES5identifier__charPool = 
				dirname(__FILE__).'/1.0.0/cache_unicodeChars/ES5identifier__charPool.unicode.txt';

			if (file_exists($cacheFile___ES5identifier__charPool__startChar)) {
				reportStatus (300, '<p class="webappObfuscator__usingCache webappObfuscator__fetchResources__unicodeTables">Using cache file "'.$cacheFile___ES5identifier__charPool__startChar.'"</p>');
				$ES5identifier__charPool__startChar = 
					file_get_contents($cacheFile___ES5identifier__charPool__startChar);
			} else {
				$ES5identifier__charPool__startChar = 
					'_' // according to the ES5 specification, $ is also allowed, but this is used frequently by jQuery so we'll skip it.	
					.resolveCodePointsDotNet($ES5identifier__charPool__startChar__files);

				file_put_contents($cacheFile___ES5identifier__charPool__startChar, $ES5identifier__charPool__startChar);
			};


			if (file_exists($cacheFile___ES5identifier__charPool)) {
				reportStatus (300, '<p class="webappObfuscator__usingCache webappObfuscator__fetchResources__unicodeTables">Using cache file "'.$cacheFile___ES5identifier__charPool.'"</p>');
				$ES5identifier__charPool =
					file_get_contents($cacheFile___ES5identifier__charPool);
			} else {
				reportStatus (300, '<p class=".webappObfuscator__fetchResources webappObfuscator__fetchResources__unicodeTables">MISSING cache file "'.$cacheFile___ES5identifier__charPool.'"</p>');
				$ES5identifier__charPool =
					$ES5identifier__charPool__startChar
					.convertJSunicodeToPHP ('\u200C\u200D')
					.resolveCodePointsDotNet($ES5identifier__charPool__files);

				reportStatus (300, '<p class="webappObfuscator__writeCache webappObfuscator__fetchResources__unicodeTables">Writing '.filesizeHumanReadable(strlen($$ES5identifier__charPool)).' to cache file "'.$cacheFile___ES5identifier__charPool.'"</p>');
				file_put_contents ($cacheFile___ES5identifier__charPool, $ES5identifier__charPool);
			};

			global $ES5identifier__charPool__startChar;
			global $ES5identifier__charPool;

		} else {

			reportStatus (1, '<p class="webappObfuscator__configInfo">Will produce ASCII output (obfuscated identifiers/tokens)</p>');
			
			$asciiIdentifier__charPool = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvw_'; 
			global $asciiIdentifier__charPool;
		}
	}	
	
	public function mangleString ($str) {
		$l = strlen($str);
		$r = '';
		for ($i=0; $i<$l; $i++) {
			$r .= substr($str, $i, 1).'_';
		};
		return $r;
	}
	
	public function unmangleString ($str) {
		$l = strlen($str);
		$r = '';
		for ($i=0; $i<$l; $i=$i+2) {
			$r .= substr($str, $i, 1);
		}
		return $r;
	}
	
	public function mangleStrings ($inputStrings, $mangle=true) {
		if (true) {
			// hacker the hack hack : mangle anything u might wanna obfuscate but shouldn't be.
			if (is_array($inputStrings)) {
				$codeToObfuscate = array();
				foreach ($inputStrings as $idx => $str) {
					if (
						($mangle && (
							stripos($str, 'http://')===false
							&& stripos ($str, 'https://')===false
							
							// someone may have custom html attributes with relative urls in 'm, exclude those... :
							&& 1 === preg_match ('#js|json|php|html$#', $str) 
						))
					) {
						$codeToObfuscate[$idx] = $str;
					} else {
						if ($mangle) {
							$codeToObfuscate[$idx] = $this->mangleString($str);
						} else {
							$codeToObfuscate[$idx] = $this->unmangleString($str);
						}
					}
				}
			} elseif (is_string($inputStrings)) {
				if (
					($mangle && (
						stripos($inputStrings, 'http://')===false
						&& stripos ($inputStrings, 'https://')===false
						
						// someone may have custom html attributes with relative urls in 'm, exclude those... :
						&& 0 === preg_match ('#js|json|php|html$#', $inputStrings) 
					))
				) {
					$codeToObfuscate = $inputStrings;
				} else {
					if ($mangle) {
						$codeToObfuscate = $this->mangleString($inputStrings);
					} else {
						$codeToObfuscate = $this->unmangleString($inputStrings);
					}
				}
			}
			$r = $codeToObfuscate;
		} else {
			$r = $inputStrings;
		}	
		return $r;
	}		
	
	public function phpJSO_replace_tokens (&$tokens, &$code, $specialDebug=false) {
		$tokenBoundary = $this->factorySettings['tokens']['tokenBoundary'];
		$regxBoundary = $this->factorySettings['tokens']['regxBoundary'];
		$searchTokenSpecialChars = $this->factorySettings['tokens']['searchTokenSpecialChars'];
		$replaceTokenSpecialChars = $this->factorySettings['tokens']['replaceTokenSpecialChars'];
		//echo '<pre>'; var_dump($this->factorySettings); echo '</pre>'; die();
		
		$regxs = array ();
		$replaces = array();
		
		/* WORKS
		$test = 'meheheh';
		$m = $this->mangleString($test);
		var_dump ($m);
		$n = $this->unmangleString($m);
		var_dump ($n);
		die();
		*/
		
		$mangleInput = false; // ehhh... one does not mangle the input.
		if ($mangleInput) {
			
			
			// hacker the hack hack : mangle anything u might wanna obfuscate but shouldn't be.
			if (is_array($code)) {
				$codeToObfuscate = array();
				foreach ($code as $idx => $str) {
					if (
						stripos($str, 'http://')===false
						&& stripos ($str, 'https://')===false
						
						// someone may have custom html attributes with relative urls in 'm, exclude those... :
						&& 0 === preg_match ('#js|json|php|html$#', $str) 
					) {
						$codeToObfuscate[] = $str;
					} else {
						$codeToObfuscate[] = $this->mangleString($str);
					}
				}
			} elseif (is_string($code)) {
				if (
					stripos($code, 'http://')===false
					&& stripos ($code, 'https://')===false
					
					// someone may have custom html attributes with relative urls in 'm, exclude those... :
					&& 0 === preg_match ('#js|json|php|html$#', $code) 
				) {
					$codeToObfuscate = $code;
				} else {
					$codeToObfuscate = $this->mangleString($code);
				}
			}
		} else {
			$codeToObfuscate = $code;
		}
		//echo 't11.2 - <pre style="color:yellow;background:red">'; echo htmlentities($codeToObfuscate); echo '</pre>';
		
		
		//echo '<pre style="color:white;background:red">$regxBoundary='; var_dump ($regxBoundary); echo '</pre>'; die();
		
		// prepare a master token-list for obfuscation :
		
                //$regxs = array_keys($tokens);
                //$replaces = array_values($tokens);
		foreach ($tokens as $token=>$tokenObfuscated) {
                    if (false && $specialDebug && $token==='siteBackground') {
                    echo '7775.1 $token=<pre>'; var_dump ($token); echo '</pre>';
                    echo '7775.2 $codeToObfuscate=<pre>'; var_dump($codeToObfuscate); echo '</pre>';
                    die();
                    }
                    if (is_array($codeToObfuscate)) {
                        $arrayContainsToken = false;
                        foreach ($codeToObfuscate as $idx=>$str) {
                            if (strpos($str, $token)!==false) {
                                $arrayContainsToken = true;
                                break;
                            }
                        }
                    }
                    if ( 
                        (is_string($codeToObfuscate) && strpos($codeToObfuscate, $token)!==false) 
                        || (is_array($codeToObfuscate) && $arrayContainsToken)
                    ) {
			$tokenRegxed = str_replace ($searchTokenSpecialChars, $replaceTokenSpecialChars, $token);
			
			/*
			$regx = 
				$regxBoundary
				.$tokenBoundary.$tokenRegxed.$tokenBoundary
				.'|'.$tokenBoundary.'\#'.$tokenRegxed.$tokenBoundary
				.'|'.$tokenBoundary.'\.'.$tokenRegxed.$tokenBoundary
				.$regxBoundary;
			*/
			$regx = $regxBoundary.$tokenBoundary.$tokenRegxed.$tokenBoundary.$regxBoundary;
			$regxs[] = $regx;
			$replaces[] = $tokenObfuscated;
			/*
			if (trim($token)==='contexts') {
			  echo 't5.1<pre>';
			  var_dump ($token);
			  var_dump ($regx);
			  var_dump ($tokenObfuscated);
			  echo '</pre>5.1-end';
			  //die();
			};*/
			/*
			$r = pregReplace ($regx, $tokenObfuscated, $code);
			if (is_string($r)) $code = $r;
			*/
			
			$regx = $regxBoundary.'(\s*)'.$tokenBoundary.'\#'.$tokenRegxed.$tokenBoundary.'(\s*)'.$regxBoundary; // html id=""
			$regxs[] = $regx;
			$replaces[] = '#'.$tokenObfuscated;
			/*
			$r = pregReplace ($regx, $replace, $code);
			if (is_string($r)) $code = $r;
			*/
			
			$regx = $regxBoundary.'(\s*)'.$tokenBoundary.'\.'.$tokenRegxed.$tokenBoundary.'(\s*)'.$regxBoundary; // html class=""
			$regxs[] = $regx;
			$replaces[] = '.'.$tokenObfuscated;
                    }
			
		}
		// and... obfuscate. 
		//	(this is the only reasonably-efficient way to do this btw,
		// 	this takes about 3 to 5 minutes for 1 megabyte of javascript,
		//	trying to do the preg_replace()s via pregReplace()s inside the 
		//	loop above here will get you a run-time for the same 1 megabyte
		//	of inputdata of over 20 minutes (or more)..)
		//	
		
		/*
		/*if (is_string($codeToObfuscate)) echo '741000'.htmlentities($codeToObfuscate).'<br/>';
		$t = '\' / sa / json / decode / contexts \'';
		if (is_string($codeToObfuscate) && substr($codeToObfuscate, 0, strlen($t))!==false) {
		//if ($codeToObfuscatesubstr($t, strlen($t))!==false) {
		  echo '741001 echo $codeToObfuscate=<pre>'; var_dump ($codeToObfuscate); echo '</pre>';
		  for ($i=0; $i<count($regxs); $i++) {
		    echo '741002 echo $regxs['.$i.']=';var_dump (htmlentities($regxs[$i])).' ==> ';
		    echo '$replaces['.$i.']=';var_dump (htmlentities($replaces[$i]));echo'<br/>';
		  }
		}  
		*/
		
		$detailedDebug = false;
		if ($detailedDebug) {
		  echo '<pre style="color:white;background:blue">phpJSO_replace_tokens $regxs='; var_dump($regxs); echo '</pre>';
		  echo '<pre style="color:grey;background:blue">phpJSO_replace_tokens $replaces='; var_dump($replaces); echo '</pre>';
		  echo '<pre style="color:yellow;background:blue">phpJSO_replace_tokens $codeToObfuscate='; var_dump($codeToObfuscate); echo '</pre>';
		}
		
		
		//$r = str_replace ($regxs, $replaces, $codeToObfuscate);
		$r = pregReplace ($regxs, $replaces, $codeToObfuscate);
		if ($detailedDebug) {
		  echo '<pre style="color:lime;background:blue">phpJSO_replace_tokens $r='; var_dump($r); echo '</pre>';
		}
		
		
		if ($mangleInput) {
			// and unmangle anything that got mangled :
			if (is_array($r)) {
				$codeObfuscated = array();
				foreach ($r as $idx => $str) {
					if (
						substr ($str, 1, 1) === '_'
						&& substr ($str, 3, 1) === '_'
						&& substr ($str, 5, 1) === '_'
						&& substr ($str, 7, 1) === '_'
					) {
						$codeObfuscated[] = $str;
					} else {
						$codeObfuscated[] = $this->unmangleString($str);
					}
				}
			} elseif (is_string($r)) {
				if (
					substr ($r, 1, 1) === '_'
					&& substr ($r, 3, 1) === '_'
					&& substr ($r, 5, 1) === '_'
					&& substr ($r, 7, 1) === '_'
				) {
					$codeObfuscated = $r;
				} else {
					$codeObfuscated = $this->unmangleString($r);
				}
			}
		} else {
			$codeObfuscated = $r;
		}
		
		if (
			is_string($codeObfuscated) 
			|| is_array($codeObfuscated)
		) return $codeObfuscated; else return false;
	}
	
	public function obfuscateTokens (&$tokens, &$code) {
		global $randomStringJSO_combinationsHadAtCurrentLength;
		global $randomStringJSO_currentLength;
		global $randomStringJSO_sourcepool;
		global $ES5identifier__charPool__startChar;
		global $ES5identifier__charPool;
		global $asciiIdentifier__charPool;
		global $useUnicodeIdentifiers;
		global $wo__tokens__ignoreList__allLowercase;
		
		$tokensObfuscated = array();
		
		$randomStringJSO_combinationsHad = array();	
		
		foreach ($tokens as $idx => $token) {
			$tries = 0;
			$r = array();
			
			// chorus :
			$randomToken = randomStringJSO ($tries);
			$t0 = array_search(strtolower($randomToken), $wo__tokens__ignoreList__allLowercase)!==false;
			$t1 = array_search($randomToken, $randomStringJSO_combinationsHad)!==false;
			$t2 = array_search($randomToken, $tokens)!==false;
			$t3 = strpos ($code, $randomToken)!==false;
			if ($useUnicodeIdentifiers) {
				$poolsize = strlen($ES5identifier__charPool__startChar);
				if ($randomStringJSO_currentLength > 1) {
					$poolsize += pow (strlen($ES5identifier__charPool), $randomStringJSO_currentLength-1);
				}
			} else {
				$poolsize = pow (strlen($asciiIdentifier__charPool), $randomStringJSO_currentLength);
			}
			
			while (
				$t0
				||$t1 
				|| $t2
				|| $t3
			) {
				// stanza aka verse :
				// ... yea... well... it aint an actual song eh..
				if ($t2 || $t3) {
					$randomStringJSO_combinationsHadAtCurrentLength++;
					$randomStringJSO_combinationsHad[] = $randomToken;
				}
				if (
					$randomStringJSO_combinationsHadAtCurrentLength
					//>= ( pow(strlen($randomStringJSO_sourcepool), $randomStringJSO_currentLength) - 10 )
					>= ( pow($poolsize, $randomStringJSO_currentLength) - 10 )
				) {
					$randomStringJSO_currentLength++;
					$randomStringJSO_combinationsHadAtCurrentLength = 0;
					$randomStringJSO_combinationsHad = array();
				};
				
				// chorus:
				$randomToken = randomStringJSO ($tries);
				$t0 = array_search(strtolower($randomToken), $wo__tokens__ignoreList__allLowercase)!==false;
				$t1 = array_search($randomToken, $randomStringJSO_combinationsHad)!==false;
				$t2 = array_search($randomToken, $tokens)!==false;
				$t3 = strpos ($code, $randomToken)!==false;
				if ($useUnicodeIdentifiers) {
					$poolsize = strlen($ES5identifier__charPool__startChar);
					if ($randomStringJSO_currentLength > 1) {
						$poolsize += pow (strlen($ES5identifier__charPool), $randomStringJSO_currentLength-1);
					}
				} else {
					$poolsize = pow (strlen($asciiIdentifier__charPool), $randomStringJSO_currentLength);
				}
				
			};
			//if (($idx % 47)==0) reportStatus ('<p class="webappObfuscator__detail" style="font-size:70%">Obfuscated '.$idx.' of '.count($tokens).' tokens</p>');
			if ($tries > 50) reportStatus ('<p class=".webappObfuscator__inefficient">webappObfuscator::obfuscateTokens() : token '.$idx.' ("'.$token.'" => "'.$randomToken.'") <span class="webappObfuscator__being-utterly-lame">(took '.$tries.' tries)</span></p>');//."\r\n";
			$randomStringJSO_combinationsHadAtCurrentLength++;
			$randomStringJSO_combinationsHad[] = $randomToken;
			$tokensObfuscated[$token] = $randomToken;
		}
		return $tokensObfuscated;
	}
}
?>
