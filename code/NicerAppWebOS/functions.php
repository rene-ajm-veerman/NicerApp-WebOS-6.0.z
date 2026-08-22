<?php
require_once(dirname(__FILE__).'/boot.php');
//require_once(dirname(__FILE__).'/3rd-party/vendor/autoload.php');
//use Birke\Rememberme\Authenticator;
//use Birke\Rememberme\Storage\FileStorage;
//use Defuse\Crypto\Key;
//use Defuse\Crypto\Crypto;


/**
 * Generate a generic "View History" button.
 *
 * @param string $documentID
 * @param array  $options   same options that na.history.view() accepts
 * @return string HTML
 */
function html_viewHistoryButton(string $documentID, array $options = []): string
{
    global $naWebOS;

    $optsJson = htmlspecialchars(json_encode($options), ENT_QUOTES, 'UTF-8');

    return $naWebOS->html_vividButton(
        1001, 'float:right',
        'btnViewHistory_' . substr(md5($documentID), 0, 8),
        'vividButton_icon_50x50 grouped btnViewHistory',
        '_50x50', 'grouped',
        '',
        "na.history.view('" . addslashes($documentID) . "', " . $optsJson . ");",
        '',
        '',
        1001, 'View revision history',
        'btnCssVividButton_outerBorder.png',
        'btnCssVividButton.png',
        $options['buttonColor'] ?? 'btnCssVividButton.redStone.png',
        $options['buttonIcon']  ?? 'btnHistory.png',
        '', '', '', ''
    );
}


function safeHTTPinput ($in, $default=null) {
    if (array_key_exists($in, $_POST)) return $_POST[$in];
    elseif (array_key_exists($in, $_GET)) return $_GET[$in];
    else return $default;
}

function addUserPasswordToHiddenFile ($u, $p) {
    global $naWebOS;
    $fn = $naWebOS->domainPath.'/siteCache/usersPasswords.csv';
    $fp = fopen ($fn, 'a');
    fwrite ($fp, $u.','.$p.PHP_EOL);
    fclose ($fp);
}

function getPassword ($u) {
    global $naWebOS;
    $fn = $naWebOS->domainPath.'/siteCache/usersPasswords.csv';
    $fc = file_get_contents($fn);
    $uz = explode ($fc, PHP_EOL);
    foreach ($uz as $idx => $u1) {
        $u2 = explode ($u1,',');
        if ($u2[0]==$u) return $u2[1];
    }
    return false;
}

function naDateTimeStr ($s, $tz) {
    $sa = $s;
    if (date('Y',$s)==1970) $sa = $s * 1000;
    if (date('Y',$s)>5000) $sa = round($s / 1000); // LOL
    $tza = $tz*60*1000;
    if ($tza<=-12*60*1000-1000) $tza = $tza / 1000;
    if ($tza>=12*60*1000+1000) $tza = $tza / 1000;
    return date ('Y-m-d(l) H:i:s', $sa+$tza).' GMT'.($tz===0?'':($tz>0?'+':'').$tz.'m');
}

function naDateTimeHeader ($s, $tz) {
    $now = time();
    //return $now.' - '.$s;
    $sa = $s;
    $tz *= -1;
    if (date('Y',$s)==1970) $sa = $s * 1000;
    if (date('Y',$s)>=5000) $sa = round($s / 1000); // LOL Y5K BUG
    $tza = $tz*60*1000;
    if ($tza<=-12*60*1000-1000) $tza = $tza / 1000;
    if ($tza>=12*60*1000+1000) $tza = $tza / 1000;
    $ago = (int)$now - (int)$sa;
    //return $ago;
    $agoHTML = secondsToTimeString($ago).' ago.';
    //return $agoHTML;
    $originalDatetime = date ('Y-m(F)-d(l) H:i:s', $sa+$tza).' GMT'.($tz===0?'':($tz>0?'+':'-').$tz.'m');
    $html =
        '<span class="naDateTimeHeader" title="'.$originalDatetime.'">'.$agoHTML.'</span>';
    return $html;
}



function secondsToTimeString ($seconds) {
    $days = floor($seconds / 86400);
    $remainingSeconds = $seconds % 86400;
    $time = gmdate("H_i-s", $remainingSeconds);
    $time = str_replace('_','h ',$time);
    $time = str_replace('-','m ',$time);
    $time.= 's';

    if ($days > 0) {
        return $days.'d, '.$time;
    }

    return $time;
}

function timestampJSmodule ($js) {
    $js = timestampJSmodule1 ($js);
    $js = timestampJSmodule2 ($js);
    return $js;
}

function timestampJSmodule1 ($js) {
  $rt = realpath(dirname(__FILE__).'/..');
  $preg = preg_match_all ('/from\s+[\'"](.*?)[\'"]/', $js, $matches);
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
    if ($matches[3][$idx]!==false) {
      $m = $matches[3][$idx];
      $js = str_replace($relPath, $relPath.'&c='.date('Ymd-His',$m), $js);
    }
  }
  //echo '<pre style="background:red;color:yellow;">'; var_dump($matches); echo '</pre>';

  return $js;
}

function timestampJSmodule2 ($js) {
  $rt = realpath(dirname(__FILE__).'/..');
  $preg = preg_match_all ('/\(\s*[\'"](.*?)[\'"]\s*\)/', $js, $matches);
  $matches[2] = [];
  $matches[3] = [];
debug_print_backtrace();
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
    if ($matches[3][$idx]!==false) {
      $m = $matches[3][$idx];
      $js = str_replace($relPath, $relPath.'&c='.date('Ymd-His',$m), $js);
    }
  }
  //echo '<pre style="background:red;color:white;">'; var_dump($matches); echo '</pre>';

  return $js;
}

global $toArray;
$toArray = function($x) use(&$toArray) {
    return is_scalar($x)
        ? $x
        : array_map($toArray, (array) $x);
};

function filePathToURL ($filepath) {
    global $naWebOS;
    $fp = str_replace(realpath(dirname(__FILE__).'/..'),'',$filepath);
    $fp = 'https://'.$naWebOS->domainFolder.$fp;
    return $fp;
}

function safeLoadJSONfile($filePath, $mustExist=true, $flush=true) {
    $debug = true;
    //echo 't93;'; var_dump ($filePath);
    if (!file_exists($filePath)) {
    //echo $filePath; echo '<br/>'; die();
        if (!$mustExist) return []; else { echo backtrace(); trigger_error ('File "'.$filePath.'" does not exist.', E_USER_ERROR); }
    };
    if (!is_readable($filePath)) {
        if (!$mustExist) return []; else trigger_error ('File "'.$filePath.'" does is not readable.', E_USER_ERROR);
    };
        //var_dump (preg_match('/\.php$/', $filePath));

    $textData =
        preg_match('/\.php$/', $filePath)
        ? require_return ($filePath, $flush)
        : file_get_contents($filePath);
    $jsonData = json_decode ($textData, true);
    /*
    try {
        if ($debug && false) {
            echo '<pre style="color:yellow;background:rgba(0,0,50,0.5);border-radius:10px;padding:8px;">';
            echo $textData;
        }
        if ($debug)
            file_put_contents ($filePath.'_output.txt', $textData);
        elseif (file_exists($filePath.'_output.txt'))
            unlink ($filePath.'_output.txt');

        $jsonData = json_decode ($textData, true);
        if ($debug && false) {
            echo json_last_error_msg();
            echo '</pre>';
        }
    } catch (Exception $e) { }
    */

    //exit();
    //echo '<pre style="color:blue">'; var_dump ($textData); echo '</pre>';
    //if (json_last_error()!==JSON_ERROR_NONE) trigger_error ('Error during JSON decoding of file content for file "'.$filePath.'" : '.json_last_error_msg(), E_USER_ERROR);
    return $jsonData;
}

function getMediaFolder ($root, $webroot, $recursive=false, $debugMe=false) {
    $excl = '/(?!.*thumbs).*/'; // exclude anything that includes 'thumbs' in it's filepath.
    //var_dump (FILE_FORMATS_photos_texts); exit;

    $files1 = getBackgroundFiles_forGetMediaFolder ($root, FILE_FORMATS_photos_texts, $excl, $recursive, $debugMe);
    $files2 = processBackgroundFiles_forGetMediaFolder ($files1, $root, $webroot, $recursive, $debugMe);

    return $files2;
}
function getBackgroundFiles_forGetMediaFolder ($root, $fileFormats, $excl, $recursive, $debugMe) {
    return getFilePathList ($root, true, $fileFormats, $excl, array('file'), null, 1, $recursive, $debugMe);
    //echo 'getBackgroundFiles(); $r:'; var_dump($recursive); var_dump ($r); exit();
}
function processBackgroundFiles_forGetMediaFolder (&$files, $root, $webRoot, $recursive, $debugMe) {
    $keyCount = 0;
    $valueCount = 0;
    $params =   array (
        'debugMe' => $debugMe,
        'root' => $root,
        'webRoot' => $webRoot,
        'recursive' => $recursive,
        'a' => &$files,
        'prevLevel' => 0,
        'keyCount' => &$keyCount,
        'valueCount' => &$valueCount
    );
    $callKeyForValues = false;
    walkArray ( $files, /*'processBackgroundFiles_forGetMediaFolder_key'*/null, 'processBackgroundFiles_forGetMediaFolder_value', $callKeyForValues, $params );
    return $files;
}
function processBackgroundFiles_forGetMediaFolder_key ($cd) {
    $path = $cd['path'].'/'.$cd['k'];
}
function processBackgroundFiles_forGetMediaFolder_value ($cd) {
    $debugMe = $cd['params']['debugMe'];
    if ($debugMe) echo '<pre style="color:white;background:green;margin-left:10px;margin:10px;border-radius:10px;padding:5px;">';

    if ($cd['k']!=='path') return false;
}










