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
        <table style="width:100%">
        <tr>
        <td rowspan="2" align="right">
        <div>
        <div><p class="backdropped" alt="(C) and (R) 2026 Rene AJM Veerman [rene.veerman.netherlands@gmail.com]">(C) and (R) 2026 <a href="https://zoned.at/d3">Rene</a> AJM Veerman [<a href="mailto:rene.veerman.netherlands@gmail.com">rene.veerman.netherlands@gmail.com</a>].</p></div>
        <div><p class="backdropped" alt="Founded in 2002 by Rene AJM Veerman [rene.veerman.netherlands@gmail.com]">Founded in 2002 by Rene AJM Veerman.</p></div>
        <div><p class="backdropped" alt="Founded in 2002 by Rene AJM Veerman [rene.veerman.netherlands@gmail.com]">Original idea by unknown and/or anonymous human(s) ;-)<br/>For which I plan to donate to charities when I finally can again.</p></div>
        </div>
        </td>
        <td align="right" style="box-shadow:3px 3px 5px 3px rgba(0,0,0,0.7);padding:10px;border-radius:5px;background:rgba(0,0,50,0.4);border:1px solid grey;width:220px;padding-right:10px;font-size:small;"><img src="/NicerAppWebOS/documentation/selfies/rene-ajm-veerman/Rene_at_Airlie_Beach_Australia_2004.jpg" style="box-shadow:3px 3px 5px 3px rgba(0,0,0,0.7);width:300px;padding:10px;border-radius:5px;background:rgba(0,0,50,0.4);border:1px solid grey;"><br/>Pictured in 2004 in Airlie Beach, Australia.<br/>And folks, i'm <em>straight</em>.</td>
        </tr>
        </table>

    </div>

    <div class="linkContainer">
        <a href="/ " class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Front page</span></h3></a>
        <a href="<?php echo $naURLs['docs__overview'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Documentation</span></h3></a>
        <a href="<?php echo $naURLs['docs__license'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">License</span></h3></a>
        <a href="<?php echo $naURLs['docs__todoList'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">To-Do List</span></h3></a>
        <a href="/company" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Company</span></h3></a>
        <?php
            global $naLAN;
            if (false && $naLAN) {
        ?>
                <a href="/memoires" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Owners memoires</span></h3></a>
                <a href="/geopolitics" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Geopolitics</span></h3></a>
        <?php
            }
        ?>
    </div>
    <script type="text/javascript">
        na.site.settings.loadingApps = false;
        na.site.settings.startingApps = false;
    </script>
