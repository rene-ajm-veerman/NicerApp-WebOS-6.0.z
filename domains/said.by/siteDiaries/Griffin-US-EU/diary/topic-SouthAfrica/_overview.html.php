<?php
    global $naWebOS;
    require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');
    $fp = $naWebOS->codePath.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.diaries.php';
    require_once ($fp);
    $diaries = new naDiaries();
?>
        <div class="naDiaryEntry naDiaryMoodNoteInfo">
                <h2 class="naDiaryEntryHeader naDiaryMoodNoteInfoHeader">Overview</h2>
        </div>