function getBackgrounds ($root, $webRoot, $recursive=false, $debugMe=false) {
    $excl = '/(?!.*thumbs).*/'; // exclude anything that includes 'thumbs' in it's filepath.

    $files1 = getBackgroundFiles ($root, FILE_FORMATS_photos_texts, $excl, $recursive, $debugMe);
    $files2 = processBackgroundFiles ($files1, $root, $webRoot, $recursive, $debugMe);
    //echo '<pre>'; var_dump ($root); var_dump ($webRoot); var_dump ($files2); exit();

    return $files2;
}
function getBackgroundFiles ($root, $fileFormats, $excl, $recursive, $debugMe) {
    return getFilePathList ($root, true, $fileFormats, $excl, array('file'), null, 1, $recursive, $debugMe);
    //echo 'getBackgroundFiles(); $r:'; var_dump($recursive); var_dump ($r); exit();
}
function processBackgroundFiles (&$files, $root, $webRoot, $recursive, $debugMe) {
    $keyCount = 0;
    $valueCount = 0;
    $params = array (
        'debugMe' => $debugMe,
        'root' => $root,
        'webRoot' => $webRoot,
        'recursive' => $recursive,
        'a' => &$files,
        'prevLevel' => 0,
        'keyCount' => &$keyCount,
        'valueCount' => &$valueCount
    );
    $callKeyForValues = false;
    walkArray ( $files, /*'processBackgroundFile_key'*/null, 'processBackgroundFile_value', $callKeyForValues, $params );
    return $files;
}
function processBackgroundFile_key ($cd) {
    $path = $cd['path'].'/'.$cd['k'];
}
function processBackgroundFile_value ($cd) {
    $debugMe = $cd['params']['debugMe'];
    if ($debugMe) echo '<pre style="color:white;background:green;margin-left:10px;margin:10px;border-radius:10px;padding:5px;">';

    if ($cd['k']!=='path') return false;

    // DO NOT DELETE THIS COMMENTED-OUT CODE.
    // IT IS FROM THE DAYS BEFORE LINUX SYMLINK USAGE TO .../siteMedia
    /*
    if ($cd['params']['recursive']) {
        if ($debugMe) { echo 'cd:'.PHP_EOL; var_dump ($cd['path']); var_dump ($cd['k']); var_dump ($cd['v']); }

        if (!is_int($cd['k'])) {
            $path1a = realpath($cd['path']).'/'.$cd['k'];
            if ($debugMe) echo 'path1a:'.$path1a.PHP_EOL;
            $path1b = substr($path1a,1);
            if ($debugMe) echo 'path1b:'.$path1b.PHP_EOL;
            $path = $path1b;
        } else {
            $path2 = $cd['path'];
            if ($debugMe) echo 'path2:'.$path2.PHP_EOL;
            $path = $path2;
        }
        if (!is_int($cd['v'])) {
            $file1a = realpath($cd['v']);
            if ($debugMe) echo 'file1a:'.$file1a.PHP_EOL;
            /*$file1b = substr($file1a,1);
            if ($debugMe) echo 'file1b:'.$file1b.PHP_EOL;
            $file1c = str_replace(realpath($cd['params']['root']),'',$file1b);
            if ($debugMe) echo 'file1c:'.$file1c.PHP_EOL;* /
            $file = $file1a;
        } else {
            $file2a = realpath($cd['v']);
            if ($debugMe) echo 'file2a:'.$file2a.PHP_EOL;
            $file2b = str_replace(realpath($cd['params']['root']),'',$file2);
            if ($debugMe) echo 'file2b:'.$file2b.PHP_EOL;
            $file = $file2b;
        }
        if ($path!=='') {
            $file3 = realpath($cd['params']['root']).$path.'/'.$cd['v'];
            if ($debugMe) {
                echo '$path:'.$path.PHP_EOL;
                echo 'file3:'.$file3.PHP_EOL;
            }
            $file = $file3;
        } else {
            $file4 = realpath($cd['params']['root']).$cd['v'];
            if ($debugMe) echo 'file4:'.$file4.PHP_EOL;
            $file = $file4;
        }
    } else {
        $path3 = $cd['path'].$cd['k'];
        $file3 = $cd['v'];
        $dbg = [ 'path3' => $path3, 'file3' => $file3 ];
        if ($debugMe) { var_dump ($dbg); echo PHP_EOL; }
        $path = $path3;
        $file = $file3;
    }

    if ($debugMe) { echo '<pre>';var_dump ($file);echo '</pre>'.PHP_EOL; exit(); }
    $td = str_replace ($cd['params']['root'], '', $file);
    //echo '<pre>';var_dump ($cd);echo '</pre>';exit();
    $path2 = $cd['path'].'/'.$cd['k'];
    $path2 = substr($path2,1);

    $ref = &chaseToPath ($cd['params']['a'], $path2);
    $xec = 'identify "'.$file.'"';
    exec ($xec, $output, $result);
    if ($result===0) {
        $regex = '/\s(\d+)x(\d+)\s/';
        preg_match_all ($regex, $output[0], $m);
        $wt = $m[1][0].'x'.$m[2][0];
        $ref = [ $td => $wt ];
    } else {
        $ref = [ $td => 'UNKNOWNxUNKNOWN' ];
    }
    */

    /*
    $path = $cd['path'];
    $file = $cd['v'];

    global $naWebOS;
    $td = str_replace ($cd['params']['root'], '', $file);
    echo $td.'<br/>';
    global $rootPath_na;
    $tdURL = str_replace($rootPath_na,'',$cd['params']['root']).'/'.$cd['k'];
    echo $tdURL.'<br/>';
    echo $rootPath_na; exit();
    if ($debugMe) {
        echo '<pre style="font-weight:bold;color:white;background:blue;margin:10px;margin-left:10px;padding:5px;border-radius:10px;">';
        var_dump ($cd['params']['root']);
        var_dump ($file);
        var_dump ($td);
        var_dump($cd['v']);
        var_dump($tdURL);
        echo '</pre>';
        //exit();
    }
    //$path2 = $cd['path'].'/'.$cd['k'];
    */

    //$debugMe = true;
    if ($cd['k']=='path') {
        $path2 = $cd['v'];
        $path2 = $cd['path'];//.'/'.$cd['k'];
        $path2 = substr($path2,1);
        //echo $path2.'<br/>';

        if ($debugMe) echo '<pre style="font-weight:bold;color:yellow;background:red;margin-left:10px;padding:5px;border-radius:10px;">$path2:'.$path2.'</pre>'.PHP_EOL;
        /*$path2 = substr($path2,1);
        if ($debugMe) echo '$path2b:'.$path2.PHP_EOL;*/

        $ref = &chaseToPath ($cd['params']['a'], $path2);
        if ($debugMe) { echo '<pre style="font-weight:bold;color:white;background:lime;margin-left:10px;padding:5px;border-radius:10px;">'; var_dump ($ref); echo '</pre>'; }
        if (true || ($ref!==false && is_array($ref))) {
            $file = $cd['v'];
            $tdURL = &chaseToPath ($cd['params']['a'], $path2.'/path');
            //echo $tdURL.'<br/>';
            //echo '<pre>'; var_dump($cd); echo '</pre>';
            global $rootPath_na;
            $tdURL = str_replace($cd['params']['root'].'/','',$tdURL);
            //echo $tdURL.'<br/>'; exit();
            // JUST DON'T:
            //$passSubArrs = is_array($ref);
            //if ($passSubArrs) foreach ($ref as $k1 => $v1) if (is_array($v1)) $passSubArrs = true;
            if ($debugMe) {
                //echo '$passSubArrs:'; var_dump ($passSubArrs);
                echo '<p style="color:cyan;background:navy;margin-left:20;padding:5px;border-radius:10px">';
                var_dump ($tdURL);
                var_dump ($file);
                var_dump ($path2);
                var_dump ($cd['k']);
                echo '</p>';
                //var_dump ($cd['params']['a']);
                //exit();

            }
            if ($debugMe && is_string($ref)) {
                echo '<pre class="font-weight:bold;color:white;background:lime;margin-left:10px;padding:5px;border-radius:10px;">';
                var_dump ($ref);
                echo '</pre>';
            }
            //if ($cd['k']=='path') {
                if ( $debugMe) {
                    $result = bindec(hex2bin('1977'));
                } else {
                    $xec = 'identify "'.$file.'"';
                    exec ($xec, $output, $result);
                }
                if ($result ===0) {
                    $regex = '/\s(\d+)x(\d+)\s/';
                    preg_match_all ($regex, $output[0], $m);
                    $wt = $m[1][0].'x'.$m[2][0];
                    if ($cd['params']['recursive']) {
                        $ref = $wt;//[ $td => $wt ];
                    } else {
                        $ref = [ $tdURL => $wt ];
                    }
                } else {
                    $wt = 'X*Y';
                    if (false && $cd['params']['recursive']) {
                        $ref = $wt;//[ $td => $wt ];
                    } else {
                        $ref = [ $tdURL => $wt ];
                    }
                }

                if ($debugMe) {
                    $dbg = [
                        'path' => $path,
                        'tdURL' => $tdURL,
                        'file' => $file,
                        'ref' => $ref
                    ];
                    var_dump ($dbg);
                    //echo '</pre>';
                }
            //}
        }
    }
    echo '</pre>';
}

function generate_all_unicode(): string {
    $str = '';
    for ($i = 0; $i <= 0x10FFFF; $i++) {
        if ($i >= 0xD800 && $i <= 0xDFFF) continue; // skip surrogates
        $str .= mb_chr($i, 'UTF-8');
    }
    return $str;
}
/*
$all = generate_all_unicode();
echo "Characters: " . mb_strlen($all, 'UTF-8') . "\n"; // → 1,111,998

$base64 = base64_encode($all); // PHP automatically uses UTF-8 bytes
echo "Base64 length: " . strlen($base64) . "\n"; // → 1,484,000
echo "First 100 chars: " . substr($base64, 0, 100) . "\n";
echo "Last 100 chars:  " . substr($base64, -100) . "\n";

// Save for comparison
file_put_contents('full_unicode_php.base64', $base64);
*/



//require_once (dirname(__FILE__).'/phpHardCrypto/boot.php');
//use Rene_AJM_Veerman\phpHardCrypto;
global $randomStringSeed;
$randomStringSeed = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
function maxRandomStringCombinations ($length) {
    global $randomStringSeed;
    return pow($length, strlen($randomStringSeed));
}

function randomString ($length) {
    global $randomStringSeed;
    $r = '';
    for ($i=0; $i<$length; $i++) {
        $r .= substr ($randomStringSeed, rand(0,strlen($randomStringSeed)), 1);
    };
    return $r;
}

$phpScript_startupTime = microtime(true); global $phpScript_startupTime;

function mainErrorHandler ($errno, $errstr, $errfile, $errline) {
    /*
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        echo 'die'; exit();
        return false;
    }*/

    global $naWebOS; global $naErr; global $naLog; global $naLAN;


    //trigger_error (json_encode(debug_backtrace()), E_USER_NOTICE);
    if ($naLAN) {
        $dbg = [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline
        ];
        {
            echo '<pre style="color:white;background:rgba(255,0,0,0.8);border-radius:10px;padding:8px;box-shadow:inset 1px 1px 3px 2px rgba(0,0,0,0.7), 2px 2px 2px 1px rgba(0,0,0,0.8);">'; echo json_encode($dbg, JSON_PRETTY_PRINT);
            //{ echo '<pre style="white-space:pre-wrap;word-break:break-all;font-size:small;color:white;background:rgba(50,0,0,0.5);border-radius:10px;padding:8px;box-shadow:inset 1px 1px 3px 2px rgba(0,0,0,0.7), 2px 2px 2px 1px rgba(0,0,0,0.8);">'; echo json_encode(debug_backtrace(), JSON_PRETTY_PRINT); echo '</pre>'; }
            echo '</pre>';
        }
    }

    if (
        is_object($naLog)
        && (
            !array_key_exists('HTTP_USER_AGENT',$_SERVER)
            || stripos($_SERVER['HTTP_USER_AGENT'], 'bot')===false
        )
        && stripos($_SERVER['SCRIPT_NAME'], 'logs.php')===false
    ) {
        $err = $naErr->add ($errno, $errstr, $errfile, $errline);
        $naLog->add ( [ $err ] ); // outputs to screen and apache log file as well.
    }

    return true;


}

function backtrace() {
    $data = debug_backtrace();
    //$data = array_splice ($data, 1, 2);
    //return '<pre>'.json_encode($data, JSON_PRETTY_PRINT).'</pre>';
    $html = '';
    while (count($data) > 0) {

        $it = array_shift($data);

        if (
            array_key_exists('function', $it)
            && (
                // ignore these, as they are part of the custom error handling system itself :
                $it['function'] == 'backtrace'
                || $it['function'] == 'mainErrorHandler'
                || $it['function'] == 'trigger_error'
            )
        ) continue;

        $html .= "\t\t\t".'<div class="backtraceItem">'.PHP_EOL;
        if (array_key_exists('class', $it) && $it['class']!=='') {
            $html .=
                "\t\t\t\t".'<span class="backtraceCodeLocation">'.PHP_EOL
                .'<span class="backtraceClassLabel">Class </span>'.PHP_EOL
                .'<span class="backtraceClass">'.$it['class'].'</span>'.PHP_EOL
                .'<span class="backtraceType">'.$it['type'].'</span>'.PHP_EOL
                .'<span class="backtraceFunction">'.$it['function'].'</span>'.PHP_EOL
                .'</span><br/>'.PHP_EOL.PHP_EOL;
        } else if (array_key_exists('function', $it) && $it['function']!=='') {
            $html .=
                "\t\t\t\t".'<span class="backtraceCodeLocation">'
                .'<span class="backtraceFunctionLabel">Function </span>'.PHP_EOL
                .'<span class="backtraceFunction">'.$it['function'].'</span>'.PHP_EOL
                .'</span><br/>'.PHP_EOL.PHP_EOL;
        }
        if (array_key_exists('file', $it) && $it['file']!=='') {
            $html .=
                "\t\t\t\t".'<span class="backtraceFileLabel">Location</span>'.PHP_EOL
                .'<span class="backtraceFile">'.$it['file'].'</span><span class="backtraceFunctionArgSeperator"> : </span>'
                .'<span class="backtraceLineNum">'.$it['line'].'</span>';
        }
        if (array_key_exists('args',$it) && is_array($it['args']) && count($it['args']) > 0) {
            $html .= "\t\t\t\t".'<div class="backtraceFunctionArgs"><span class="backtraceFunctionArgsLabel">Function arguments</span><span class="backtraceFunctionArgSeperator"> : </span>';
            foreach ($it['args'] as $idx => $arg) {
                if (is_array($arg)) {
                    $html .= "\t".'<span class="backtraceFunctionArg">'.json_encode($arg).'</span><span class="backtraceFunctionArgSeperator">,</span> ';
                } else {
                    $html .= "\t".'<span class="backtraceFunctionArg">'.json_encode($arg).'</span><span class="backtraceFunctionArgSeperator">,</span> ';
                }
            }
            $html .= '</div>'.PHP_EOL;
        }
        $html .= '</div><br/>'.PHP_EOL.PHP_EOL.PHP_EOL;
    }
    return $html;
}

function checkForJSONerrors($rawData, $filepath, $exampleFilepath) {
    if (json_last_error() !== JSON_ERROR_NONE) {
        $error =
            '<div class="phpJSONerror">'.PHP_EOL
                .'<div class="phpJSONerrorTitle">PHP JSON ERRROR :</div>'.PHP_EOL
                .'<div class="phpJSONerror_location">'.PHP_EOL
                    .'<span class="phpJSONerror_filepathLabel">File path : </span>'
                    .'<span class="phpJSONerror_filepath">'.$filepath.'</span><br/>'.PHP_EOL
                    .'<span class="phpJSONerror_exampleFilepathLabel">Example template file path : </span>'.PHP_EOL
                    .'<span class="phpJSONerror_exampleFilepath">'.$exampleFilepath.'</span>'.PHP_EOL
                .'</div>'.PHP_EOL
                .'<div class="phpJSONerror_rawData">'.htmlspecialchars($rawData).'</div>'.PHP_EOL
                .'<div class="phpJSONerror_message">'
                    .'<span class="phpJSONerror_msgLabel">Error message : </span>'
                    .'<span class="phpJSONerror_msg">'.json_last_error_msg().'</span>'
                .'</div>'
            .'</div>';
        trigger_error ($error, E_USER_ERROR);
        return false;
    }
    return true;
}

function translate_plainUserName_to_couchdbUserName ($un) {
    global $naWebOS;
    $dn = $naWebOS->domainFolderForDB;
    $un = str_replace($dn.'___', '', $un);
    return $dn.'___'.str_replace('.','__',str_replace(' ', '_', $un));
}
function translate_couchdbUserName_to_plainUserName ($un) {
    $un = preg_replace('/.*___/','', $un);
    return str_replace('_',' ',str_replace('__', '.', $un));
}

function translate_plainGroupName_to_couchdbGroupName ($gn) {
    global $naWebOS;
    $dn = $naWebOS->domainFolderForDB;
    //echo '<pre style="color:red">'; var_dump ($dn); echo '</pre>';
    $gn = str_replace($dn.'___', '', $gn);
    //echo '<pre style="color:purple">'; var_dump ($dn); echo '</pre>';
    return $dn.'___'.str_replace('.','__',str_replace(' ', '_', $gn));
}
function translate_couchdbGroupName_to_plainGroupName ($gn) {
    $gn = preg_replace('/.*___/','', $gn);
    return str_replace('_',' ',str_replace('__', '.', $gn));
}


