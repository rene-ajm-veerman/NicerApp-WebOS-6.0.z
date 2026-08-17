<?php
    global $naWebOS;
    require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');
    $fp = $naWebOS->codePath.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.diaries.php';
    require_once ($fp);
    $diaries = new naDiaries();
?>
        <div class="naDiaryEntry naDiaryMoodNoteInfo">
                <h2 class="naDiaryEntryHeader naDiaryMoodNoteInfoHeader">Overview</h2>

                <p><a href="https://maps.app.goo.gl/5kyGi6YsxhWtP39z7" class="nomod noPushState" target="gmapsLithuania">Google Maps</a></p>
                <?=naPhotoAlbum(['mediaFolder'=>'/Users/GUSEU/Media Albums/Lithuania']);?>

                <ol>
                        <li><a href="https://nicer.app/wiki-search/frontpage/title/family?family=wikipedia&search=kaliningrad+world+war+2&language=en&go=Go" class="nomod noPushState" target="naWikiKaliningradWW2">Kaliningrad during WW2 (wikipedia.org)</a>.</li>
                </ol>

                <h2 class="naDiaryEntryHeader naDiaryMoodNoteInfoHeader">Scenario 1 - Retreat</h2>
                <p>Ideally, the illegitimate opportunistic, sneaky and stubborn expansion of NATO into far eastern Europe should be somewhat reversed by NATO voluntarily. To do so, the roads and railroads towards Kaliningrad city should be handed over to Moscow north of either <a href="https://maps.app.goo.gl/5Q8ZQnEejEGjoVkc6" class="nomod noPushState" target="gmapsAlytus">Alytus, Lithuania</a>, or Vilnius, Lithuania.</p>

                <h2 class="naDiaryEntryHeader naDiaryMoodNoteInfoHeader">Scenario 2 - Fight</h2>
                <p>This would involve the (eventual) annexxation of Kaliningrad oblast (so the entire "province").</p>
        </div>
