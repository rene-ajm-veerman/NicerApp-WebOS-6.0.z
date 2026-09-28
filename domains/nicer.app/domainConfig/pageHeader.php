<?php
    global $naWebOS;
    require_once ($naWebOS->codePath.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/mainmenu.items.php');
    global $naURLs; // from .../domainConfig/nicer.app/mainmenu.items.php
    global $na_apps_structure;
    if (false) {
        echo '<pre style="color:blue;">';
        var_dump ($na_apps_structure);
        echo '</pre><pre style="color:purple">';
        var_dump ($naURLs);
        echo '</pre>'; exit();
    }
?>
    <style>
        #companyLogosAndName {
            width:100%;
            display:flex;
            justify-content:left;
            align-items:center;
        }
        .divFor_neCompanyLogo {
            display:flex;
            flex-direction:column;
            justify-content:center;
            color:white;
            border-radius:20px;
            border:solid rgba(0,0,0,0.8);
            background:rgba(0,0,50,0.555);
            box-shadow:0px 0px 2px 1px rgba(0,0,0,0.55), 0px 0px 5px 2px rgba(0,0,0,0.8);
        }
        .divFor_neCompanyLogo video {
            margin : 10px;
            border-radius:20px;
        }
    </style>
    <div id="companyLogosAndName" class="container">
        <table style="width:fit-content">
            <tr>
                        <!--
                <td>
                    <div class="divFor_neCompanyLogo" style="width:500px;height:700px;background:none;border:none;box-shadow:none;">
                        <video controls autoplay loop playsinline style="width:fit-content">
                            <source src="/NicerAppWebOS/businessLogos/text-to-video-AI/raw-oxpecker-v5.11/stitched.mp4" type="video/mp4"></source>
                            Your browser does not support video.
                        </video>
                        <iframe src="https://nicer.app/NicerAppWebOS/businessLogos/models/angel_brass_version.html" defer style="width:500px;height:700px;background:none;border:none;"></iframe>
                    </div>
                </td>
                        -->
                <td rowspan="2">
                    <div>
                        <div><h1 class="contentSectionTitle1" alt="Nicer.app WebOS homepage">Nicer.app WebOS homepage</h1></div>
                    </div>
                </td>
            </tr>
        </table>
        <table style="width:100%;margin:30px;">
        <tr>
        <td rowspan="2" align="right">
        <div style="font-size:x-large;font-weight:bold;text-shadow:0px 0px 4px black, 2px 2px 8px black">
            <div><p class="backdropped"><span style="font-size:x-large;font-weight:bold">© <!--can't claim this when using an MIT license : and ®--> 2026 <a href="https://zoned.at/d3" class="tooltip" title="Diary part 3">René</a> AJM Veerman</span> [<a href="mailto:rene.veerman.netherlands@gmail.com">rene.veerman.netherlands@gmail.com</a>],<br/>also known as <a href="https://www.usmessageboard.com/search/1852780/?c[users]=GavanPeacefan&o=date" class="nomod noPushState tooltip" title="Geopolitical diary" target="usmessageboardDotComSlashGavanPeacefan">Gavan Peacefan Grokman Veers</a>.<br/>Current status : Having fixed the theme editor, <br/>i'm now continuing work on the installation procedures at <a href="new.nicer.app" class="nomod noPushState" target="nna">new.nicer.app</a>.</p></div>

            <div><p class="backdropped" alt="Original idea by unknown and/or anonymous humans">Original idea by unknown and/or anonymous human(s) ;-)</p></div>
        </div>
        </td>
        </tr>
        </table>

    </div>

    <div class="linkContainer">
        <a href="/ " class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Front page</span></h3></a>
        <a href="<?php echo $naURLs['docs__overview'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Documentation</span></h3></a>
        <a href="<?php echo $naURLs['docs__license'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">License</span></h3></a>
        <a href="<?php echo $naURLs['docs__todoList'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">To-Do List</span></h3></a>
        <a href="/company" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Company</span></h3></a>
    </div>
    <script type="text/javascript">
        na.site.settings.loadingApps = false;
        na.site.settings.startingApps = false;
    </script>
