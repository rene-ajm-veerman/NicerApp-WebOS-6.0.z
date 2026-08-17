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
            width:98%;
            display:flex;
            justify-content:left;
            align-items:center;
        }
        .divFor_neCompanyLogo {
            display:flex;
            flex-direction:column;
            justify-content:center;
            width:100%;
            color:white;
            margin-left:5px;
            margin-bottom:20px;
            border-radius:10px;
            border:solid rgba(0,0,0,0.8);
            background:rgba(0,0,50,0.555);
            padding:5px;
            box-shadow:0px 0px 2px 1px rgba(0,0,0,0.55);
        }
        .divFor_neCompanyLogo video {
            width : 100%;
            border-radius:12px;
            box-shadow:0 8px 20px rgba(0,0,0,0.3);
        }
        @media (any-pointer: coarse) {
            body {
                font-size : x-large;
            }
            .divFor_neCompanyLogo {
            }
        }
        @media (any-pointer: fine) {
            body {
                font-size : x-large;
            }
            .divFor_neCompanyLogo {
            }

        }
    </style>
    <div id="companyLogosAndName" class="container">
        <table style="width:100%">
            <tr>
                <td>
                    <div class="divFor_neCompanyLogo">
                        <video
                            controls
                            autoplay
                            loop
                            playsinline
                            poster="/NicerAppWebOS/businessLogo/said.by-logo-v1.0.0-male-twirler-2.0.0.png">

                            <source src="/NicerAppWebOS/businessLogo/2025-11-03--AI-text-to-video--SUCKS--rationingWAYtooSTRICT/said.by-logo-v1.0.0-male-twirler.mp4" type="video/mp4">

                            Your browser does not support video.
                        </video>
                        <p class="backdropped">Logo <a href="/NicerAppWebOS/businessLogo/said.by-logo-v1.0.0-male-twirler-2.0.0.txt" class="nomod noPushState" target="saidByLogoCreationStory">made</a> with the aid of <a href="https://x.com/ReneVeerman1977/status/1985356657098973657" class="nomod noPushState" target="grokDotComChatLogAtXdotCom">@grok at X.com</a> and <a href="https://runwayml.com" target="runwaymlDotCom" class="nomod noPushState">RunWayML.com</a> AI.</p>
                    </div>
                </td>
                <td>
                    <div class="divFor_neCompanyLogo">
                        <video controls autoplay loop playsinline>
                            <source src="/NicerAppWebOS/businessLogo/2025-11-03--AI-text-to-video--SUCKS--rationingWAYtooSTRICT/said.by-logo-v1.0.0-male-twirler-3.0.0-dream-machine.lumalabs.ai.mp4" type="video/mp4">

                            Your browser does not support video.
                        </video>
                        <p class="backdropped">Logo <a href="/NicerAppWebOS/businessLogo/2025-11-03--AI-text-to-video--SUCKS--rationingWAYtooSTRICT/said.by-logo-v1.0.0-male-twirler-3.0.0.txt" class="nomod noPushState" target="saidByLogoCreationStory">made</a> with the aid of <a href="https://x.com/ReneVeerman1977/status/1985356657098973657" class="nomod noPushState" target="grokDotComChatLogAtXdotCom">@grok at X.com</a> and <a href="https://dream-machine.lumalabs.ai" target="runwaymlDotCom" class="nomod noPushState">Luma Dream Machine AI</a>.</p>
                    </div>
                </td>
                <td rowspan="2">
                    <div>
                        <div style="margin-left:30px;"><h1 class="contentSectionTitle1" title="Said.by Social Media">Said.by <br/>Social Media</h1></div>
                    </div>
                    <div>
                        <div style="margin-left:30px;"><h2 class="contentSectionTitle2" title="a Nicer.App product">A <a href="https://nicer.app" class="nomod noPushState" target="nicerDotApp">nicer.app product</a>.</h1></div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="divFor_neCompanyLogo">
                        <video controls autoplay loop playsinline>
                            <source src="/NicerAppWebOS/businessLogo/2025-11-03--AI-text-to-video--SUCKS--rationingWAYtooSTRICT/said.by-logo-v1.0.0-aFewFireDancers.mp4" type="video/mp4">

                            Your browser does not support video.
                        </video>
                        <p class="backdropped">Logo made with the aid of <a href="https://x.com/ReneVeerman1977/status/1985356657098973657" class="nomod noPushState" target="grokDotComChatLogAtXdotCom">@grok at X.com</a> and <a href="https://dream-machine.lumalabs.ai" target="runwaymlDotCom" class="nomod noPushState">Luma Dream Machine AI</a>.</p>
                    </div>
                </td>
                <td>
                    <div class="divFor_neCompanyLogo">
                        <!-- THERE IS NO ALTERNATIVE BUT TO USE HARDCODED WIDTH:Npx;HEIGHT:Mpx HERE -->
                        <!--<img id="neCompanyLogo_img" style="position:absolute;opacity:0.00001;z-index:-1" src="/NicerAppWebOS/favicon/android-chrome-512x512.png"/>-->
                        <!--<canvas id="neCompanyLogo" width="125" height="125" onclick="event.data={element:'neCompanyLogo'}; na.logo.settings.stage.removeAllChildren(); na.logo.init_do_createLogo('neCompanyLogo','countryOfOriginColors');"></canvas>-->
                        <video controls autoplay loop playsinline>
                            <source src="https://grok.x.ai/temp/red-billed-oxpecker-buffalo.webm" type="video/webm">

                            <!-- MP4 fallback (Safari, older browsers) -->
                            <source src="/NicerAppWebOS/businessLogo/2025-11-03--AI-text-to-video--SUCKS--rationingWAYtooSTRICT/said.by-logo-v1.0.0-Rembrandt-plein.squares.Amsterdam.NL.mp4" type="video/mp4">

                            Your browser does not support video.
                        </video>
                        <p class="backdropped">Logo made with the aid of <a href="https://dream-machine.lumalabs.ai" target="runwaymlDotCom" class="nomod noPushState">Luma Dream Machine AI</a>.</p>
                    </div>

                </td>
                <td>&nbsp;</td>
            </tr>
        </table>

    </div>

    <div class="linkContainer">
        <a href="<?php echo $naURLs['docs__overview'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">Documentation</span></h3></a>
        <a href="<?php echo $naURLs['docs__license'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">License</span></h3></a>
        <a href="<?php echo $naURLs['docs__todoList'];?>" class="contentSectionTitle3_a"><h3 class="contentSectionTitle3"><span class="contentSectionTitle3_span">To-Do List</span></h3></a>
    </div>
    <script type="text/javascript">
        na.site.settings.loadingApps = false;
        na.site.settings.startingApps = false;
    </script>
