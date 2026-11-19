<?php
    $rootPath_vkdmd = realpath(dirname(__FILE__).'/../../../../../..');
    //echo $rootPath_vkdmd; die();
    require_once ($rootPath_vkdmd.'/NicerAppWebOS/boot.php');
    require_once ($rootPath_vkdmd.'/../../domains/'.$naWebOS->domainFolder.'/domainConfig/mainmenu.items.php');
    global $naWebOS;
    global $naURLs;
    //var_dump ($naURLs);
    $appFolder = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer';
    $rf = dirname(__FILE__).'/music/';
    $ffi = []; // file and folder index

    //var_dump($rf); die();
    if (file_exists(dirname(__FILE__).'/index.views.json') && (!array_key_exists('rc',$_GET) || $_GET['rc']!=='true')) {
        // ONLY SLOWS THINGS DOWN CONSIDERABLY $folders = json_decode(file_get_contents($rf.'/index.foldersAndFiles.json'),true);
        $views = json_decode(file_get_contents(dirname(__FILE__).'/index.views.json'),true);
        //var_dump(json_last_error_msg());
        //var_dump($views);die();
    } else {
        $folders = getFilePathList ($rf,true,'/*.*/',null,['dir']);
        //var_dump ($folders); die();
        $views = [];
        foreach ($folders as $idx => $folder) {
            if (strpos($folder['path'],'/meta')!==false) continue;
            $k1 = 'music__index__';
            $k2a1 = str_replace(' ','-t1-',basename($folder['realPath']));
            $k2a1 = str_replace('/','-t2-',$k2a1);

            $k2a2 = str_replace(' ','-t1-',basename($folder['realPath']));
            $k2a2 = str_replace('/','-t2-',$k2a2);
            $k3 = $k1.$k2a1;

            $files = getFilePathList ($folder['path'], false, '/.*.mp3/i', null, ['file']);
            if (count($files)===0) continue;
            //var_dump ($files); die();
            //$k = $folder['webPath'];
            $ffi = [];
            foreach ($files as $idx2 => $file) {
                $f = str_replace($appFolder.'/music/','','/'.$file['webPath']);
                //var_dump ($f); die();
                //$f = str_replace(' ','-t0000-',$f); // start unicode compatible custom translation table for JSON files :
                //$f = str_replace('/','-t0001-',$f);
                //$f = str_replace('(','-t0002-',$f);
                //$f = str_replace(')','-t0003-',$f);
                $ffi[] = encode_base64_url($f);
            }

            $views[] = [
                $k3 => [
                    $appFolder => [
                        'folder' => $folder,
                        'files' => $ffi,
                        'set' => $k2a1,
                        'seoValue' => 'music-'.$k2a2
                    ]
                ]
            ] ;

        }
        $json = array();
        $urls = array();
        //echo'<pre>';    var_dump($views);die();
        // ONLY SLOWS THINGS DOWN CONSIDERABLY : file_put_contents(dirname(__FILE__).'/index.foldersAndFiles.json', json_encode($folders));
        file_put_contents(dirname(__FILE__).'/index.views.json', json_encode($views));
    };

    $json = [];
    $urls = [];
    foreach ($views as $viewIDX => $view) {
        foreach ($view as $viewIDX2 => $app)
        foreach ($app as $appFolder => $viewSettings) {
            $viewName = $viewSettings['set'];
            $viewSettings['appFolder'] = $appFolder;
            $json[$viewName] = json_encode($viewSettings);
            $urls[$viewName] = '/view/'.encode_base64_url($json[$viewName]);
        }
    };

//var_dump($urls); die();

    /*
    if (file_exists(dirname(__FILE__).'/index.files.json') && (!array_key_exists('rc',$_GET) || $_GET['rc']!=='true')) {
        //$files = json_decode (file_get_contents(dirname(__FILE__).'/index.files.json'),true);
    //} else {
        $files = getFilePathList ($rf,true,'*.*',null,['file']);
        //var_dump($files); die();
        file_put_contents(dirname(__FILE__).'/index.files.json', json_encode($folders));
    };
    var_dump (count($files)); die();
    */

