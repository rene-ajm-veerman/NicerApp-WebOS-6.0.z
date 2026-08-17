<?php

global $wo__ignoreList__filesystem_rootPath; // set in .../globals.php


foreach ($wo__ignoreList__filesystem_rootPath as $idx => $rootPath) {
    if (
    !is_string($rootPath)
    || !file_exists($rootPath)
    ) {
    $err = array (
        'msg' => 'invalid $wo__ignoreList__filesystem_rootPath;',
        '$wo__ignoreList__filesystem_rootPath' => $wo__ignoreList__filesystem_rootPath,
        'file_exists($wo__ignoreList__filesystem_rootPath)' => file_exists($wo__ignoreList__filesystem_rootPath)
    );
    badResult (E_USER_ERROR);
    } else {
    $what = array ('dir','file');
    $files = getFilePathList ($rootPath, true, '/.*/', $what);
    
    $debugTokensFilesystem = false;
    if ($debugTokensFilesystem) { echo 'token.whitelist.filesystem.php:::$files=<pre>'; var_dump ($files); echo '</pre>'; }
    
    $files2 = array();
    foreach ($files as $idx=>$filepath) {
        $files1 = array();
        if (strpos($filepath,'/')!==false) $files1 = explode ('/', $filepath);
        if (strpos($filepath,'\\')!==false) $files1 = explode ('\\', $filepath);
        foreach ($files1 as $idx2 => $part) {
        if ($part!=='' && array_search($part, $files2)===false) {
            $files2[] = $part;
        }
        }
    }
    if ($debugTokensFilesystem) { echo 'token.whitelist.filesystem.php:::$files2=<pre>'; var_dump ($files2); echo '</pre>'; }
    
    $tokensFilesystem = array();
    foreach ($files2 as $idx=>$part) {
        $files3 = explode ('.', $part);
        foreach ($files3 as $idx3 => $part2) {
        if ($part2!=='' && array_search($part2, $tokensFilesystem)===false) {
            $tokensFilesystem[] = $part2;
        }
        }
        
    }
    
    $wo__ignoreList__filesystem = array_unique($tokensFilesystem);
    if ($debugTokensFilesystem)  { echo 'token.whitelist.filesystem.php:::$wo__ignoreList__filesystem=<pre>'; var_dump ($wo__ignoreList__filesystem); echo '</pre>'; }
    global $wo__ignoreList__filesystem;
    }
}
if ($debugTokensFilesystem) die();

?>