function cdb_login($db, $cdb, $cRec, $username) {
    //echo '$_COOKIE=<pre>';var_dump($_COOKIE);echo '</PRE>';
    $fncn = '.../NicerAppWebOS/functions.php::cdb_login()';

    $done = false;
    /* ANTIQUATED CODE :
    if (
        $username=='nicer_app___Rene_AJM_Veerman'
        || $username=='said_by___Rene_AJM_Veerman'
        || $username=='192_168_178_29___Rene_AJM_Veerman'
    ) {
        try {
            $cdb->login ($cRec['username'], $cRec['password']);
        } catch (Throwable $e) {
            trigger_error ($fncn.' : could not login using credentials '.json_encode($cRec).'.', E_USER_ERROR);
            return false;
        } catch (Exception $e) {
            trigger_error ($fncn.' : could not login using credentials '.json_encode($cRec).'.', E_USER_ERROR);
            return false;
        }
        $done = true;
    } else {
    */

    //echo '<pre style="color:white;background:navy;">'; var_dump ($username); var_dump($cRec); echo '</pre>';

    if (
        is_array($cRec)
        && array_key_exists('password', $cRec)
        && is_string ($cRec['password'])
    ) {
        try {
            global $naLAN;

            if ($cRec['username']=='admin') {
                $cdb->login ($cRec['username'], 'texass.t33');
            } else {
                //if ($naLAN) {echo '<pre>';debug_print_backtrace(); var_dump($cRec);echo '</pre>';}//exit;}
                $cdb->login ($db->translate_plainUserName_to_couchdbUserName($cRec['username']), $cRec['password']);
            }
            $cdb_session = $cdb->getSession();
            if (
                is_object($cdb_session)
                && $cdb_session->body->ok
                && !is_null($cdb_session->body->userCtx->name)
            ) {
                if ($username!=='admin') $_SESSION['cdb_loginName'] = $cdb_session->body->userCtx->name;
                //echo '<pre style="color:green;">'; var_dump($_SESSION); echo '</pre>';
                $done = true;
            }
        } catch (Exception $e) {
            $dbg = [ 'username' => $cRec['username'] ];
            trigger_error ($fncn.' : could not login using credentials '.json_encode($dbg).', $e->getMessage()='.$e->getMessage(), E_USER_WARNING);
        }
    }


    //if (!$done) die();
    //echo 't333:'; var_dump ($done);

    if (
        !$done
        && is_array($_COOKIE)
        && array_key_exists('cdb_authSession_cookie',$_COOKIE)
        && is_string($_COOKIE['cdb_authSession_cookie'])
        && $_COOKIE['cdb_authSession_cookie']!==''
    ) {
        try {
            $r = $cdb->loginByCookie ($_COOKIE['cdb_authSession_cookie']);
        } catch (Throwable $e) {
              try {
                //echo '<pre>';var_dump($cRec);die();
                $cdb->login ($db->translate_plainUserName_to_couchdbUserName('Guest'), 'Guest');
                $cdb_session = $cdb->getSession();
                //echo '<pre>';var_dump($cdb_session);die();
                if (
                    is_object($cdb_session)
                    && $cdb_session->body->ok
                    && !is_null($cdb_session->body->userCtx->name)
                ) {
                    if ($username!=='admin') $_SESSION['cdb_loginName'] = $cdb_session->body->userCtx->name;
                    //echo '<pre style="color:green;">'; var_dump($_SESSION); echo '</pre>';
                    $done = true;
                }
            } catch (Exception $e) {
                trigger_error ($fncn.' : could not login using credentials '.json_encode($cRec).', $e->getMessage()='.$e->getMessage(), E_USER_WARNING);
            }

        }

        try {
            $cdb_session = $cdb->getSession();
            //var_dump($cdb_session);
        } catch (Throwable $e) {
            if (
                is_array($cRec)
                && array_key_exists('password', $cRec)
                && is_string ($cRec['password'])
            ) {
                $_SESSION['cdb_loginName'] = $cRec['username'];
                $cdb->login ($cRec['username'], $cRec['password'], Sag::$AUTH_COOKIE);
                $cdb_session = $cdb->getSession();
                if (
                    is_object($cdb_session)
                    && $cdb_session->body->ok
                    && !is_null($cdb_session->body->userCtx->name)
                ) {
                    $done = true;
                }
            }
        }
        if (
            isset($cdb_session)
            && is_object($cdb_session)
            && $cdb_session->body->ok
            && !is_null($cdb_session->body->userCtx->name)
        ) {
            $done = true;
        } else {
            if (
                is_array($cRec)
                && array_key_exists('password', $cRec)
                && is_string ($cRec['password'])
            )
                $cdb->login ($cRec['username'], $cRec['password'], Sag::$AUTH_COOKIE);
            else
                $cdb->login ($db->translate_plainUserName_to_couchdbUserName('Guest'), 'Guest', Sag::$AUTH_COOKIE);
            if ($cRec['username']!=='Guest') trigger_error ('Session cookie expired. You have been logged in as \''.$cRec['username'].'\'', E_USER_WARNING);
            //echo '<pre>'; var_dump ($cdb->getSession()); exit();
            if (
                is_object($cdb_session)
                && $cdb_session->body->ok
                && !is_null($cdb_session->body->userCtx->name)
            ) {
                $done = true;
            }
        }
    } elseif (
        !$done
        && is_array($_COOKIE)
        && array_key_exists('AuthSession',$_COOKIE)
        && is_string($_COOKIE['AuthSession'])
        && $_COOKIE['AuthSession']!==''
    ) {
        $r = $cdb->loginByCookie ($_COOKIE['AuthSession']);
        $done = true;
    }


    //echo 't593:'; var_dump ($done);
    if ($done) {
        $cdb_session = $cdb->getSession();
        //echo '<pre>'; var_dump($cdb_session->body->userCtx->name); echo '</pre>';// exit;

        global $naLAN;
	$dn = null;
        if ($cdb_session->body->userCtx->name!=='admin') {
        //if ($naLAN) {
            $dbName = '_users';
	    global $naWebOS;
	    $cdba = $naWebOS->dbsAdmin->findConnection('couchdb')->cdb;
            try {
                $cdba->setDatabase($dbName, false);
            } catch (Exception $e) {
                echo 'cdb_login() ERROR 1 : '.$e->getMessage();
		exit;
            }

            $findCommand = [
                'selector' => [
                    'name' => $cdb_session->body->userCtx->name
                ],
                'fields' => ['_id', 'name', 'displayName']
            ];

            try {
                $call = $cdba->find($findCommand);
            } catch (Exception $e) {
                echo '<pre>'; var_dump ($findCommand); echo PHP_EOL.'<br/>'.PHP_EOL.$e->getMessage(); echo '</pre>'; exit;
            }
            //echo '<pre>'; var_dump ($findCommand); echo PHP_EOL.'<br/>'.PHP_EOL.json_encode ($call, JSON_PRETTY_PRINT); echo '</pre>'; exit;
	    if (
		    array_key_exists(0,$call->body->docs)
		    && property_exists($call->body->docs[0],'displayName')
	    ) $dn = $call->body->docs[0]->displayName;
        }

        return [
            'username' => $cdb_session->body->userCtx->name,
	    'displayName' => $dn,
            'roles' => $cdb_session->body->userCtx->roles
        ];
    } else return false;
}


// * outdated, superceded by .../NicerAppWebOS/phpHardCrypto which uses the built-in 'sodium' encryption library functions in PHP7+
function useRememberme_birke($credential='Guest') {
    // Initialize RememberMe Library with file storage
    $storagePath = dirname(__FILE__)."/siteCache/tokens";
    if (!is_writable($storagePath) || !is_dir($storagePath)) {
        die(
            "'$storagePath' does not exist or is not writable by the web server.\n".
            "To run the example, please create the directory and give it the correct permissions."
        );
    }
    global $rememberMeStorage;
    $rememberMeStorage = new FileStorage($storagePath);
    global $rememberMe;
    $rememberMe = new Authenticator($rememberMeStorage);
    global $loginResult;
    $loginResult = $rememberMe->login();
    //echo 'lr:'; var_dump ($loginResult->getCredential());

    if ($loginResult->isSuccess()) {
        $_SESSION['cdb_loginName'] = $loginResult->getCredential();
        //setcookie('cdb_loginName', $loginResult->getCredential(), time() + 604800, '/');
        //echo '<pre>';var_dump ($_SESSION);exit();
        // There is a chance that an attacker has stolen the login token, so we store
        // the fact that the user was logged in via RememberMe (instead of login form)
        $_SESSION['remembered_by_cookie'] = true;


    } else {
        $loginResult = $rememberMe->createCookie($credential);
        $loginResult = $rememberMe->login();
        $_SESSION['cdb_loginName'] = $loginResult->getCredential();
        $_SESSION['remembered_by_cookie'] = true;
    };
    return $loginResult;
}

function ascii2hex($ascii) {
  $hex = '';
  for ($i = 0; $i < strlen($ascii); $i++) {
    $byte = strtoupper(dechex(ord($ascii[$i])));
    $byte = str_repeat('0', 2 - strlen($byte)).$byte;
    $hex.=$byte;
  }
  return $hex;
}



function encode_base64_url($string) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

    return str_replace(['+','/','='], ['-','_',''], base64_encode($string));
}

function decode_base64_url($string) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

    return base64_decode(str_replace(['-','_'], ['+','/'], $string));
}

function execPHP_oldNbuggy ($file, $flush=false) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

    if (is_array($file)) $file = $file['realPath'];
    if ($flush) {
        ob_flush(); // NOT WISE AT ALL (nested calls to execPHP() will crash JSON decoding in the browser due to HTML inserted in AJAX response before the JSON data.
        $c = '';
    } else {
        $c = ob_get_contents();
        if ($c===false) $c = '';
    }
    ob_end_clean();
    ob_start();
    $p = strpos($file,'?');
    $qs = substr($file, $p+1, strlen($file)-$p-1);
    $f = (
        $p === false
        ? $file
        : substr($file, 0, $p)
    );

    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    echo "<h2>About to include: " . $file . "</h2>\n";
    //echo '<pre>'; debug_print_backtrace(); echo '</pre>';
    //flush(); ob_flush();
    //echo $qs; exit();
    if ($p!==false) parse_str ($qs, $_GET); // may seem like a dirty hack, but isn't.
    //echo '$flush='; var_dump ($flush); echo PHP_EOL.PHP_EOL;
    //echo '$f='; var_dump ($f); echo PHP_EOL.PHP_EOL;
    echo '$c1='; var_dump ($c); echo PHP_EOL.PHP_EOL;
    include ($f);
    $c .= ob_get_contents();
    echo '$c2='; var_dump ($c); echo PHP_EOL.PHP_EOL;
    ob_end_clean();    ob_start();    flush(); ob_flush(); ob_end_flush();
    return $c;
};

/**
 * Safely execute a PHP file and return its output as string
 * Works much better with nested includes and AJAX responses
 */
function execPHP(string $file, bool $flush = false): string
{
    if (is_array($file)) {
        $file = $file['realPath'] ?? $file[0] ?? '';
    }

    if (empty($file) || !file_exists($file)) {
        return '<h2>HTTPS Error : file not found - ' . htmlspecialchars($file) . '</h2>';
    }
    //echo '<h1>'.$file.'</h1>';

    // Parse query string if present (your old trick)
    $p = strpos($file, '?');
    if ($p !== false) {
        $qs = substr($file, $p + 1);
        $f  = substr($file, 0, $p);
        parse_str($qs, $_GET);
    } else {
        $f = $file;
    }

    // === SAFER BUFFERING APPROACH ===
    ob_start();

    try {
        //echo "<!-- execPHP: including {$f} -->\n";   // your debug (visible in HTML)

        include $f;                                 // <-- this should now output correctly

        $output = ob_get_clean();                   // get everything that was output

        if ($flush) {
            echo $output;           // send to browser immediately
            flush();
            return '';
        }

        return $output;

    } catch (Throwable $e) {
        $output = ob_get_clean();                   // get everything that was output
        ob_end_clean(); // make sure we don't leave a buffer open on error

        error_log("execPHP error in {$f}: " . $e->getMessage());
        global $naLAN;
        if (false && $naLAN) {
            return '<p>execPHP error in ' . htmlspecialchars($f) . ': ' . htmlspecialchars($e->getMessage()).'<br/></p><pre>'.json_encode(debug_backtrace(),JSON_PRETTY_PRINT).'</pre>'.$output;
        } else {
            return '<p>execPHP error in ' . htmlspecialchars($f) . ': ' . htmlspecialchars($e->getMessage()) . '</p>'.$output;
        }
    }
}

function require_return_oldNbuggy ($file, $flush=false) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

// used by .../domainConfigs/DOMAIN.EXT/mainmenu.php
    //trigger_error ('require_return() : $file='.$file, E_USER_NOTICE);
    $c = '';
    try {
        if ($flush) {
            if (ob_get_contents()!==false) ob_flush(); // NOT WISE AT ALL (nested calls to execPHP() will crash JSON decoding in the browser due to HTML inserted in AJAX response before the JSON data.
        } else {
            $c = ob_get_contents();
            if ($c===false) $c = '';
        }
        if (ob_get_contents()!==false) ob_end_clean();
    } catch (Throwable $e) {
    } catch (Exception $e) {
    }
    ob_start();
    //error_reporting(E_ALL);
    //ini_set('display_errors', 1);
    //ini_set('display_startup_errors', 1);
    //echo "<h2>About to return: " . $file . "</h2>\n";
//    echo '<pre>'; debug_print_backtrace(); echo '</pre>';
    flush(); ob_flush();
    include ($file);
    //echo '$c1='; var_dump ($c); echo PHP_EOL.PHP_EOL;

    $c .= ob_get_contents();
    //echo '$c2='; var_dump ($c); echo PHP_EOL.PHP_EOL;

    ob_end_clean();
    ob_start();
    flush(); ob_flush(); ob_end_flush();

    return $c;
}

function require_return($file, $once = false) {
    if (is_array($file)) $file = $file['realPath'] ?? $file;

    if (empty($file) || !file_exists($file)) {
        return '<!-- require_return error: file not found - ' . htmlspecialchars((string)$file) . ' -->';
    }

    ob_start();
    try {
        if ($once) {
            require_once $file;
        } else {
            require $file;
        }
        $output = ob_get_clean();

        $r = $output !== false ? $output : '';
        return $r;
    } catch (Throwable $e) {
        ob_end_clean();
        //error_log("require_return error in {$file}: " . $e->getMessage()."\n".json_encode(debug_backtrace(),JSON_PRETTY_PRINT));
        error_log("require_return error in {$file}: " . $e->getMessage());
        global $naLAN;
        if ($naLAN) {
            return '<h1>Error in template ' . htmlspecialchars($file) . ': ' . ($e->getMessage()) .'</h1><pre>'.$e->getTraceAsString(). '</pre>';
        } else {
            return '<h1>Error in template ' . htmlspecialchars($file) . ': ' . ($e->getMessage()) .'</h1>';
        }
    }
}

