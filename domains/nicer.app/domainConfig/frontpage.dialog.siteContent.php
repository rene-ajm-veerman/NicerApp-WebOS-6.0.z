                                                                                                                                                                                                                                                                <?php
    require_once(dirname(__FILE__).'/../NicerAppWebOS/boot.php');
    global $naWebOS;
    global $naURLs;
    require_once(dirname(__FILE__).'/../NicerAppWebOS/businessLogic/vividUserInterface/v5.y.z/photoAlbum/4.0.0/functions.php');
    //$src = $naWebOS->codePath.'/../../domains/'.$naWebOS->domainFolder.'/domainConfig/pageHeader.php'; echo $src; var_dump(file_exists($src)); exit();
    require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');
?>
    <script type="text/javascript">
        //delete na.site.settings.current.app;
        //na.m.waitForCondition ('frontPage.dialog.siteContent.php : desktopIdle?', na.m.desktopIdle, function() {
            //na.d.s.visibleDivs.push('#siteToolbarLeft');
            //na.d.s.visibleDivs.push('#siteToolbarRight');
            //na.d.s.visibleDivs.push('#siteToolbarTop');
            //na.desktop.resize();
       // }, 100);
       na.site.globals.app = {
           "/" : { "page" : "index" }
       };
        $(document).ready(function() {
            na.d.s.visibleDivs = arrayRemove (na.d.s.visibleDivs, '#siteToolbarLeft');
            na.d.s.visibleDivs = arrayRemove (na.d.s.visibleDivs, '#siteToolbarRight');
            na.d.s.visibleDivs = arrayRemove (na.d.s.visibleDivs, '#siteToolbarThemeEditor');
            na.d.s.visibleDivs = arrayRemove (na.d.s.visibleDivs, '#siteToolbarTop');
        });
    </script>

    <div style="height:2000px">
    <div class="naFrontpage_headerText naFrontpage_headerText_intro" style="float:left">
        <p>
        Discover an open-source CMS and WebOS that lets you create stunning apps over dynamic backgrounds.
        Perfect for all ages to learn, play, and innovate with!<br/>
        Project status : <span id="siteLastModified"></span>.
        </p>
        <p style="padding:8px;border-radius:5px;margin-block-start:0;margin-block-end:0;margin-inline-start:0;margin-inline-end:0;">
        <!--Opensourced as <a href="https://github.com/rene-ajm-veerman/nicerApp-WebOS-6.0.z" class="nomod noPushState" target="naGithub">v6.0.z on Github.com</a>,
        and -->Available as <a href="https://nicer.app/downloads" class="nomod noPushState" target="naDownload">full package here</a>, <br/>
        </p>
        <p style="font-weight:bold">14 Aug 2026 : Today : source tree cleanup and release</p>

        <p><a href="https://said.by/view/eyJcL05pY2VyQXBwV2ViT1NcL2FwcHNcL05pY2VyQXBwV2ViT1NcL2NvbnRlbnQtbWFuYWdlbWVudC1zeXN0ZW1zXC9OaWNlckFwcFdlYk9TIjp7ImNtc1ZpZXdNZWRpYSI6eyJjb2RlUGF0aCI6IlwvdmFyXC93d3dcL25pY2VyLmFwcC01LjEwLnpcL2RvbWFpbnNcL3NhaWQuYnlcL3NpdGVEYXRhXC9zYWlkLmJ5XC9Vc2Vyc1wvUmVuZSBBSk0gVmVlcm1hblwvTWVkaWEgQWxidW1zXC9OZXciLCJmaWxlbmFtZSI6IlNjcmVlbnNob3RfMjAyNjA2MTJfMjAxMjQwLnBuZyJ9fX0" class="nomod noPushState" target="naScreenshots-20260623-1016CET-AMS">Screenshot</a> <a href="https://said.by/Rene-AJM-Veerman/about/NicerApp" class="nomod noPushState" target="naScreenshots-collection">collection</a> of <a class="nomod noPushState" target="naDiary-20260623-1008CET-AMS" href="https://nicer.app/view/eyJhaWQiOjAsImZkcyI6NTcxMjAwLCJycCI6IlwvMjAyNiBCZXN0XC8ifQ?idxStart=0&pw=alwaysXMASzzz">this</a>.</p>
        <p style="padding:8px;border-radius:5px;margin-block-start:0;margin-block-end:0;margin-inline-start:0;margin-inline-end:0;">
        Bug-reports as well as legal inquiries may be sent to <a href="mailto:rene.veerman.netherlands@gmail.com">rene.veerman.netherlands@gmail.com</a>.
        </p>
    </div>

    <div class="naFrontpage_headerText naFrontpage_headerText_intro" style="float:left">
    <p>
    I've been thinking on how to prevent over-usage of my system, and the answer is a side-module, another human+AI-written statistical algorithm, to compute graphdata of daily usage hours (measured in seconds) per user/IP-address, per app, per NicerApp domain, results also stored in database for quick retrieval upon display or decision-time.
    </p>
    </div>


    <div class="naFrontpage_headerText naFrontpage_headerText_recentAchievement" style="float:left">
        <p class="backdropped" style="color:lime">
            2026-Aug-2nd, 21:58CEST<br/>
        </p>
        <p class="backdropped">
        I accept donations for these services via
        </p>
        <!--
        -->
