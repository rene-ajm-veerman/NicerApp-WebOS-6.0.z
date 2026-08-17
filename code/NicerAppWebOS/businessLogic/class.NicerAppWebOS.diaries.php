<?php

class naDiaries {
    public function getDiary ($diaryName='siteOwner', $dp = null, $printPage = '/NicerAppWebOS/documentation/NicerApp--company-print.php') {
        global $naWebOS;
        $first1 = true;
        $rp = realpath(dirname(__FILE__).'/../siteData');
        //var_dump($rp); echo '<br/>';
        echo '<pre>'; var_dump($dp); echo '<br/>'; var_dump (file_exists($dp)); echo '</pre>';
        if (is_null($dp)) $dp = str_replace('/domainConfig', '', $naWebOS->domainPath).'/Diaries/'.$diaryName.'/';
        echo '<pre>'; var_dump($dp); echo '<br/>'; var_dump (file_exists($dp)); echo '</pre>';
        $files = getFilePathList ($dp, true, '/.*/', null, ['file'], null, 1, false, false);
        //echo '<pre>'; var_dump($files); echo '</pre>'; //die();
        $files2 = [];
        foreach ($files as $fIdx => $fRec) {
            $files2[] = '/'.$fRec['realPath'];
        }
        //echo '<pre>'; var_dump($files2); echo '</pre>'; //die();
        $files = $files2;
        sort ($files);
        //echo '<pre>'; var_dump($files); echo '</pre>'; die();
        echo '<div class="naDiaryWebPage">';
        echo '<span onclick="$(\'.naFilePath,ol,ul,.naDiaryEntry,.naDiaryDay,.naDiaryDaySegment\', $(\'.naDiaryWebPage\')).show(\'slow\');" style="margin-top:10px;color : white; text-shadow : 0px 0px 4px rgba(0,255,255,0.5), 2px 2px 3px rgba(0,0,0,0.751);background:rgba(0,0,50,0.7);border:1px solid white;box-shadow:2px 2px 3px 1px rgba(0,0,0,0.55);margin-top:10px;padding:5px;border-radius:15px;">expand all</span>';
        echo '<span style="margin-left:10px;margin-top:10px;color : white; text-shadow : 0px 0px 4px rgba(0,255,255,0.5), 2px 2px 3px rgba(0,0,0,0.751); margin-top:10px;background:rgba(0,0,50,0.7);border:1px solid white;box-shadow:2px 2px 3px 1px rgba(0,0,0,0.55);padding:5px;border-radius:15px;"><a href="'.$printPage.'" class="nomod noPushState" target="naCompanyDiary-printPage">print</a></span>';
        foreach ($files as $fidx => $fp) {

            if (basename($fp)==='-dayTitle.html.php') {
                //echo '<div class="naDiaryDay">';
                if (strpos($fp,'.archived')===false) {
                    echo require_return ($fp);
                }
                //echo '</div>';
            } else {
                if (strpos($fp,'.archived')===false) {
                    if (false && basename($fp)!=='-dayTitle.html.php') {
                        echo '<div class="naFilePath">'; echo $fp; echo '</div>';
                    }
                    if (strpos($fp,'html.php')!==false) echo require_return ($fp);
                }
            }
        }
        echo '</div>';

    }
}

?>
