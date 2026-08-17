<?php
header('Content-type: text/javascript');
$rt = realpath(dirname(__FILE__).'/..');

if (!array_key_exists('f', $_GET)) die (500);
//if (strpos($_GET['f'], '..')!==false) die (403);
//if (!file_exists($rt.$_GET['f']) || !is_readable($rt.$_GET['f'])) die (403);
//echo $rt.$_GET['f']; exit();

if (preg_match('/\.\.\//', $_GET['f'])) $_GET['f'] = '/'.$_GET['f'];

$js = file_get_contents($rt.$_GET['f']);
$js = filestampJSmodule($js);
$js = timestampJSmodule1a($js);
$js = timestampJSmodule1($js);
$js = timestampJSmodule2($js);
$js = timestampJSmodule3($js);
echo $js;

functdion filestampJSmodule ($js) {
  $rt = realpath(dirname(__FILE__).'/..');
  $preg = preg_match_all ('/class\s+(.*?)\s+\{/', $js, $matches);

  return str_replace(
    $matches[0][0],
    $matches[0][0].PHP_EOL."\t".'moduleURL = "'.$_GET['f'].'";',
    $js
  ).PHP_EOL;
}

function timestampJSmodule1 ($js) {
  /*
  $rt = realpath(dirname(__FILE__).'/..');
  $preg = preg_match_all ('/from\s+[\'"](\/NicerAppWebOS\/.*?)[\'"]/', $js, $matches);
  $matches[2] = [];
  $matches[3] = [];

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if (strpos($rp,'&')===false) {
        $m = filemtime($rt.$rp);
        $matches[2][] = $rt.$rp;
        $matches[3][] = $m;
    } else {
        $matches[2][] = $rt.$rp;
        $matches[3][] = false;
    }
  }

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if ($matches[3][$idx]!==false) {
      $m = $matches[3][$idx];
      $js = str_replace(
        $relPath,
        '/NicerAppWebOS/ajax_getModule.php?f='.$rp.'&c='.date('Ymd-His',$m),
        $js
      );
    }
  }
  //echo '<pre style="background:red;color:yellow;">'; var_dump($matches); echo '</pre>';
*/
  return $js;
}
function timestampJSmodule1a ($js) {
  /*
  $rt = realpath(dirname(__FILE__).'/..');
  $preg = preg_match_all ('/from\s+[\'"](https:\/\/new\.nicer\.app\/NicerAppWebOS\/.*?)[\'"]/', $js, $matches);
  $matches[2] = [];
  $matches[3] = [];

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('https://new.nicer.app/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if (strpos($rp,'&')===false) {
        $m = filemtime($rt.$rp);
        $matches[2][] = $rt.$rp;
        $matches[3][] = $m;
    } else {
        $matches[2][] = $rt.$rp;
        $matches[3][] = false;
    }
  }

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if ($matches[3][$idx]!==false) {
      $m = $matches[3][$idx];
      $js = str_replace(
        $relPath,
        'https://new.nicer.app/NicerAppWebOS/ajax_getModule.php?f='.$rp.'&c='.date('Ymd-His',$m),
        $js
      );
    }
  }
  //echo '<pre style="background:red;color:yellow;">'; var_dump($matches); echo '</pre>';
  */
  return $js;
}
function timestampJSmodule2 ($js) {
  /*
  $rt = realpath(dirname(__FILE__).'/..');
  $rt2 = $rt.(
    preg_match('/^\//', $_GET['f'])
    ?''
    :'/'
  ).$_GET['f'];
  $fn = basename($rt2);
  $rt2a = str_replace($rt,'',dirname($rt2));
  $rt3 = $rt.$rt2a;
  $rt = $rt3;
  //echo '/* '.$rt.' * /'.PHP_EOL;
  //echo '/* '.$fn.' * /'.PHP_EOL;
  $preg = preg_match_all ('/from\s+[\'"](\.\.\/.*?)[\'"]/', $js, $matches);
  $matches[2] = [];
  $matches[3] = [];

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if (strpos($rp,'&')===false) {
        $m = filemtime($rt.'/'.$rp);
        $matches[2][] = $rt.'/'.$rp;
        $matches[3][] = $m;
    } else {
        $matches[2][] = $rt.'/'.$rp;
        $matches[3][] = false;
    }
  }

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if ($matches[3][$idx]!==false) {
      $m = $matches[3][$idx];
      $js = str_replace($relPath, '/NicerAppWebOS/ajax_getModule.php?f='.$rt2a.'/'.$rp.'&c='.date('Ymd-His',$m), $js);
    }
  }
  //echo '/* <pre style="background:red;color:yellow;">'.PHP_EOL; var_dump($matches); echo '</pre> * /'.PHP_EOL;
*/
  return $js;
}
function timestampJSmodule3 ($js) {
  /*$rt = realpath(dirname(__FILE__).'/..');
  $preg = preg_match_all ('/loadModule\s+\([\'"](.*?)[\'"],\)/', $js, $matches);
  $matches[2] = [];
  $matches[3] = [];

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if (strpos($rp,'&')===false) {
        $m = filemtime($rt.$rp);
        $matches[2][] = $rt.$rp;
        $matches[3][] = $m;
    } else {
        $matches[2][] = $rt.$rp;
        $matches[3][] = false;
    }
  }

  foreach ($matches[1] as $idx => $relPath) {
    $rp = str_replace('/NicerAppWebOS/ajax_getModule.php?f=','',$relPath);
    if ($matches[3][$idx]!==false) {
      $m = $matches[3][$idx];
      $js = str_replace($relPath, '/NicerAppWebOS/ajax_getModule.php?f='.$rp.'&c='.date('Ymd-His',$m), $js);
    }
  }
  //echo '/* <pre style="background:red;color:yellow;">'.PHP_EOL; var_dump($matches); echo '</pre> * /';
*/
  return $js;
}
?>