<pre class="backdropped">
RAJM Veerman
NL30INGB0007689155
</pre>


<p class="backdropped" style="color:lime">
2026-Aug-10th, 09:44CEST<br/>
</p>
<p class="backdropped">All donations and expenses will be handled in a tax-compliant way.<br/>I'm allowed to make about two-thousand Euros extra per year.</p>
        </div>

    <div class="naFrontpage_headerText naFrontpage_headerText_recentAchievement" style="float:left">
    <p class="backdropped" style="color:lime">
    2026-Aug-10th, 19:25CEST<br/>
    </p>
    <p class="backdropped">I'm allowed to inquire about laser eye surgery, which gets rid of the need for contact lenses and glasses entirely, which is something that I badly need for medical and practical reasons.</p>
    </div>


    <div class="naFrontpage_headerText naFrontpage_headerText_recentAchievement" style="float:left">
    <p class="backdropped" style="color:lime">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/gvnsP1byW7U?si=wSt6jQ1dQQwjyjJu" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
    </p>
    </div>

    <div class="naFrontpage_headerText naFrontpage_headerText_maintenance" style="float:right">
    <p class="backdropped">
    2026-Aug-15th, 14:48CEST
    </p>
    <p class="backdropped">
    I am migrating away from github, well, at least until they start to show the exact commands needed for linking up multiple repositories inside a single published repository on github.com and in a, or the, kubuntu git binary.
    </p>
    <p class="backdropped">It sure looks like I'm not getting back on to x.com (legal problems related to 'authenticity' without further specification into offending post urls/links..) And I already could not get back onto facebook.com; so for my latest diary entries look no further than my '<a href="https://said.by/Rene-AJM-Veerman/in/hisDiary" class="nomod noPushState" target="RVsDiary">hisDiary link</a>' :-)</p>
    </div>

    <div class="naFrontpage_headerText naFrontpage_headerText_maintenance" style="float:right">
    <p class="backdropped">
    2026-Aug-2nd, 09:40CEST
    </p>
    <p class="backdropped">
    These webservers are in the process of undergoing a major version upgrade of the database business logic code as well as other base layers. This upgrade is now nearly complete.
    </p>
    <p class="backdropped">
    Services may go down temporarily on any of my servers for seemingly unexplainable reasons over the next few weeks.
    </p>
    <p class="backdropped">
    In addition, the PHP database backup-to-filesystem scripts are scheduled to get coded within the next few weeks, and put on 6-hour intervals of execution into folders which automatically get backed up whenever i update my sourcecode backups.
    </p>
    </div>

    <div class="naFrontpage_headerText naFrontpage_headerText_maintenance" style="float:right">
    <p class="backdropped">
    2026-Aug-8th, 15:36CEST
    </p>
    <p class="backdropped">
    During 2027, I plan to create and release feature upgrades that will be known as 'phpTasksManager' and 'phpRegionalCloud'.
    </p>
    <p class="backdropped">
    Services may go down temporarily on any of my servers for seemingly unexplainable reasons during that year too.
    </p>
    </div>

    </div>

    <h1 class="contentSectionTitle1">Accreditions</h1>

    <p class="backdropped"><a href="https://afraid.org">Afraid.org FREE Domain Name Server services</a>.</p>

    <p class="backdropped"><a href="https://youtube.com">Youtube.com FREE public videoclips service</a>.</p>

    <h1 class="contentSectionTitle1">Accreditions : AIs</h1>

    <p class="backdropped"><a href="https://grok.com">Grok</a>.</p>

    <p class="backdropped"><a href="https://google.com">Gemini</a>.</p>

    <p class="backdropped"><a href="https://claude.ai/chat">Claude</a>.</p>

    <h1 class="contentSectionTitle1">Free apps offered :</h1>

    <p class="backdropped">Yields calculated in real time now for <a href="https://nicer.app/fusion/Grok-by-xai-and-Elon-Musk/tokamak-yields-v2.1.1.html" class="nomod noPushState" target="groksTokamak">nuclear fusion power plant designs</a>! With <a href="https://grok.com/share/c2hhcmQtMw_f2621998-1b7b-4d6f-81e0-993a6de5524e" class="nomod noPushState" target="grokFusionPowerPlantsDiscussion">assistance</a> by <a href="https://grok.com" class="nomod noPushState" target="grokHomepage">Grok.com</a> from <a href="https://x.ai" class="nomod noPushState" target="xaiHomepage">x.ai</a> (see also <a href="https://x.com/xai" class="nomod noPushState" target="xaiHomepageOnXdotCom">x.com/xai</a>).<br/>(C) + (R) 2026 by Rene AJM Veerman, building upon general progress in the science and engineering fields. I do not want to cheat anyone out of their legitimate own paychecks though! :-)</p>

    <h1 class="contentSectionTitle1"><a href="https://said.by" class="contentSectionTitle3_a nomod noPushState" target="_new">Social Media features</a></h1>

    <h1 id="h3_news" class="contentSectionTitle1">News</h1>
    <ul class="index" style="margin-block-end:33px;">
        <li><a href="<?php echo $naURLs['newsHeadlines_englishNews'];?>" class="contentSectionTitle3_a">English News</a></li>
        <li><a href="<?php echo $naURLs['newsHeadlines_englishNews_worldHeadlines'];?>" class="contentSectionTitle3_a">English News : World Headlines only</a></li>
        <li><a href="/business-news" class="contentSectionTitle3_a">English Business News</a></li>
        <li><a href="<?php echo $naURLs['newsHeadlines_nederlandsNieuws'];?>" class="contentSectionTitle3_a">Nederlands Nieuws</a></li>
        <li><a href="<?php echo $naURLs['newsHeadlines_nederlandsNieuws_wereldNieuws'];?>" class="contentSectionTitle3_a">Nederlands Nieuws : Internationale Headlines</a></li>
        <li><a href="/actualit%C3%A9s-fran%C3%A7aises" class="contentSectionTitle3_a">Actualités françaises</a></li>
        <li><a href="<?php echo $naURLs['newsHeadlines_deutscheNachrichten'];?>" class="contentSectionTitle3_a">Deutsche Nachrichten</a></li>
        <li><a href="<?php echo $naURLs['newsHeadlines_arabic'];?>" class="contentSectionTitle3_a">Arabic Business News (in English)</a></li>
    </ul>
    
    <h1 class="contentSectionTitle1">Encyclopedias</h1>
    <ul>
        <li><a href="/wiki/frontpage" class="contentSectionTitle3_a">Wikipedia.org</a></li>
        <li><a href="/grokipedia" class="contentSectionTitle3_a">Grokipedia</a></li>
    </ul>

    <h3 class="contentSectionTitle3"><a href="https://zoned.at" target="zonedAt" class="contentSectionTitle3_a">URL redirection (on https://zoned.at)</a></h3>

    <?php
    global $naLAN;
    if (true || $naLAN) {
    ?>
        <h3 class="contentSectionTitle3"><a href="/logs" class="contentSectionTitle3_a">View logs</a></h3>
    <?php
    }
    ?>

    <h1 class="contentSectionTitle1">Tarot cardgame</h1 >
    <?=naTarotDecksAlbum();?>

