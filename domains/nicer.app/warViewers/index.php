<pre>
i don't want people popularizing these warsimulators.
and i sure don't want young kids to think war is fun.

<p><a href="https://grok.com/share/c2hhcmQtMw_f8493dd7-ab1c-4a70-9bc3-104c832ad437">See this Grok.com conversation</a> as well please.</p>

(C) 2026 Rene AJM Veerman, rene.veerman.netherlands@gmail.com
license : https://opensource.org/license/mit

<?php
 require_once (__DIR__.'/../NicerAppWebOS/boot.php');
 $root = __DIR__;
 $files = getFilePathList ($root, true, '/.*.html$/', null, ['file']);
 usort($files,function($a,$b){
     return strcmp($a['path'],$b['path']);
 });
 foreach ($files as $i=>$f) {
     if (
         strpos($f['path'],'.html')!==false
         || strpos($f['path'],'.php')!==false
     ) {
         echo '<p><a href="/warViewers/'.$f['webPath'].'">/warViewers/'.$f['webPath'].'</a></p>';
    }
}
?>
</pre>