?>
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet"> 
	<script type="text/javascript">
        $(document).ready(function(){
            delete na.site.settings.current.loadingApps;
            delete na.site.settings.current.startingApps;
            delete na.site.settings.loadingApps;
            delete na.site.settings.current.loadingApps;
            delete na.site.settings.running_loadContent;
            delete na.site.settings.running_loadTheme;
        })
	</script>

    <style>
        p {
            color : white;
            background : rgba(0,0,0,0.4);
            border-radius : 14px;
        }
        li {
            display: flex;
            flex-direction: row;
            align-items: center;
        }

        #pageTitle {
            display : inline-block;
        }

        .container {
            display : flex;
            justify-items : center;
            align-items : start;
            justify-content : center;
            align-content : start;
            width : 100%;
            height : 100%;
        }

        .bg {
            display : inline-block;
            background : rgba(0,0,0,0.4);
            border-radius : 14px;
            height : fit-content;
        }
        p.p1 { width : 380px; text-align:center;}
        .naMusicPlayer_folder {
            font-size : large;
            font-weight : bold;
            color : skyblue;
        }
        .naMusicPlayer_file {
            display : none;
            font-size : normal;
            font-weight : normal;
            color : lime;
            border-radius : 25px;
            background : rgba(250,250,250,0.5);
            border : 3px ridge rgba(100,100,255,0.5);
            box-shadow : inset 0px 0px 3px 2px rgba(0,0,0,0.8), 2px 2px 4px 3px rgba(0,0,0,0.7);
        }
    </style>
    <div class="container" style="display:block;position:absolute;top:10px;left:10px;width:310px;">
        <div id="divFor_neCompanyLogo" style="margin-left:5px;margin-bottom:20px;width:300px;border-radius:10px;border:solid rgba(0,0,0,0.8);background:rgba(0,0,50,0.555);padding:5px;box-shadow:0px 0px 2px 1px rgba(0,0,0,0.55);">
            <!-- THERE IS NO ALTERNATIVE BUT TO USE HARDCODED WIDTH:Npx;HEIGHT:Mpx HERE -->
            <!--<img id="neCompanyLogo_img" style="position:absolute;opacity:0.00001;z-index:-1" src="/NicerAppWebOS/favicon/android-chrome-512x512.png"/>-->
            <!--<canvas id="neCompanyLogo" width="125" height="125" onclick="event.data={element:'neCompanyLogo'}; na.logo.settings.stage.removeAllChildren(); na.logo.init_do_createLogo('neCompanyLogo','countryOfOriginColors');"></canvas>-->
            <iframe src="https://nicer.app/NicerAppWebOS/businessLogo/NicerEnterprisesLogo-v1.3.1.html" style="height:300px;width:300px;overflow:hidden;border:none;background:transparent;"></iframe>
        </div>
        <div>
            <div style="margin-left:30px; width:calc(100% - 387px);"><h1 class="contentSectionTitle1" title="NicerApp WebOS homepage">NicerApp WebOS homepage</h1></div>
        </div>
    </div>
    <div id="divFor_neCompanyLogo_accreditation" style="margin:20px;width:310px;border-radius:10px;border:solid rgba(0,0,0,0.8);background:rgba(0,0,50,0.555);padding:5px;box-shadow:0px 0px 2px 1px rgba(0,0,0,0.55);position:absolute;top:320px;left:10px;">
        Logo made with <a href="https://x.com/grok" target="xDotCom-grok-homepage" class="nomod noPushState">https://x.com/grok</a> on <a href="https://x.ai" target="xAI-homepage" class="nomod noPushState">https://x.ai</a><br/><span style="font-size:small;background:none;box-shadow:none;">between 2025-07-23(Wednesday) +-05:45CEST and 2025-07-24 +-04:40CEST</span>.
    </div>


    <div class="container" style="width:calc(90% - 387px)">
    <div class="bg">
        <h1 id="pageTitle" class="naVividTextCSS" style="font-size:200%;">music on nicer.app</h1>
        <p class="p1">
        Statement by NicerApp WebOS Owner:<br/>
        These files are put online for anyone to view and download based on fair use in turn based on free distribution, educational purpose, and anti-fear (so: general public health) reasons.<br/>
        For copyright claims, please email <a href="emailto:rene.veerman.netherlands@gmail.com">rene.veerman.netherlands@gmail.com</a> with a subject line that starts with 'COPYRIGHT CLAIM' or 'COPYRIGHT NOTICE', preferably detailing where the merchandise for your copyright claim is located as well, so that I can list that on the player page.<br/>
        </p>
        <p class="p1">
        Profits for these music pages will be shared 20% to the Owner of NicerApp WebOS, and 80% to legitimate copyright claim holders per calendar year, subject to the amount of seconds that their music was played in total (so for any IP, for any hour on any day) relative to the amount of seconds of total playlength for the music that is under a copyright claim for that calendar year.
        </p>
        <p class="p1">
        Copyright Disclaimer: - Under section 107 of the copyright Act 1976, <a href="https://en.wikipedia.org/wiki/Fair_use" class="contentSectionTitle2_a" target="fairUse">which was reaffirmed in a 2021 court decision</a>, allowance is made for FAIR USE for purpose such a as criticism, comment, news reporting, teaching, scholarship and research. Fair use is a use permitted by copyright statues that might otherwise be infringing. Non- Profit, educational or personal use tips the balance in favor of FAIR USE.
        </p>
        <p class="p1">
        This server is hosted in The Netherlands.<br/>
        Dutch Law Disclaimer regarding copyright status of these files reads :
        <a href="/NicerAppWebOS/screenshots/2025%20A.D./Screenshot_20250622_015900.png" class="nomod noPushState" target="_new"><img src="/NicerAppWebOS/screenshots/2025%20A.D./Screenshot_20250622_015900.png" style="width:380px"/></a>
        </p>
        <ol>
        <?php
            if (false) {
                $di = [
                    'matches' => $views
                ];
                $cmd = [
                    'di' => $di,
                    '{"HTML_className":"naWebOS-debug-outer-DIV"}' => 'naWebOS_desktopos_info',
                    '$appFolder' => $appFolder
                ];
                //naWebOS_output_debug_info ($cmd);
                //echo '<pre style="color:lime;">'; var_dump ($views); //die();
            };
            //echo '<pre style="color:lime;">'; var_dump ($views); die();

            $k4 = 0;
            foreach ($views as $k1 => $v1) {
                foreach ($v1 as $k2 => $v2) {
                    foreach ($v2 as $k3 => $v3) {
                        //if ($v3['set']=='meta') continue;
                        $k3a = str_replace(' ','-t1-',$k3);
                        $k3a = str_replace('/','-t2-',$k3a);
                        $k4++;

                        $files = $v3['files'];

                        //echo '<pre style="color:skyblue;">'; var_dump ($v3); echo '</pre>'; die();
                        //var_dump ($v3);   var_dump($urls); die();

                        //                        <li><af class="naMusicPlayer_folder contentSectionTitle3_a naVividTextCSS" href="<?php /*echo $urls[$v3['set']]*/? >#"><span class="contentSectionTitle3_span" style="background:none;box-shadow:none;  "><a href="javascript:$('#ol-<?php echo $k3a;? >').css({display:'block'});"><?php echo str_replace('NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer/music/','',$v3['folder']['webPath']);? ></a></span></af></li>
        ?>
                        <li><a href="javascript:$('#ol-<?php echo $k4;?>').css({display:'block'});">
                        <?php echo str_replace('NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer/music/','',$v3['folder']['webPath']);?>
                        </a>
                        <ol id="ol-<?php echo $k4;?>" style="display:none;">
        <?php
                            //$noFiles = [];
                            $k5 = 0;
                            foreach ($files as $idx => $file) {
                                /*
                                $f = str_replace('-t0000-',' ',$file); // start unicode compatible custom translation table for JSON files :
                                $f = str_replace('-t0001-','/',$f);
                                $f = str_replace('-t0002-','(',$f);
                                $f = str_replace('-t0003-',')',$f);
                                */
                                $file = $appFolder.'/music'.decode_base64_url($file);
                                $k5++;

                                /*
                                $dbg = [
                                    'file' => $file,
                                    'v3' => $v3['folder']['webPath']
                                ];
                                echo '<pre>'; var_dump($dbg); echo '</pre>'; //die();
                                */

                                if (strpos($file,$v3['folder']['webPath'])!==false) {
                                    ?>
                                    <li>
                                        <audio id="audio_<?php echo $k4.'_'.$k5;?>" controls src="/<?php echo str_replace(' ', '%20',$v3['folder']['webPath'].'/'.basename($file));?>" target="naMusicPlayer-HTMLmediaPlayer" class="nomod noPushState naMusicPlayer_file"></audio>
                                        <a href="javascript:$('#audio_<?php echo $k4.'_'.$k5;?>').css({display:'block'});"><?php echo basename($file)?></a>
                                    </li>
                                    <?php
                                }
                            }
        ?>
                        </ol>
                        </li>
        <?php
                    }
                }
            }
        ?>
        </ol>
    </div>
    </div>
