<?php
require_once ('../../../../boot.php');

main();

function main () { 
	$dir = dirname(__FILE__).'/../themes/buttonsAndMenus';
	$numberPerPage = 6;
	$numberPerLine = 3;

	$path = realpath($dir);
	
	$files = getFilePathList ($path, true, "/.*\.json$/", array('file'));
	sort ($files);
	$begin = ((((int)$_GET['page']-1)*$numberPerPage));
	$end = ((((int)$_GET['page'])*$numberPerPage));
	echo '<table style="" cellpadding="5" border="1">';
	echo '<tr>';
	$onLine = 0;
	for ($i = $begin; $i < $end && $i<count($files); $i++) {
		
		$f = pathinfo($files[$i]);
		$f = $f['filename'];
		$f2 = pathinfo($f);
		$f2 = $f2['filename'];
		$themeName = $f2;
		
		$urlJSON = str_replace('\\','/',str_replace(str_replace('/', '\\', SA_HD), SA_WEB, $files[$i]));
		$urlImage = str_replace('.json', '', $urlJSON);
		
		$onLine++;
		if ($onLine>$numberPerLine) {
			echo '</tr><tr>';
			$onLine = 1;
		}
		getButton ('btn_'.$themeName, $themeName, $urlImage, $urlJSON, str_replace('.json','', $files[$i]));
	}
	echo '</tr>';
	echo '<tr>';
	echo '<td colspan="999" style="text-align:center">';
	$j = 0;
	for ($i=1; $i<ceil(count($files)/$numberPerPage)+1; $i++) {
		if ($j!=0) echo ' | ';
		$j++;
		if ($i!==(int)$_GET['page']) {
			echo '<a href="javascript:getPage('.$i.');">'.$i.'</a>';
		} else {
			echo $i;
		}
	}
	echo '</td>';
	echo '</tr>';
	echo '</table>';
}


function getButton ($btnID, $label, $urlImage, $urlJSON, $filepathImage) {
?>
			<td class="button" style="height:90px;vertical-align:top;">
				<div id='<?php echo $btnID?>' class='vividButton vividTheme__<?php echo $label?>'><a href="javascript:alert('Button <?php echo $btnID?> was clicked!');"><?php echo 'test';//$label?></a></div>
				<table cellspacing="5" style="width:100%">
					<tr>
						<td>
							<input id='enable_<?php echo $btnID?>' type="checkbox"  onclick="if (this.checked) sa.vcc.changeState(null, document.getElementById('<?php echo $btnID?>__item__0'),'disabled'); else sa.vcc.changeState(null, document.getElementById('<?php echo $btnID?>__item__0'),'normal');  document.getElementById('select_<?php echo $btnID?>').checked=false;"><span class="inputStateSelector">Disabled</span></input><br/>
							<input id='select_<?php echo $btnID?>' type="checkbox"  onclick="if (this.checked) sa.vcc.changeState(null, document.getElementById('<?php echo $btnID?>__item__0'),'selected'); else sa.vcc.changeState(null, document.getElementById('<?php echo $btnID?>__item__0'),'normal');  document.getElementById('enable_<?php echo $btnID?>').checked=false;"><span class="inputStateSelector">Selected</span></input><br/>
						</td>
						<td class="download" style="font-size:9px; text-align:right;">
							<a href="<?php echo $urlImage?>"><?php echo basename($urlImage)?></a> <?php echo filesizeHumanReadable(filesize($filepathImage))?><br/>
							<a href="<?php echo $urlJSON?>"><?php echo basename($urlJSON)?></a><br/>
						</td>
					</tr>
				</table>
			</td>
<?php
}


?>