function htmlentities_recursive($data, $flags = ENT_QUOTES | ENT_HTML5, $encoding = 'UTF-8') {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = htmlentities_recursive($value, $flags, $encoding);
        }
        return $data;
    }

    if (is_object($data)) {
        foreach (get_object_vars($data) as $key => $value) {
            $data->$key = htmlentities_recursive($value, $flags, $encoding);
        }
        return $data;
    }

    // Only convert strings
    if (is_string($data)) {
        return htmlentities($data, $flags, $encoding);
    }

    // Leave numbers, booleans, null, etc. unchanged
    return $data;
}

function cssArray_seperate ($id, $regExps, $exclRegExps, $arr) {
    $ret = [ $id => [] ];
    $debug = false;
    if ($debug) { echo '<pre style="color:green">$arr='; var_dump ($arr); echo '</pre>';exit(); }
    foreach ($regExps as $rIdx => $regex) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                preg_match_all ($regex, $key, $m, PREG_PATTERN_ORDER);

                if ($debug) {
                    echo '<pre style="color:blue">$key='; var_dump ($key); echo "\n\$m="; var_dump($m);
                    echo "\npreg_last_error_msg()=".preg_last_error_msg();
                    echo '</pre>';
                }

                $pass = false;
                if (array_key_exists(1,$m) && count($m[1])===1) { $k = $m[1][0]; $pass = true; }
                if (array_key_exists(2,$m) &&count($m[2])===1) { $k = $m[2][0];  $pass = true; }

                foreach ($exclRegExps as $rIdx2 => $regex2) {
                    preg_match_all ($regex2, $key, $m2, PREG_PATTERN_ORDER);
                    $pass2 = (
                        (array_key_exists(1,$m2) && count($m2[1])===1)
                        || (array_key_exists(1,$m2) && count($m2[1])===1)
                    );
                    if ($pass2) $pass = false;
                }


                if ($pass) {
                    $k = strtoupper(substr($k,0,1)).substr($k,1);
                    if (!array_key_exists($k,$ret[$id])) $ret[$id][$k] = [];
                    $ret[$id][$k] = array_merge_recursive($ret[$id][$k], [ 'css' => [ $key => $value ]]);
                }
            }
        }
    }
    if ($debug) {
        echo '<pre style="color:green">$ret='; var_dump($ret); echo '</pre>';
        //exit();
    };
    return $ret;
}
function css_array_to_css2($rules, $indent = 0) {
    $css = '';
    $prefix = str_repeat('  ', $indent);

    //echo '<pre style="color:blue">'; var_dump ($rules); echo '</pre>';
    if (is_array($rules))
    foreach ($rules as $key => $value) {
        //echo '<pre style="color:green">'; var_dump ($key); echo '</pre>';
        if (is_array($value) && $key=='css') {
            $css .= $prefix . css_array_to_css($value, $indent + 1);
        } elseif (is_array($value)) {
            $css .= $prefix . css_array_to_css2 ($value, $indent);
        }
    }
    //elseif (is_string($rules)) $css .= $rules;

    return $css;
}

function css_array_to_css($rules, $indent = 0) {
    $css = '';
    $prefix = str_repeat('  ', $indent);

    foreach ($rules as $key => $value) {
        if (is_array($value)) {
            $selector = $key;
            $properties = $value;

            $css .= $prefix . "$selector {\n";
            $css .= $prefix . css_array_to_css($properties, $indent + 1);
            $css .= $prefix . "}\n";
        } else {
            $property = $key;
            $selector2 = '';
            for ($i=0; $i<strlen($property); $i++) {

                // translate Hungarian notation to dashed notation :
                $c = substr($property, $i, 1);
                if ($c === strtolower($c)) {
                    $selector2 .= $c;
                } else {
                    $selector2 .= '-'.strtolower($c);
                }
            }
            $property = $selector2;
            $suffix = (
                strpos($value, '!important')!=false
                    ?preg_replace('/\s\s/',' ',str_replace('!important','',$value))." !important;\n"
                    :$value." !important;\n"
            );
            $suffix = $value.";".PHP_EOL;
            $css .= $prefix.$property.': '.$suffix;
        }
    }

    return $css;
}

function css_to_array ($cssContent) {
    $ret = [];

    $c = explode ("\n", $cssContent);
    //echo '<pre style="color:white;background:green">'; var_dump ($c); echo '</pre>';
    foreach ($c as $lineIdx => $lc) {
        if (strpos($lc, '{')!==false) {
            $k = trim(explode('{',$lc)[0]);
            //echo '<pre style="color:cyan;background:green">'; var_dump ($k); echo '</pre>';
            $ret[$k] = [];
        }
        if (strpos($lc, ':')!==false) {
            $kv = trim (explode(':',$lc)[0]);
            $kv1 = explode('-', $kv);
            if (count($kv1)===1) {
                $kv = $kv;
            } else {
                $kv = $kv1[0].ucfirst($kv1[1]);
            };
            //echo '<pre style="color:white;background:green">'; var_dump ($kv); echo '</pre>';
            $v = trim (explode(':',$lc)[1]);
            $v = str_replace (';','',$v);
            //echo '<pre style="color:black;background:green">'; var_dump ($v); echo '</pre>';
            $ret[$k][$kv] =$v;
        }
    }
    //echo '<pre style="color:white;background:green">'; var_dump ($ret); echo '</pre>';
    return $ret;
}

function css_keyframes_to_array ($cssContent) {
    $ret = [];

    $c = explode ("\n", $cssContent);
    //echo '<pre style="color:white;background:green">'; var_dump ($c); echo '</pre>';
    foreach ($c as $lineIdx => $lc) {
        if (strpos($lc,'%')===false && strpos($lc, '{')!==false) {
            $k = trim(explode('{',$lc)[0]);
            //echo '<pre style="color:cyan;background:green">'; var_dump ($k); echo '</pre>';
            $ret[$k] = [];
        }
        if (strpos($lc, '%')!==false) {
            $pv = trim (explode('%',$lc)[0]);
            //echo '<pre style="color:yellow;background:darkgreen">'; var_dump ($pv); echo '</pre>';
            $v = trim (explode('{',$lc)[1]);
            $v = str_replace ('}','',$v);
            //echo '<pre style="color:white;background:darkgreen">'; var_dump ($v); echo '</pre>';
            $ret[$k][$pv.'%'] =[];
        }
        if (strpos($lc, ':')!==false) {
            $kv = trim (explode(':',$lc)[0]);
            $kv1 = explode('-', $kv);
            if (count($kv1)===1) {
                $kv = $kv;
            } else {
                $kv = $kv1[0].ucfirst($kv1[1]);
            };
            //echo '<pre style="color:white;background:green">'; var_dump ($kv); echo '</pre>';
            $v = trim (explode(':',$lc)[1]);
            $v = str_replace (';','',$v);
            //echo '<pre style="color:black;background:green">'; var_dump ($v); echo '</pre>';
            $ret[$k][$pv.'%'][$kv] =$v;
        }
    }
    //echo '<pre style="color:white;background:green">'; var_dump ($ret); echo '</pre>';
    return $ret;
}

