<?php
	require_once(realpath(dirname(__FILE__)).'/../../../boot.php');
    //require_once(realpath(dirname(__FILE__)).'/functions.php');
	
	class app2Dextension_photoAlbum_disk {
		private $cn = 'class appExtension_photoAlbum_disk_v1_0_0';
		private $filePath = '';
		private $files = [];
		private $divIDidx = -1;
		
		public function constructor ($filePath='') {
			$fncn = 'public function constructor ($filePath='')';
			if (is_string($filePath) && $filePath!=='') {
				trigger_error ($this->cn.'::'.$fncn.' : $filePath has to be a valid path to a folder containing image files (*.gif, *.jpg, *.jpeg, *.png);', E_USER_ERROR);
			} else {
				$this->filePath = $filePath;
				$this->files[] = getFilePathList ($filePath, true, '/.*/', null, array('file'), null, 1, true);
				$this->divIDidx++;
			}
		}
		
		public function output_HTML5 ($indentations = 0, $divID = 'appExtension_photoAlbum_disk_v1_0_0__id_'.$this->divIDidx) {
			$indentationArray = [];
			for ($i=0; $i<$indentations; $i++) { $indentationArray[] = ''; }
			$indentationString = implode ('\t',$indentationArray);
			$is = $indentationString;
			$indentationArray[] = '';
			$is2 = implode ('\t',$indentationArray);
			
			$ret = $is.'<div id="'.$divID.'_mainWindow" class="vividDialog">'.PHP_EOL;
			$ret = $is2.'<div id="'.$divID.'_contentWindow" class="vividDialogContent vividScrollpane appExtension_photoAlbum_disk_v1_0_0__mainWindow" style="overflow:hidden;float:left;width:220px;height:240px;margin:5px;padding:10px;padding-top:20px;border-radius:10px;border:1px solid black;background:rgba(0,0,0,0.7);box-shadow:2px 2px 2px rgba(0,0,0,0.5), inset 1px 1px 1px rgba(0,0,255,0.5), inset -1px -1px 1px rgba(0,0,255,0.5);">'.PHP_EOL;


			require_once (realpath(dirname(__FILE__).'/../../../..').'/boot_stage_001.php');
			global $saSiteHTTP; global $saSiteDomain; global $saSiteRootFolder; global $saFrameworkFolder;
			global $saSiteHD; global $saFrameworkHD; 
			global $saSiteURL; global $saFrameworkURL;global $saCMS;
			
			$baseURL = $saSiteURL.'nicerapp/siteData/'.$_SERVER['HTTP_HOST'];
			$baseDir = $saSiteHD.'nicerapp/siteData/'.$_SERVER['HTTP_HOST'];
			$targetDir = $baseDir.'/'.$_GET['codePath'];
			$thumbDir = $targetDir.'/thumbs';
			
			$smID = $_GET['smID'];
			$iid = $_GET['iid'];
			$dialogID = $_GET['dialogID'];
			$imgStyle = ''; // boxShadow perhaps

			define ("FILE_FORMATS_photos", "/(.*\.png)|(.*\.gif)|(.*\.jpg)/");
			$files = getFilePathList ($targetDir, false, FILE_FORMATS_photos, null, array('file'));
			
			?>
			<style>
				.filename {
					color : white;
				}
			</style>
			<?php
			
			$dbg = array (
				'baseURL' => $baseURL,
				'baseDir' => $baseDir,
				'targetDir' => $targetDir,
				'files' => $files
			);
			//echo '<pre style="color:black;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>';
			foreach ($files as $idx => $filePath) {
				$fileName = str_replace ($targetDir.'/', '', $filePath);
				$thumbPath = $thumbDir.'/'.$fileName;
				$thumbURL = str_replace ($baseDir, $baseURL, $thumbPath);
				$fileURL = str_replace ($baseDir, $baseURL, $filePath);
				$dbg = array (
					'fileName' => $fileName,
					'filePath' => $filePath,
					'baseDir' => $baseDir,
					'thumbDir' => $thumbDir,
					'thumbPath' => $thumbPath,
					'thumbURL' => $thumbURL
				);
				////echo $is2.'\t<pre style="color:black;background:white;border-radius:3px;border:1px solid black;">'; var_dump ($dbg); echo '</pre>';
				//echo $is2.'\t<div style="overflow:hidden;float:left;width:220px;height:240px;margin:5px;padding:10px;padding-top:20px;border-radius:10px;border:1px solid black;background:rgba(0,0,0,0.7);box-shadow:2px 2px 2px rgba(0,0,0,0.5), inset 1px 1px 1px rgba(0,0,255,0.5), inset -1px -1px 1px rgba(0,0,255,0.5);">';
				
				if ($iid=='pageSettings__pageSettings_siteBackgroundTree_tree') {
					if ($dialogID!=='siteBackground') {
						$onclick = 'onclick="window.top.na.sitePopups.pageSettings.setBackground(\''.$dialogID.'\', \''.$fileURL.'\');"';
					} else {
						$onclick = 'onclick="window.top.na.s.c.setBackground(\''.$fileURL.'\');"';
					}
				} else {
					$onclick = '';
				}
				
				echo $is2.'\t<center><img src="'.$thumbURL.'" style="width:200px" '.$onclick.'/><br/><span class="filename">'.$fileName.'</span></center>'.PHP_EOL;   
				//echo $is2.'\t</div>';
			}

			$ret.= $is2.'</div>'.PHP_EOL;
			$ret.= $is.'</div>'.PHP_EOL;
			return $ret;
		}
		
	}
	
?>
