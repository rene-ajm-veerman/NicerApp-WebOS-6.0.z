<?php
    global $naWebOS;
    require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');
    $fp = $naWebOS->codePath.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.diaries.php';
    require_once ($fp);
    $diaries = new naDiaries();
?>
        <div class="naDiaryEntry naDiaryMoodNoteInfo">
                <h2 class="naDiaryEntryHeader naDiaryMoodNoteInfoHeader"><a class="nomod noPushState" target="googleMapsTaiwan" href="https://maps.app.goo.gl/qnfbN8Bz3Zjyayzf9">Maps</a></h2>
                <?=naPhotoAlbum(['mediaFolder'=>'/Users/GUSEU/Media Albums/Taiwan']);?>
        </div>