function css_animation_template_to_animation ($theme, $anim, $selectors, $searchReplace) {
    $t = $theme; $a = $anim; $s = $selectors; $sr = $searchReplace; $debug = false;
    if ($debug) { echo '<pre style="background:navy;color:grey;">'; var_dump ($sr); echo '</pre>'; }
    foreach ($s as $i => &$rawSel) {
        $selFinal = '@keyframes '.$rawSel.'__'.$theme;
        if ($debug) { echo '<span style="background:blue;color:white;">'.$selFinal.'</span><br/>'; }
        foreach ($a as $j => &$rawA) {
            if ($j==$selFinal) {
                if ($debug) { echo '<pre style="background:navy;color:yellow;">'; var_dump ($rawA); echo '</pre>'; }
                foreach ($rawA as $k => &$rawAss) { // rawAss = raw animation stop settings. :D
                    foreach ($sr as $l => &$rawSR) {
                        if ($k === $l) {
                            // have reached an animation stop that needs changing
                            foreach ($rawSR as $m => &$rawPS) { //rawPS = raw CSS property settings
                                foreach ($rawAss as $n => &$rawPS2) {
                                    if ($m === $n) {
                                        if ($debug) {
                                            echo '<pre style="background:green;color:yellow;">'; var_dump ($rawPS); echo '</pre>';
                                            echo '<pre style="background:darkgreen;color:lime;">'; var_dump ($rawAss); echo '</pre>';
                                            echo '<pre style="background:darkgreen;color:white;">'; var_dump ($rawPS2); echo '</pre>';
                                        }
                                        foreach ($rawPS as $o => &$sre) { // sre = search-replace entry
                                            if ($debug) {
                                                echo '<pre style="background:red;color:white;">'; var_dump ($rawPS2); echo '</pre>';
                                                echo '<pre style="background:red;color:yellow;">'; var_dump (preg_replace ($sre['search'], $sre['replace'], $rawPS2)); echo '</pre>';
                                            }
                                            $a[$j][$k][$n] = preg_replace ($sre['search'], $sre['replace'], $rawPS2);

                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    unset ($rawSel); unset ($rawA); unset ($rawAss); unset ($rawSR); unset ($rawPS); unset ($rawPS2); unset ($sre);
    if ($debug) { echo '<pre style="background : blue; color : yellow;">'; var_dump ($a); echo '</pre>'; }
    return $a;
}

function css_animation_array_to_css ($anim, $indent=0) {
    $css = '';
    $prefix = str_repeat('  ', $indent);

    foreach ($anim as $keyFrameID => $kf) {
        $css .= $prefix . "$keyFrameID {\n";
        $css .= $prefix . css_array_to_css($kf, $indent + 1);
        $css .= $prefix . "}\n";
    }

    return $css;
}

function css_animation_keys_to_css ($theme, $keys, $indent=0) {
    $css = '';
    $prefix = str_repeat('  ', $indent);
    $prefix2 = str_repeat('  ', $indent+1);
    foreach ($keys as $k => $v) {
            $css .= $prefix.$k." {\n";
            $css .= $prefix2."animation : ".$v.'__'.$theme." 1s forwards;\n";
            $css .= $prefix."}\n";
    }
    return $css;
}


function naWebOS_photoAlbum_countFiles ($jobs) {
    $r = 0;
    foreach ($jobs as $jobIdx => $job) {
        $root = $job['path'];
        $thumbsPath = $job['thumbnails'];
        $delThumbs = $job['delThumbs'];

        if (file_exists($root)) {
            $files = getFilePathList ($root, true, FILE_FORMATS_photos, null, ['file']);

            $files3 = [];
            foreach ($files as $idx => $file) {
                if (strpos($file,'/thumbs/thumbs')!==false) {
                    $pi = pathinfo($file);
                    $xec = 'rm -rf "'.$pi['dirname'].'"';
                    exec ($xec, $output, $result);
                    continue;
                }
                if (strpos($file,'/thumbs')===false) {
                    $files3[] = $file;
                }
            }

            $r += count($files3);
        }
    }
    return $r;
}

function naWebOS_photoAlbum_resizeFiles ($totalFileCount, $totalJobsCount, $jobsDoneCount, $jobIdx, $job) {
    global $filePerms_ownerUser;
    global $filePerms_ownerGroup;
    global $filePerms_perms_publicWriteableExecutable;
    global $filePerms_perms_readonly;
    global $filePerms_perms_readWrite;
    //var_dump($job); echo PHP_EOL; exit();

    $root = $job['path'];
    $thumbsPath = $job['thumbnails'];
    $delThumbs = $job['delThumbs'];
    $arguments = $job['arguments'];
    $excl = '/(?!.*thumbs).*/'; // exclude anything that includes 'thumbs' in it's filepath.
    $excl2 = null;


    //var_dump ($files); exit();
    $files3 = [];

    $msgJobs = ($jobsDoneCount+1).'of'.$totalJobsCount;
    $fncn = 'naWebOS_photoAlbum_convert() job '.$msgJobs.' :: ';
    echo $fncn.'Figuring out which files to convert.'.PHP_EOL;
    //var_dump ($delThumbs); var_dump ($excl); exit();

    if ($delThumbs) {
        $files = getFilePathList ($root, true, FILE_FORMATS_photos, $excl, ['file'], null, 1, false);
        foreach ($files as $idx => $file) {
            if (strpos($file,'/thumbs')!==false) {
                $pi = pathinfo($file);
                $xec = 'rm -rf "'.dirname($pi['dirname']).'"';
                exec ($xec, $output, $result);
            }
        }
        exit();
    } else {
        $files = getFilePathList ($root, true, FILE_FORMATS_photos, $excl, ['file'], null, 1, false);
    }
    //echo '<pre>'; var_dump ($files); echo '</pre>'; exit();

    foreach ($files as $idx => $file) {
        $file = $file['realPath'];
        if (strpos($file,'/thumbs/thumbs')!==false) {
            $pi = pathinfo($file);
            $xec = 'rm -rf "'.$pi['dirname'].'"';
            exec ($xec, $output, $result);
            continue;
        }
        if (strpos($file,'/thumbs')===false) {
            $f = dirname($file).'/thumbs/'.basename($file);
            //echo 't23:'.$f.PHP_EOL;
            $found = false;
            foreach ($files as $idx2=>$file2) {
                if ($f===$file2) $found = true;
            }
            if (!$found) $files3[] = $file;
        }
    }

    global $filePerms_ownerUser;
    global $filePerms_ownerGroup;
    global $filePerms_perms;

    $sizeIndex = ['__root__'=>$root];
    foreach ($files3 as $idx => $file) {
        try {
            $original_info = getimagesize($file);
            $original_w = $original_info[0];
            $original_h = $original_info[1];

            $sizeIndex[str_replace($root,'',$file)] = [$original_w,$original_h];
        } catch (Throwable $e) {
            echo $fncn.$t->getMessage().'.'.PHP_EOL;
            continue;
        } catch (Exception $e) {
            echo $fncn.$t->getMessage().'.'.PHP_EOL;
            continue;
        }
        //var_dump ($original_info); exit();

        // my (and the end-user's) thanks go to https://ansi.gabebanks.net/.
        $cEnd = '';
        $cHL = ''; //colorsHighlighted
        $cEnd = "\x1b[39;49m"; // back to default colors
        $cMsgCat = ""; // message category colors
        if ($original_w > 7000) {
            $cHL = "\x1b[32;49m"; // green
        } elseif ($original_w > 5000) {
            $cHL = "\x1b[36;49m"; // cyan
        } elseif ($original_w > 3700) {
            $cHL = "\x1b[34;49m"; // blue
        } elseif ($original_w > 1000) {
            $cHL = "\x1b[33;49m"; // yellow
        }

        $w1 = '(w='.$cHL.$original_w.$cEnd.',h='.$cHL.$original_h.$cEnd.')';
        $msg = $fncn.$cMsgCat.'NOW RESIZING, $thumbsPath="'.$thumbsPath.'", $file="'.$file.'", from '.$w1.$cMsgCat.' to ';
        echo $msg;

        $didAny = false;

        $msgJobs = ($jobsDoneCount+1).'of'.$totalJobsCount;
        $fncn = 'naWebOS_photoAlbum_convert() job '.$msgJobs.', fileCount='.count($files3).', fileIdx='.$idx.' : ';

        $tp = $thumbsPath;
        $tp = str_replace('./','',$tp);
        $tp = str_replace('/$filename.$ext','',$tp);

        $pi3 = pathinfo($file);
        $file2 = $pi3['dirname'].'/'.$tp.'/300/'.$pi3['basename'];
        $x1 = createDirectoryStructure (dirname($file2), $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);

        $file3 = $pi3['dirname'].'/'.$tp.'/651/'.$pi3['basename'];
        $x2 = createDirectoryStructure (dirname($file3), $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);

        $file4 = $pi3['dirname'].'/'.$tp.'/1920/'.$pi3['basename'];
        $x3 = createDirectoryStructure (dirname($file4), $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);

        $file5 = $pi3['dirname'].'/'.$tp.'/3840/'.$pi3['basename'];
        $x4 = createDirectoryStructure (dirname($file5), $filePerms_ownerUser, $filePerms_ownerGroup, $filePerms_perms_publicWriteableExecutable);

        $dbg = [
            'x1' => $x1,
            'x2' => $x2,
            'x3' => $x3
        ];

        $dbg2 = [
            '$file2' => $file2,
            'file_exists($file2)' => file_exists($file2),
            '$file3' => $file3,
            'file_exists($file3)' => file_exists($file3),
            '$file4' => $file4,
            'file_exists($file4)' => file_exists($file4),
            '$file5' => $file5,
            'file_exists($file5)' => file_exists($file5)
        ];
        //echo '<pre style="color:cyan;background:navy;border-radius:10px;margin:10px;">';
        //var_dump ($dbg2);
        //echo '</pre>';


        //var_dump ($dbg);

        try {
            $original_img = null;

            if (!file_exists($file2)) {
                if (is_null($original_img)) $original_img = imagecreatefromjpeg($file);
                $thumb_w = 300;
                $thumb_h = round(($original_h * $thumb_w)/$original_w);
                $thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
                if (imagecopyresampled($thumb_img, $original_img,
                                0, 0,
                                0, 0,
                                $thumb_w, $thumb_h,
                                $original_w, $original_h)
                ) {;
                    imagejpeg($thumb_img, $file2);
                    imagedestroy($thumb_img);
                    imagedestroy($original_img);

                    $w1 = '(w='.$cHL.$original_w.$cEnd.',h='.$cHL.$original_h.$cEnd.')';
                    $w2 = '(w='.$cHL.$thumb_w.$cEnd.',h='.$cHL.$thumb_h.$cEnd.')';
                    $msg = $fncn.$cMsgCat.'FILE RESIZED, $thumbsPath="'.$thumbsPath.'", from '.$w1.$cMsgCat.' to '.$w2.$cMsgCat.', '.$cEnd.'$file="'.$file.'"'.PHP_EOL;

                    $msg = $w2.' ';
                    echo $msg;
                } else {
                    $msg = $fncn.PHP_EOL.'File "'.$file2.'" could not be created.'.PHP_EOL;
                    echo $msg;
                }
                $didAny = true;
            }

            if (!file_exists($file3)) {
                if (is_null($original_img)) $original_img = imagecreatefromjpeg($file);
                $thumb_w = 651;
                $thumb_h = round(($original_h * $thumb_w)/$original_w);
                $thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
                if (imagecopyresampled($thumb_img, $original_img,
                                0, 0,
                                0, 0,
                                $thumb_w, $thumb_h,
                                $original_w, $original_h)
                ) {;
                    imagejpeg($thumb_img, $file3);
                    imagedestroy($thumb_img);
                    imagedestroy($original_img);
                    $w1 = '(w='.$cHL.$original_w.$cEnd.',h='.$cHL.$original_h.$cEnd.')';
                    $w2 = '(w='.$cHL.$thumb_w.$cEnd.',h='.$cHL.$thumb_h.$cEnd.')';
                    $msg = $fncn.$cMsgCat.'FILE RESIZED, $thumbsPath="'.$thumbsPath.'", from '.$w1.$cMsgCat.' to '.$w2.$cMsgCat.', '.$cEnd.'$file="'.$file.'"'.PHP_EOL;

                    $msg = $w2.' ';
                    echo $msg;
                } else {
                    $msg = $fncn.PHP_EOL.'File "'.$file3.'" could not be created.'.PHP_EOL;
                    echo $msg;
                }
                $didAny = true;
            }

            if ($original_w > 2200 && !file_exists($file4)) {
                if (is_null($original_img)) $original_img = imagecreatefromjpeg($file);
                $thumb_w = 1920;
                $thumb_h = round(($original_h * $thumb_w)/$original_w);
                $thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
                if (imagecopyresampled($thumb_img, $original_img,
                                0, 0,
                                0, 0,
                                $thumb_w, $thumb_h,
                                $original_w, $original_h)
                ) {;
                    imagejpeg($thumb_img, $file4);
                    imagedestroy($thumb_img);
                    imagedestroy($original_img);
                    $w1 = '(w='.$cHL.$original_w.$cEnd.',h='.$cHL.$original_h.$cEnd.')';
                    $w2 = '(w='.$cHL.$thumb_w.$cEnd.',h='.$cHL.$thumb_h.$cEnd.')';
                    $msg = $fncn.$cMsgCat.'FILE RESIZED, $thumbsPath="'.$thumbsPath.'", from '.$w1.$cMsgCat.' to '.$w2.$cMsgCat.', '.$cEnd.'$file="'.$file.'"'.PHP_EOL;

                    $msg = $w2.' ';
                    echo $msg;
                } else {
                    $msg = $fncn.PHP_EOL.'File "'.$file4.'" could not be created.'.PHP_EOL;
                    echo $msg;
                }
                $didAny = true;
            }

            if ($original_w > 4400 && !file_exists($file5)) {
                if (is_null($original_img)) $original_img = imagecreatefromjpeg($file);
                $thumb_w = 3840;
                $thumb_h = round(($original_h * $thumb_w)/$original_w);
                $thumb_img = imagecreatetruecolor($thumb_w, $thumb_h);
                if (imagecopyresampled($thumb_img, $original_img,
                                0, 0,
                                0, 0,
                                $thumb_w, $thumb_h,
                                $original_w, $original_h)
                ) {;
                    imagejpeg($thumb_img, $file5);
                    imagedestroy($thumb_img);
                    imagedestroy($original_img);

                    $w1 = '(w='.$cHL.$original_w.$cEnd.',h='.$cHL.$original_h.$cEnd.')';
                    $w2 = '(w='.$cHL.$thumb_w.$cEnd.',h='.$cHL.$thumb_h.$cEnd.')';
                    $msg = $fncn.$cMsgCat.'FILE RESIZED, $thumbsPath="'.$thumbsPath.'", from '.$w1.$cMsgCat.' to '.$w2.$cMsgCat.', '.$cEnd.'$file="'.$file.'"'.PHP_EOL;

                    $msg = $w2.' ';
                    echo $msg;
                } else {
                    $msg = $fncn.PHP_EOL.'File "'.$file5.'" could not be created.'.PHP_EOL;
                    echo $msg;
                }
                $didAny = true;
            }

            if (!$didAny) echo '(no resizings were necessary)';
            echo '<br/>';
            echo PHP_EOL;

            if ($didAny) usleep (500);

        } catch (Throwable $t) {
            echo $fncn.$t->getMessage().'.'.PHP_EOL;
        } catch (Exception $e) {
            echo $fncn.$e->getMessage().'.'.PHP_EOL;
        }


        /*
        if ($idx/13 === round($idx/13)) {
            echo '13 files done, sleeping for 5 seconds.'.PHP_EOL;
            sleep (5);
        }

        $xec = 'mogrify -set comment \'File checked by mogrify.\' "'.$file.'" 2>&1';
        exec ($xec, $output, $result1);

        $xec = 'convert "'.$file.'" -resize 200 "'.$file2.'" 2>&1';
        exec ($xec, $output, $result);

        $result = $result1.$result;
//var_dump ($output); exit();
        if (
            (
                $result === 0
                ||
                $result === '00'
            )
            && (
                is_array($output)
                && count($output)===0
            )
        ) {
            $dbg = [
                'idx' => $idx,
                'count' => count($files3),
                'xec' => $xec,
                'output' => $output,
                'result' => $result
            ];
            $msg = $fncn.'Succeeded.'.PHP_EOL;
            echo $msg;
            //var_dump ($dbg);
        } else {
            echo $fncn.'CORRUPTED FILE - DELETING ORIGINAL *AND* THUMBNAIL.'.PHP_EOL;
            $dbg = [
                'idx' => $idx,
                'count' => count($files3),
                'xec' => $xec,
                'output' => $output,
                'result' => $result
            ];
            var_dump ($dbg); echo PHP_EOL;

            $xec = 'rm "'.$file.'"';
            echo $xec.PHP_EOL;
            //exec ($xec, $output, $result);

            $xec = 'rm "'.$file2.'"';
            echo $xec.PHP_EOL;
            //exec ($xec, $output, $result);
        }
        /* waste of time :
        $xec = 'identify -verbose "'.$file.'" 2>&1 | grep "corrupt image"';
        exec ($xec, $output, $result);
        if (count($output)>0) {
            echo 'CORRUPTED FILE "'.$file.'" - DELETING ORIGINAL *AND* THUMBNAIL.'.PHP_EOL;

            $xec = 'rm "'.$file.'"';
            exec ($xec, $output, $result);

            $xec = 'rm "'.$file2.'"';
            exec ($xec, $output, $result);
        }
        */
    }

    file_put_contents ($root.'/sizeIndex.json', json_encode($sizeIndex,JSON_PRETTY_PRINT));

    return count($files);
}

function createDirectoryStructure ($filepath, $ownerUser=null, $ownerGroup=null, $filePerms=null) {
$fncn = "createDirectoryStructure";
$debug = false;
/*	Creates a directory structure.
    Returns a boolean success value. False usually indicates illegal characters in the directories.

    If you supply a filename as part of $filepath, all directories for that filepath are created.
    If $filepath==only a directory, you TODO**MUST**TODO end $filepath with / or \
*/

    global $filePerms_ownerUser;
    global $filePerms_ownerGroup;
    global $filePerms_perms_publicWriteableExecutable;
    global $filePerms_perms_readonly;
    global $filePerms_perms_readWrite;

    if (is_null($ownerUser)) $ownerUser = $filePerms_ownerUser;
    if (is_null($ownerGroup)) $ownerGroup = $filePerms_ownerGroup;
    if (is_null($filePerms)) $filePerms = $filePerms_perms_publicWriteableExecutable;


    //slash-direction doesn't matter for PHP4 file functions :-), so we even things out first;
    $filepath = strtr (trim($filepath), "\\", "/");
    if ($filepath[strlen($filepath)-1]!="/") $filepath.="/";
    if ($filepath[0]!="/") $filepath="/".$filepath;
    if ($debug) { echo '<pre class="debug_createDirectoryStructure">'; };
    if ($debug) { echo $fncn.'()'.PHP_EOL; echo '$filepath='; var_dump ($filepath); echo PHP_EOL.PHP_EOL; }

    if (($filepath[1]!=':') && ($filepath[0]!='/')) trigger_error ("$fncn: $filepath is not from the root. results would be unstable. gimme a filepath with / as first character.", E_USER_ERROR);

    $directories = explode ("/", $filepath);
    $i = count($directories)-1;
    $j = $i;
    while ($j > -1) {
        if ($directories[$j]==='') unset ($directories[$j]);
        $j--;
    };
    $result = true;
    if ($debug && false) { echo '1::$directories='; var_dump ($directories); echo PHP_EOL.PHP_EOL; }

    for ($i = count($directories); $i>0; $i--) {
        $pathToTest = '/'.implode ("/", array_slice($directories,0,$i+1));
        if ($debug) { echo '$pathToTest='; var_dump ($pathToTest); echo PHP_EOL.'file_exists($pathToTest)='; var_dump(file_exists($pathToTest)); echo PHP_EOL.PHP_EOL; }
        if (file_exists($pathToTest)) break;
    }

    $dbg = array (
        'ptt' => $pathToTest,
        'i' => $i,
        'dirs' => $directories,
         'backtrace' => debug_backtrace()
    );

    if ( ($i < count($directories)) ) {
        if ($debug) { var_dump ($dbg); echo PHP_EOL.PHP_EOL; }

        for ($j = $i; $j < (count($directories)); $j++) {
            $pathToCreate = '/'.implode ("/", array_slice($directories,0,$j+1));
            //var_dump ($pathToCreate);
            if (!file_exists($pathToCreate)) {
                //$filePerms = 0770; // dirty hack for said.by and the now more secure .../setPermissions.sh
                if ($debug) { echo 'p2='; var_dump ($pathToCreate); debug_print_backtrace();}
                try {
                    //don't work no more? $result=mkdir($pathToCreate,0750,true);//!is_null($filePerms)?$filePerms:0777);

                    // does work :
                    $xec = 'mkdir -p "'.$pathToCreate.'"';
                    exec ($xec, $output, $result);
                    $dbg = [
                        '$xec' => $xec,
                        '$output' => $output,
                        '$result' => $result
                    ];
                    if ($debug) { echo 'p3='; var_dump ($dbg); echo PHP_EOL; };

                    $xec = 'chown root:www-data "'.$pathToCreate.'"';
                    exec ($xec, $output, $result);
                    $dbg = [
                        '$xec' => $xec,
                        '$output' => $output,
                        '$result' => $result
                    ];
                    if ($debug)  { echo 'p4='; var_dump ($dbg); echo PHP_EOL; };


                    $xec = 'chmod 770 "'.$pathToCreate.'"'; // very important or photo uploads just wont work
                    exec ($xec, $output, $result);
                    $dbg = [
                        '$xec' => $xec,
                        '$output' => $output,
                        '$result' => $result
                    ];
                    if ($debug) { echo 'p5='; var_dump ($dbg); echo PHP_EOL; };

                    if (is_string($ownerUser)) $x = chown ($pathToCreate, $ownerUser);
                    if (is_string($ownerGroup)) $y = chgrp ($pathToCreate, $ownerGroup);
                    if (!is_null($filePerms)) $z = chmod ($pathToCreate, 0770);
                } catch (Exception $e) {
                    $dbg = [ '$result' => $result, 'chown' => $x, 'chgrp' => $y, 'chmod' => $z, 'msg' => $e->getMessage() ];
                    echo '$dbg='; var_dump ($dbg);
                }

            }
        }
    }
    if ($debug) echo '</pre>';
    return true;
}

function getFilePathList (
//TODO: all features listed below $debug are untested.

	//$pathStart,
	$path,								// current path
	$recursive = false,					// if true, we also process any subdirectory.
	$fileSpecRE = "/.*/",				// Regular Expression file specs - will be matched against any filename found.
	// ^-- this is NOT the same as normal "somefile-*.something.extension" type wildcards. see example above.
    $excludeFolders = null,
	$fileTypesFilter = array (),		// array (int=>string (filetype() result) ==== int=>"file"|"dir" )
	$depth = null,
	$level = 1,
	$returnRecursive = false,           // whether or not to return the filepaths found as a recursive array (true) or a flat list array (false).
  $debug = false,
	$ownerFilter = array (),			// array (int=>string (username) ); only return files owned by someone in $ownerFilter.
	$fileSizeMin = null,				// If >=0, any files returned must have a minimum size of $fileSizeMin bytes.
	$fileSizeMax = null,				// same as above, but maximum size

	/* all date parameters below must be provided in the mktime() format. */
	$aTimeMin = null,					// calls fileatime(). Read The Friendly Manual. http://www.php.net/manual/
	$aTimeMax = null,					//	^- access includes a program reading from this file.
	$mTimeMin = null,					// calls filemtime(). RTFM.
	$mTimeMax = null,
	$cTimeMin = null,					// calls filectime(). rtfm.
	$cTimeMax = null,
	/*	on windows XP, cTime = creation time; mTime = modified time; aTime = access time.
		I also noted some BUGS in retrieving these dates from my system.
	*/
	$listCall = "",						// interesting feature; lets you include results from any informational file function(s).
    $pathStart = null,
    $realPath = null,
    $flatList = false
/*	TODO : fix $*Date* parameter handling,
	returns an array consisting of all files in a directory structure, filtered by the parameters given.
	results are returned in directory order. if ($recursive) then subdirectory content is listed before file content.
	OKAY, this one is monolithic :)   But very usefull, so an exception to the rule is granted here.
example:
	htmlDump (getFilePathList("c:/dat/web", true, "/.*\.php$|.*\.php\d$|.*\.inc$/",
		array(), array(), null, null, null, null, null, null, null, null,
		"\"ctime=\".date (\"Y/m/d H:m:s\", filectime (\$filepath)).".
		"\" - atime=\".date (\"Y/m/d H:m:s\", fileatime (\$filepath)).".
		"\" - mtime=\".date (\"Y/m/d H:m:s\", filemtime (\$filepath)).".
		";"
		));
	-== this returns an array with complete filepaths of all files under c:/dat/web, that have an extension like
		*.php, *.php3, *.php4 or *.inc.
		for my system, it returns:
			array(4) {
			  [0]=>
			  string(115) "c:/dat/web/index.php - [listCall=ctime=2003/05/11 18:05:26 - atime=2003/05/16 05:05:44 - mtime=2003/05/16 05:05:44]"
			  [1]=>
			  string(122) "c:/dat/web/preProcessor.php - [listCall=ctime=2003/05/15 16:05:55 - atime=2003/05/16 04:05:47 - mtime=2003/05/15 17:05:35]"
			  [2]=>
			  string(116) "c:/dat/web/source.php - [listCall=ctime=2003/05/11 18:05:26 - atime=2003/05/16 04:05:47 - mtime=2003/04/28 13:04:07]"
			  [3]=>
			  string(117) "c:/dat/web/sources.php - [listCall=ctime=2003/05/11 18:05:26 - atime=2003/05/16 04:05:50 - mtime=2003/05/12 00:05:22]"
}
		in this example, the $listCall is kinda complicated. but only to show it's power.
		if you're having trouble debugging your $listCall, turn on the relevant jsonViewer() call in this function.

another example:
	jsonViewer (getFilePathList("c:/dat/web", false, "/.*\.php$|.*\.php\d$|.*\.inc$/",
		array(), array(), null, null, null, null, null, time()-mktime (0,0,0,0,1,0));
	-== this returns, for my system, all *.php,*.php3/4,*.inc files in c:/dat/web, that havent changed since 24 hours ago:
*/

) {
    //$debug = true;
    $fncn = '.../NicerAppWebOS/functions.php/getFilePathList()';
    if (is_null($pathStart)) $pathStart = $path;
    $realPath = realpath($path);
    global $naWebOS;

	//if (stripos($path, $pathStart)!==false) {
		if ($debug) { echo '<pre style="color:black;background:lime;">'; var_dump ($path); var_dump ($excludeFolders); echo '</pre>'; };
		if (!is_dir($path)) {
            $msg = $fncn.' : FATAL ERROR : "'.$path.'" does not exist.';
            $msg = $naWebOS->getContent__standardErrorMessage($msg)['siteContent'];
            echo '<pre  style="background:rgba(0,0,0,0.5);border:1px solid grey; box-shadow:2px 2px 4px 2px rgba(0,0,0,0.7); padding:10px;margin:20px;border-radius:8px;color:ivory;text-shadow:2px 2px 3px rgba(0,0,0,7);">';debug_print_backtrace();
            //trigger_error ($msg, E_USER_ERROR);
            echo $msg;
            exit();
        }

		if (!is_null($excludeFolders)) {
            $r = preg_match($excludeFolders, $realPath, $ms);
            if ($debug) {
                echo '<pre style="color:yellow;background:navy;border-radius:10px;padding:5px;">';
                //var_dump (debug_backtrace()); echo PHP_EOL;
                echo '$r='; var_dump($r); echo PHP_EOL;
                echo '$path='; var_dump ($path); echo PHP_EOL;
                echo '$realPath='; var_dump ($realPath); echo PHP_EOL;
                echo '$excludeFolders='; var_dump ($excludeFolders); echo PHP_EOL;
                echo '$ms='; var_dump($ms);//exit();
                echo '</pre>';
            }
        }


        $result = [];
		if (
            is_null($excludeFolders)
            || $r === 0
            || $recursive
		) {
            //if (!in_array("file",$fileTypesFilter)) $fileTypesFilter[count($fileTypesFilter)]="file";
            //htmlOut (" --== $path ==--");

            if ($path[strlen($path)-1]!="/") $path.="/";
            if ($realPath[strlen($realPath)-1]!="/") $realPath.="/";
            if ($handle = opendir($realPath)) {
                /* This is the correct way to loop over the directory. */
                while (false !== ($file = readdir($handle))) {
                //if (!is_file($path.$file)) continue;
                    if ($file != "." && $file != "..") {

                        $pass = true;
                        //echo $path.$file.'<br/>';
                        $ft = filetype($path.$file);
                        //var_dump($path.$file);
                        //if ($ft=='dir') { var_dump ($ft); die(); }

                        if (!in_array ($ft, $fileTypesFilter)) $pass = false;
                        if ($debug) { echo '<pre style="color:red">'; var_dump ($ft); var_dump($fileTypesFilter); echo '</pre>'; };
                        if ($ft=="dir") $filepath = $path.$file."/"; else $filepath = $path.$file;

                        if ($debug && false) {
                            echo '<pre style="color:yellow;background:red;border-radius:10px;padding:5px;">';
                            var_dump ($filepath); echo PHP_EOL;
                            var_dump ($fileSpecRE); echo PHP_EOL;
                            var_dump ($excludeFolders); echo PHP_EOL;
                            var_dump ($pass); echo PHP_EOL;
                        }
                        if ($debug) { echo '#p0='; var_dump ($pass); echo PHP_EOL; }
                        if ($pass/* && !$recursive*/) $pass = preg_match ($fileSpecRE, $filepath) === 1 || $recursive;
                        if ($debug) { echo '#p1a='; var_dump (preg_match ($fileSpecRE, $filepath)); echo '#p1b='; var_dump($fileSpecRE); echo '#p1c=';var_dump ($filepath); echo '#p1d='; var_dump ($pass); echo '<br/>'.PHP_EOL; }
                        if ($pass && !is_null($excludeFolders) && $excludeFolders!=='') $pass = preg_match ($excludeFolders, $filepath) === 0 || $recursive;
                        if ($debug) { echo '#p2='; var_dump ($pass); echo PHP_EOL; }
                        if ($debug) echo '</pre>';
                        if ($pass && count($ownerFilter)>0) {
                            $fo = fileowner ($filepath);
                            if ($fo!=false) {
                                $fo = posix_getpwuid($fo);
                                if (!in_array ($fo, $ownerFilter)) $pass=false;
                            } else {
                            //couldn't retrieve username. be strict & safe, fail.
                                $pass = false;
                            }
                        }
                        if ($pass && isset($fileSizeMin)) if (filesize ($filepath) < $fileSizeMin) $pass=false;
                        if ($pass && isset($fileSizeMax)) if (filesize ($filepath) > $fileSizeMax) $pass=false;

                        if ($pass && isset($aTimeMin))
                            $pass=evalDate ("fileatime", $filepath, ">=", $aTimeMin, "aTimeMin");
                        if ($pass==true && isset($aTimeMax))
                        //	^- if ($stringValue) == always true!,
                        //		so explicitly check for boolean true result after calling
                        //		functions that may return an (error) string.
                            $pass=evalDate ("fileatime", $filepath, "<=", $aTimeMax, "aTimeMax");
                        if ($pass==true && isset($mTimeMin))
                            $pass=evalDate ("filemtime", $filepath, ">=", $mTimeMin, "mTimeMin");
                        if ($pass==true && isset($mTimeMax))
                            $pass=evalDate ("filemtime", $filepath, "<=", $mTimeMax, "mTimeMax");
                        if ($pass==true && isset($cTimeMin))
                            $pass=evalDate ("filectime", $filepath, ">=", $cTimeMin, "cTimeMin");
                        if ($pass==true && isset($cTimeMax))
                            $pass=evalDate ("filectime", $filepath, "<=", $cTimeMax, "cTimeMax");

                        $r = "";

                        /*
                        if (false && $debug) {
                            echo '$excludeFolders='; var_dump($excludeFolders); echo '<br/>'.PHP_EOL;
                            echo '$pass='; var_dump($pass); echo '<br/>'.PHP_EOL;
                            echo '$filepath='; var_dump($filepath); echo '<br/>'.PHP_EOL;
                        };


                        if (false && is_string($pass)) {
                            //htmlOut ("PASSED - checks failed");
                            $result[count($result)] = "[$pass]".$filepath;
                        }
                        */

                        if (false && $debug) { echo 'preg_match($excludeFolders, $filepath.$r)='; var_dump(preg_match($excludeFolders, $filepath.$r)); echo '<br/>'.PHP_EOL; }

                        //if (    (true && $ft=='dir') || $debug) {
                        if ($debug) {
                            $dbg = [
                                //'bt' => debug_backtrace(),
                                'filepath' => $filepath,
                                'fp' => $path.$file,
                                'frp' => $realPath.$file,
                                'r' => $recursive,
                                'rr' => $returnRecursive,
                                'ft' => $ft,
                                'pass' => $pass,
                                'c' => (is_null($depth) || $level < $depth)
                            ];
                            echo '<pre style="color:white;background:brown;border-radius:5px;padding:5px;">'; echo json_encode ($dbg,JSON_PRETTY_PRINT); echo '</pre>';
                        }

                        if (
                            $recursive
                            && $ft=="dir"
                            //&& $pass // DON'T, if you want any backgrounds to show up in the results at all.
                            /*
                            && (
                                is_null($depth)
                                || $level<=$depth
                            )
                              && preg_match($excludeFolders, $filepath.$r)===1
                            */
                        ) {
                            $subdir = @getFilePathList ($filepath,$recursive, $fileSpecRE, $excludeFolders,
                                $fileTypesFilter, $depth, $level+1, $returnRecursive, $debug, $ownerFilter, $fileSizeMin, $fileSizeMax,
                                $aTimeMin, $aTimeMax, $mTimeMin, $mTimeMax,
                                $cTimeMin, $cTimeMax, $listCall, $pathStart, $realPath, $flatList);
                            if ($debug) { echo '<pre style="color:yellow;background:red;border-radius:5px;">$fp='.$filepath.', $pathStart='.$pathStart.', $subdir='; var_dump($subdir); echo '</pre>'.PHP_EOL; };
                            if (count($subdir) > 0)
                                if (!$returnRecursive || $flatList===true) {
                                    if (!is_null($subdir) && count($subdir)>0) array_splice ($result, count($result)+1, 0, $subdir);
                                } else {
                                    if (
                                        !is_null($subdir)
                                        && count($subdir)>0
                                    ) {
                                        if (!array_key_exists('folders',$result))
                                            $result['folders'] = [];
                                        $result['folders'][basename($filepath.$r)] = $subdir;
                                    }
                                }
                        }

                        if ($pass==true || $recursive) {
                            //htmlOut ("PASSED");

                            $ev = "\$r = $listCall";
                            //htmlDump ($ev);
                            if (!empty($listCall)) eval ($ev);
                            $idx = count ($result);
                            if (!empty($r)) $r = " - [listCall=$r]";
                            /*if (!$returnRecursive) {
                                $result[$idx] = $filepath.$r;
                            } else {
                                $result[$idx] = basename($filepath.$r);
                            }*/
                            //if (!array_key_exists('files',$result)) $result['files'] = [];
                            //$result['files'][basename($filepath)] = $filepath;//DON'T! str_replace($pathStart,'',$filepath);
                            global $naWebOS;
                            //debug_print_backtrace();die();
                            if (in_array ($ft, $fileTypesFilter))
                            if (!$returnRecursive) {
                                $result[$idx] = [
                                    'path' => $filepath.$r,
                                    'realPath' => $realPath.basename($filepath),
                                    'webPath' => '/'.str_replace($naWebOS->path,'',str_replace($pathStart.'/','',$filepath))
                                ];
                                $result[$idx]['webPath'] = str_replace('/code','',$result[$idx]['webPath']);
                                //echo '<pre style="color:white;background:blue;padding:5px;margin:10px;border-radius:8px;">';var_dump($naWebOS->webPath);  var_dump($result[$idx]);echo '</pre>';
                            } else {
                                if (!array_key_exists('files',$result)) $result['files'] = [];
                                $result['files'][basename($filepath)] = [
                                    'path' => $filepath,
                                    'realPath' => $realPath.basename($filepath),
                                    'webPath' => '/'.str_replace($naWebOS->path,'',str_replace($pathStart.'/','',$filepath))
                                ];
                                $result['files'][basename($filepath)]['webPath'] = str_replace('/code','',$result['files'][basename($filepath)]['webPath']);

                                //echo '<pre style="color:blue;background:navy;padding:5px;margin:10px;border-radius:8px;">'; var_dump($naWebOS->webPath); var_dump($result['files'][basename($filepath)]);echo '</pre>';
                            }

                        }

                    } // !dot-files ('.' && '..', current & parent path on OS commandline)
                } //!preg_match($excludeFolders, $path)
			}
		}
		//htmlDump ($result, "result");
		if ($debug) {
            echo '<pre style="color:purple;background:yellow;border:3px ridge black;padding:5px;border-radius:10px;">$path='.$path.'; $result='; var_dump ($result); echo '</pre>';
            echo '<pre style="color:magenta;background:yellow;border:3px ridge black;padding:5px;border-radius:10px;">$realPath='.$realPath.'; $result='; var_dump ($result); echo '</pre>';

        };
		return $result;
	//}
	//$result = array();
	//return $result;
}



function addThumbsToFolder ($folder) {
    $files = getFilePathList ($root, true, '/.*\.gif|.*\.jpg|.*\.jpeg|.*\.png/', array('file'));
    sort ($files);
    //echo '<pre style="color:orange;">'; var_dump ($files); echo '</pre>';
    ob_start();

    foreach ($files as $idx => $original) {
        if ($verbose) {
            echo '<p style="color:green;">'.$idx.' of '.count($files).' : '.$original.'</p>';
        }
        $thumb = str_replace(basename($original), 'thumbs/'.$original, $original);
        createDirectoryStructure (dirname($thumb));
        if (!file_exists($thumb)) {
            $xec = 'convert "'.$original.'" -resize 200 "'.$thumb.'"';
            exec ($xec, $output, $result);
            if ($showOutput) {
                $dbg = array (
                    'idx' => $idx,
                    'count' => count($files),
                    'exec' => $xec,
                    'output' => $output,
                    'result' => $result
                );
                echo '<pre>';var_dump($dbg); echo '</pre>';
            }
            ?>
            <script type="text/javascript">
                document.scrollTop = 999999999999;
            </script>
            <?php
            ob_flush();
            ob_end_flush();
            flush();
            ob_start();
        }
        //exit();
    }
}








function walkArray (&$a, $keyCallback=null, $valueCallback=null, $callKeyForValues=false, $callbackParams=null, $k='', $level=0, $path='') {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

// usage : walkArray ($someRecursiveArray, 'walkArray_printKey', 'walkArray_printValue');
// can handle recursive arrays. a nested array is a recursive array.
// is faster, especially on large arrays, than RecuriveArrayIterator, see speed testing comment at http://php.net/manual/en/class.recursiveiteratoriterator.php
// provides detailed information to callbacks on where in the data we are, something that array_walk_recursive just doesnt do.
// passes data around as pointers, not copies of data.
    if (is_numeric($k)) $k = intval($k);
    if (!is_array($a)) {
        trigger_error ('walkArray() was called but $a parameter passed is not an array.', E_USER_ERROR);
        return false;
    } else {
        foreach ($a as $k=>&$v) {
            $cd = array ( // callback data
                'type' => 'key',
                'path' => $path,
                'level' => $level,
                'a' => &$a,
                'k' => &$k,
                'v' => &$v,
                'params' => &$callbackParams
            );
            //$cd['params']['a'] = &$a;

            if (is_string($keyCallback) && ($callKeyForValues || is_array($v)))
                call_user_func ($keyCallback, $cd);

            if (is_array ($v)) {
                walkArray ($a[$k], $keyCallback, $valueCallback, $callKeyForValues, $callbackParams, $k, $level+1, $path.'/'.$k);
            } else {
                $cd['type'] = 'value';
                if (is_string($valueCallback)) call_user_func ($valueCallback, $cd);
            }
        }
    }
    $r = true;
    return goodResult($r);
}


function walkArray_printKey ($cd) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

    echo '<div style="background:blue;color:yellow;border-radius:5px;padding:2px;margin-top:5px;">'.PHP_EOL;
    $indent = 20 * $cd['level'];
    echo '<div style="padding-left:'.$indent.'px">'.PHP_EOL;
    echo 'key : '.$cd['k'].'<br/>'.PHP_EOL;
    echo 'path : '.$cd['path'].'<br/>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
}

function walkArray_printValue ($cd) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

    echo '<pre style="background:green;color:white;border-radius:5px;padding:2px;margin-top:2px;">'.PHP_EOL;
    $indent = 20 * $cd['level'];
    echo '<div style="padding-left:'.$indent.'px">'.PHP_EOL;
    echo 'key : '.$cd['k'].'<br/>'.PHP_EOL;
    echo 'path : '.$cd['path'].'<br/>'.PHP_EOL;
    echo 'value : '.$cd['v'].'<br/>'.PHP_EOL;
    echo '</div>'.PHP_EOL;
    echo '</pre>'.PHP_EOL;
}


function &chaseToPath (&$wm, $path, $create=false, $targetData=false) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

    //var_dump ($create); exit();
    //echo '$wm=<pre>'; var_dump ($wm);echo '</pre>'; //exit();
    //$path = str_replace ('/', '/d/', $path);
    //$path .= '/d';
    $nodes = explode ('/', $path);
    foreach ($nodes as $idx=>$node) {
        if (is_numeric($node) && is_string($node)) {
            if (strpos($node,'.')===false) {
                $nodes[$idx] = (int)$node;
            } else {
                $nodes[$idx] = (float)$node;
            }
        }
    }
    $chase = &chase ($wm, $nodes, $create, $targetData);

    if (false) {
        //echo '$wm=<pre>'; var_dump ($wm);echo '</pre>'; exit();
        $dbg = array (
            '$path' => $path,
            '$nodes' => $nodes,
            '$wm' => $wm,
            '$chase' => $chase
        );
        echo '$dbg=<pre style="background:red;color:yellow;">'; var_dump ($dbg); echo '</pre>'.PHP_EOL.PHP_EOL;
        //exit();
    }



    $false = false;
    if (good($chase)) {
        $arr = &result($chase);
        return $arr;
    } else return $false;
}

function &chase (&$arr, $indexes, $create=false, $targetData=false) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

    if (false) {
        echo 'nicerapp/functions.php --- $arr=<pre>'; var_dump ($arr); echo '</pre>';
        echo 'nicerapp/functions.php --- $indexes=<pre>'; var_dump ($indexes); echo '</pre>';
        echo 'nicerapp/functions.php --- $create=<pre>'; var_dump ($create); echo '</pre>';
        echo 'nicerapp/functions.php --- $targetData=<pre>'; var_dump ($targetData); echo '</pre>';
    }
	$r = &$arr;
	foreach ($indexes as $idx) {
           // echo 'sitewide/functions.php --- $idx=<pre>'; var_dump ($idx); var_dump (array_key_exists($idx,$r)); var_dump ($r); echo '</pre>';
            if (
                    is_array($r)
                    && (
                            $create===true
                            || array_key_exists($idx,$r)
                    )
            ) {
                    if ($create===true && !array_key_exists($idx,$r)) $r[$idx]=array();
                    //echo 'sitewide/functions.php --- $idx=<pre>'; var_dump ($idx); echo '</pre>';
                    $r = &$r[$idx];
            } else {
                /*
                    $err = array(
                    'msg' => 'Could not walk the full tree',
                    'vars' => array(
                            '$idx--error'=>$idx,
                            '$indexes'=>$indexes,
                            '$arr'=>$arr
                            )
                    );
                    badResult (E_USER_NOTICE, $err);
                  */
                    $ret = false; // BUG #2 squashed
                    return $ret;
            }
	}
	if (is_array($targetData))
    foreach ($targetData as $k => $v) {
        //echo PHP_EOL.'$k='; var_dump($k); echo PHP_EOL;
        if (is_array($v) && $idx=='') {
            $r[$k] = $v;
        } else {
            $r[$k] = $v;
        }
    }

        //echo 'sitewide/functions.php --- $r=<pre>'; var_dump ($r); echo '</pre>';
	return goodResult($r);
}

function &chaseToReference (&$array, $path) {
    /*
     * LICENSE : https://opensource.org/license/mit
     * (C) +-2020AD to 2025AD (possibly later, see https://nicer.app/NicerAppWebOS/version.json or https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/version.json)
     * (C) 2025 "Rene A.J.M. Veerman" <rene.veerman.netherlands@gmail.com>
     */

	if (!empty($path)) {
		if (empty($array[$path[0]])) {
			$err = array(
				'msg' => 'Could not walk the full tree',
				'$path' => $path,
				'$array (possibly partially walked)' => $array
			);
			return badResult (E_USER_NOTICE, $err);
		} else return chaseToReference($array[$path[0]], array_slice($path, 1));
	} else {
		return goodResult($array);
	}
}

function good($r) {
	return (
		is_array($r)
		&& array_key_exists('result',$r)
	);
}

function &result(&$r) {
	return $r['result'];
}

function &resultArray (&$r) {
  $r2 = array();
  foreach ($r as $k => $v) {
    $r2[$k] = result($v);
  }
  return $r2;
}


function &goodResult(&$r) {
	$r2 = array (
		'isMetaForFunc' => true,
		'result' => &$r
	);
	return $r2;
}

function safeJSONload($filePath, $exampleFilepath='[NOT_SPECIFIED]') {
    $fncn = 'safeJSONload("'.$filePath.'")';
    if (!file_exists($filePath)) {
        trigger_error ($fncn.' : File "'.$filePath.'" does not exist. You might find an example file in "'.$exampleFilepath.'".', E_USER_WARNING);
        return false;
    }
    if (!is_readable($filePath)) {
        trigger_error ($fncn.' : file "'.$filePath.'" is not readable.', E_USER_WARNING);
        return false;
    }

    $fc = file_get_contents($filePath);
    if (!is_string($fc)) {
        trigger_error ($fncn.' : file "'.$filePath.'" does not contain a string.', E_USER_WARNING);
        return false;
    };

    $jsonArr = json_decode ($fc, true);
    if (json_last_error()!==0) {
        trigger_error ($fncn.' : JSON decoding of file "'.$filePath.'" resulted in PHP error code '.json_last_error().', error = '.json_last_error_msg(), E_USER_WARNING);
        return false;
    }
    return $jsonArr;
}

function NicerAppWebOS_jsonErrorHandler() {
// maybe i don't need to write this at all, ever.
}

function recrypt ($username, $plaintextPassword, $newHash_couchDB) {
    $debug = false;
    $baseDir = realpath(dirname(__FILE__).'/..').'/NicerAppWebOS/apps';
    $files = @getFilePathList ($baseDir, true, '/^recrypt.php$/', null, array('file'));
    if ($debug) {
        echo '<pre>';
        var_dump ($files);
    };

    foreach ($files as $idx => $filepath) {
        $relPath = str_replace($baseDir.'/','',$filepath);
        $relPathParts = explode (DIRECTORY_SEPARATOR, $relPath);
        $manufacturerName = recryptFunctionNameCompatibility ($relPathParts[0]);
        $viewName = recryptFunctionNameCompatibility ($relPathParts[1]);

        $funcName = $manufacturerName.'__'.$viewName;
        require_once ($filepath);

        //var_dump ($funcName);
        if (function_exists($funcName)) call_user_func ($funcName, $username, $plaintextPassword, $newHash_couchDB);
    }

}

function recryptFunctionNameCompatibility ($val) {
    $ret = str_replace('.','_',$val);
    return $ret;
}
function negotiateOptions () {
	//print_r (debug_backtrace());
  $params = func_get_args ();

  $r = array();

  foreach ($params as $paramIdx => $param) {
		if ((array)$param!==$param) return badResult (E_USER_WARNING, array(
			'function' => '/code/sitewide_rv/php_expansion_packs.php::negotiateOptions',
			'msg' => 'Param with idx '.$paramIdx.' is not an array.',
			'$paramIdx' => $paramIdx,
			'$param' => $param
		));

		foreach ($param as $k=>$v) {

		  if (is_array($v)) {
			if (!array_key_exists($k,$r) || !is_array($r[$k])) $r[$k] = array();
			$r[$k] = negotiateOptions ($r[$k], $v);
		  } else {
			$r[$k] = $v;
		  }

		}
	}
    return $r;
}

function naWebOS_output_debug_parseColor($cmd, $colorname) {
    $intermediate = '{"color":"'.$colorname.'"}';
    $color = $cmd[$intermediate];
    //ob_end_clean(); echo $color; die();
    return $color;
}
function naWebOS_output_debug_command ($cmd=null) {
    if (is_null($cmd)) return false;

    $di = $cmd['di'];
    $classOuterDiv = $cmd['{"HTML_className":"naWebOS-debug-outer-DIV"}'];
    $classFieldName = $cmd['{"HTML_className":"naWebOS-field-name"}'];
    $classFieldValue = $cmd['{"HTML_className":"naWebOS-field-value"}'];
    $classLineRemaining = $cmd['{"HTML_className":"naWebOS-debug-lineRemaining"}'];
    $valueLinesRemaining = $cmd['{"value":"linesRemaining"}'];
    $colorHostnamectl = $cmd['{"color":"hostnamectl"}'];
    $colorTracerouteVersion = $cmd['{"color":"tracerouteVersion"}'];
    $colorTracerouteResults = $cmd['{"color":"tracerouteResults"}'];
    $colorTracerouteResultLine = $cmd['{"color":"tracerouteResultLine"}'];
    $selectedColor_outerDiv = $cmd['{"colorSelection_outerDiv":"determinedInUpwardFunction"}'];
    if (strpos($selectedColor_outerDiv,',')!==-1) {
        $p = explode(',',$selectedColor_outerDiv);
        $c = [];
        foreach ($p as $k=>$v) {
            $vp = explode(':',$v);
            if (is_int($vp[0])) $c[intval($vp[0])] = naWebOS_output_debug_parseColor($cmd, $v[1]);
            if ($vp[0]=='N') $c['N'] = naWebOS_output_debug_parseColor($cmd, $v[1]);

        }
    }
    $k1 = $di['matches'][1][0];
    $divID = $di['idx'];

    $r = '<div id="lineCommand_'.$divID.'" class="vdToolbar vividDialog '.$classOuterDiv.'" style="position:relative;display:inline-block;height:content-height;width:content-width;'.naWebOS_output_debug_parseColor($cmd, ($i>0?$c['N']:$c[$i])).'">';
    $r .= "\t".'<div class="vividDialogBackground1"></div>';
    $r .= "\t".'<div class="vividDialogContent vividScrollpane" style="overflow:visible;overflow-y:auto;">';
    $r .= "\t\t".'<h1 class="naWebOS-debug-line naWebOS-debug-line-key"><span class="'.$classFieldName.'">'.$k1.'></span></h1>';
    $r .= "\t\t".'<pre class="'.$classFieldValue.' naWebOS-value">'.json_encode($di, JSON_PRETTY_PRINT).'</pre><br/>';
    $r .= '</div></div>'.PHP_EOL.PHP_EOL;
    return $r;
}


function naWebOS_output_debug_info ($cmd=null) {
    if (is_null($cmd)) return false;

    $di = $cmd['di'];
    $classOuterDiv = $cmd['{"HTML_className":"naWebOS-debug-outer-DIV"}'];
    $classFieldName = $cmd['{"HTML_className":"naWebOS-field-name"}'];
    $classFieldValue = $cmd['{"HTML_className":"naWebOS-field-value"}'];
    $classLineRemaining = $cmd['{"HTML_className":"naWebOS-debug-lineRemaining"}'];
    $valueLinesRemaining = $cmd['{"value":"linesRemaining"}'];
    $colorHostnamectl = $cmd['{"color":"hostnamectl"}'];
    $colorTracerouteVersion = $cmd['{"color":"tracerouteVersion"}'];
    $colorTracerouteResults = $cmd['{"color":"tracerouteResults"}'];
    $colorTracerouteResultLine = $cmd['{"color":"tracerouteResultLine"}'];
    $selectedColor_outerDiv = $cmd['{"colorSelection_outerDiv":"determinedInUpwardFunction"}'];
    //echo $selectedColor_outerDiv.'<br/>';
    if (strpos($selectedColor_outerDiv,',')!==false) {
        $p = explode(',',$selectedColor_outerDiv);
        $c = [];
        foreach ($p as $k=>$v) {
            $vp = explode(':',$v);
            if (is_numeric($vp[0])) $c[intval($vp[0])] = naWebOS_output_debug_parseColor($cmd, $v[1]);
            if ($vp[0]=='N') $c['N'] = naWebOS_output_debug_parseColor($cmd, $v[1]);
        }
    }

    /*
    $keyCount = 0;
    $valueCount = 0;
    $params = array (
        'root' => $di,
        'webRoot' => $webRoot,
        'recursive' => true,
        'a' => &$di,
        'prevLevel' => 0,
        'keyCount' => &$keyCount,
        'valueCount' => &$valueCount,
    );
    $callKeyForValues = false;*/
    $k1 = $di['matches'][1][0];
    $divID = $di['idx'];
    $i = $di['idx'];
    if (!array_key_exists('idx2',$di)) $di['idx2'] = -1;
    $j = ++$di['idx2'];

    $r = '<div id="lineInfo_'.$i.'_'.$j.'" class="vdToolbar vividDialog '.$classOuterDiv.'" style="position:relative;display:inline-block;height:content-height;width:content-width;'.naWebOS_output_debug_parseColor($cmd, ($i>0?$c['N']:$c[$i])).'">';
    $r .= "\t".'<div class="vividDialogBackground1"></div>';
    $r .= "\t".'<div class="vividDialogContent vividScrollpane" style="overflow:visible;overflow-y:auto;">';
    $r .= "\t\t".'<h1 class="naWebOS-debug-line naWebOS-debug-line-key"><span class="'.$classFieldName.'">'.$k1.'></span></h1>';
    $r .= "\t\t".'<pre class="'.$classFieldValue.' naWebOS-value">'.json_encode($di, JSON_PRETTY_PRINT).'</pre><br/>';
    $r .= '</div></div>'.PHP_EOL.PHP_EOL;
    return $r;
}

function processDI_value ($cd) {
    $debugMe = false;

    $path = $cd['path'];
    $file = $cd['v'];

    global $naWebOS;
    $td = str_replace ($cd['params']['root'], '', $file);
    //echo '/var/www/'.$naWebOS->domainFolder; exit();
    $tdURL = str_replace('/var/www/'.$naWebOS->domainFolder,'',$cd['params']['root']).'/'.$cd['k'];
    if ($debugMe) {
        echo '<pre style="font-weight:bold;color:white;background:blue;margin:10px;margin-left:10px;padding:5px;border-radius:10px;">';
        var_dump ($cd['params']['root']);
        var_dump ($file);
        var_dump ($td);
        var_dump($cd['v']);
        //$tdURL = $cd['params']['root'].$td;
        var_dump($tdURL);
        echo '</pre>';
        //exit();
    }
    //$path2 = $cd['path'].'/'.$cd['k'];
    if ($cd['k']=='realPath') {
        $path2 = $cd['v'];
        $path2 = $cd['path'];//.'/'.$cd['k'];
        $path2 = substr($path2,1);

        if ($debugMe) echo '<pre style="font-weight:bold;color:yellow;background:red;margin-left:10px;padding:5px;border-radius:10px;">$path2:'.$path2.'</pre>'.PHP_EOL;
        /*$path2 = substr($path2,1);
        if ($debugMe) echo '$path2b:'.$path2.PHP_EOL;*/

        $ref = &chaseToPath ($cd['params']['a'], $path2);
        if ($debugMe) { echo '<pre style="font-weight:bold;color:white;background:lime;margin-left:10px;padding:5px;border-radius:10px;">'; var_dump ($ref); echo '</pre>'; }
        if (true || ($ref!==false && is_array($ref))) {
            $tdURL = &chaseToPath ($cd['params']['a'], $path2.'/webPath');
            global $rootPath_na;
            $tdURL = '/'.str_replace($rootPath_na,'',$tdURL);
            // JUST DON'T:
            //$passSubArrs = is_array($ref);
            //if ($passSubArrs) foreach ($ref as $k1 => $v1) if (is_array($v1)) $passSubArrs = true;
            if ($debugMe) {
                //echo '$passSubArrs:'; var_dump ($passSubArrs);
                echo '<p style="color:cyan;background:navy;margin-left:20;padding:5px;border-radius:10px">';
                var_dump ($tdURL);
                var_dump ($file);
                var_dump ($path2);
                var_dump ($cd['k']);
                echo '</p>';
                //var_dump ($cd['params']['a']);
                //exit();

            }
            if ($debugMe && is_string($ref)) {
                echo '<pre class="font-weight:bold;color:white;background:lime;margin-left:10px;padding:5px;border-radius:10px;">';
                var_dump ($ref);
                echo '</pre>';
            }
            //if ($cd['k']=='path') {
                $xec = 'identify "'.$file.'"';
                if (!$debugMe) {
                    exec ($xec, $output, $result);
                    //var_dump ($output); die();
                } else {
                    $result = 1;
                };
                if ($result===0) {
                    $regex = '/\s(\d+)x(\d+)\s/';
                    preg_match_all ($regex, $output[0], $m);
                    $wt = $m[1][0].'x'.$m[2][0];
                    if ($cd['params']['recursive']) {
                        $ref = $wt;//[ $td => $wt ];
                    } else {
                        $ref = [ $tdURL => $wt ];
                    }
                } else {
                    $wt = 'X*Y';
                    if (false && $cd['params']['recursive']) {
                        $ref = $wt;//[ $td => $wt ];
                    } else {
                        $ref = [ $tdURL => $wt ];
                    }
                }

                if ($debugMe) {
                    $dbg = [
                        'path' => $path,
                        'tdURL' => $tdURL,
                        'file' => $file,
                        'ref' => $ref
                    ];
                    var_dump ($dbg);
                    //echo '</pre>';
                }
            //}
        }
    }
    echo '</pre>';
}

function getMusicPlayerContentFiles ($root, $webRoot, $recursive=true) {
    $keyCount = 0;
    $valueCount = 0;
    $params = array (
        'root' => $root,
        'webRoot' => $webRoot,
        'recursive' => $recursive,
        'a' => &$files,
        'prevLevel  ' => 0,
        'keyCount' => &$keyCount,
        'valueCount' => &$valueCount,
    );
    $callKeyForValues = false;
    walkArray ( $files, /*'processBackgroundFile_key'*/null, 'processBackgroundFile_value', $callKeyForValues, $params );
    return $files;
}
/*
function processBackgroundFile_key ($cd) {
    $path = $cd['path'].'/'.$cd['k'];
}
function processBackgroundFile_value ($cd) {
    $debugMe = false;
    if ($debugMe) echo '<pre style="color:white;background:green;margin-left:10px;margin:10px;border-radius:10px;padding:5px;">';

    $path = $cd['path'];
    $file = $cd['v'];

    global $naWebOS;
    $td = str_replace ($cd['params']['root'], '', $file);
    $tdURL = str_replace('/var/www/'.$naWebOS->domainFolder,'',$cd['params']['root']).'/'.$cd['k'];
    if ($debugMe) {
        echo '<pre style="font-weight:bold;color:white;background:blue;margin:10px;margin-left:10px;padding:5px;border-radius:10px;">';
        var_dump ($cd['params']['root']);
        var_dump ($file);
        var_dump ($td);
        var_dump($cd['v']);
        var_dump($tdURL);
        echo '</pre>';
        //exit();
    }
    //$path2 = $cd['path'].'/'.$cd['k'];
    if ($cd['k']=='realPath') {
        $path2 = $cd['v'];
        $path2 = $cd['path'];//.'/'.$cd['k'];
        $path2 = substr($path2,1);

        if ($debugMe) echo '<pre style="font-weight:bold;color:yellow;background:red;margin-left:10px;padding:5px;border-radius:10px;">$path2:'.$path2.'</pre>'.PHP_EOL;
        /*$path2 = substr($path2,1);
        if ($debugMe) echo '$path2b:'.$path2.PHP_EOL;* /

        $ref = &chaseToPath ($cd['params']['a'], $path2);
        if ($debugMe) { echo '<pre style="font-weight:bold;color:white;background:lime;margin-left:10px;padding:5px;border-radius:10px;">'; var_dump ($ref); echo '</pre>'; }
        if (true || ($ref!==false && is_array($ref))) {
            $tdURL = &chaseToPath ($cd['params']['a'], $path2.'/webPath');
            // JUST DON'T:
            //$passSubArrs = is_array($ref);
            //if ($passSubArrs) foreach ($ref as $k1 => $v1) if (is_array($v1)) $passSubArrs = true;
            if ($debugMe) {
                //echo '$passSubArrs:'; var_dump ($passSubArrs);
                echo '<p style="color:cyan;background:navy;margin-left:20;padding:5px;border-radius:10px">';
                var_dump ($tdURL);
                var_dump ($file);
                var_dump ($path2);
                var_dump ($cd['k']);
                echo '</p>';
                //var_dump ($cd['params']['a']);
                //exit();

            }
            if ($debugMe && is_string($ref)) {
                echo '<pre class="font-weight:bold;color:white;background:lime;margin-left:10px;padding:5px;border-radius:10px;">';
                var_dump ($ref);
                echo '</pre>';
            }
            //if ($cd['k']=='path') {
                $xec = 'identify "'.$file.'"';
                exec ($xec, $output, $result);
                if ($result===0) {
                    $regex = '/\s(\d+)x(\d+)\s/';
                    preg_match_all ($regex, $output[0], $m);
                    $wt = $m[1][0].'x'.$m[2][0];
                    if ($cd['params']['recursive']) {
                        $ref = $wt;//[ $td => $wt ];
                    } else {
                        $ref = [ $tdURL => $wt ];
                    }
                } else {
                    $wt = 'X*Y';
                    if (false && $cd['params']['recursive']) {
                        $ref = $wt;//[ $td => $wt ];
                    } else {
                        $ref = [ $tdURL => $wt ];
                    }
                }

                if ($debugMe) {
                    $dbg = [
                        'path' => $path,
                        'tdURL' => $tdURL,
                        'file' => $file,
                        'ref' => $ref
                    ];
                    var_dump ($dbg);
                    //echo '</pre>';
                }
            //}
        }
    }
    echo '</pre>';
}
*/




?>
