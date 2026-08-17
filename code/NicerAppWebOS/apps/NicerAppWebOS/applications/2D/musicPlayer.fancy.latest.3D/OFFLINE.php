<?php
    /*
    $views = array(
        'music_index__DJ_Firesnake' => array (
            '/NicerAppWebOS/apps/nicer.app/applications/2D/musicPlayer' => array (
                'set' => 'DJ_FireSnake',
                'seoValue' => 'music-2015-DJ_FireSnake'
            )
        ),
        'music_index__Deep_House' => array (
            '/NicerAppWebOS/apps/nicer.app/applications/2D/musicPlayer' => array (
                'set' => 'Deep_House',
                'seoValue' => 'music-2021-Deep_House'
            )
        ),
        'music_index__Beautiful_Chill_Mixes' => array (
            '/NicerAppWebOS/apps/nicer.app/applications/2D/musicPlayer' => array (
                'set' => 'Beautiful_Chill_Mixes',
                'seoValue' => 'music-Beautiful_Chill_Mixes'
            )
        ),
        'music_index__Black_Horse__Mongolian_Traditional_Classical_Music_Art' => array (
            '/NicerAppWebOS/apps/nicer.app/applications/2D/musicPlayer' => array (
                'set' => 'Black_Horse__Mongolian_Traditional_Classical_Music_Art',
                'seoValue' => 'music-Black_Horse-Mongolian-Traditional-Classical-Music-Art'
            )
        )
    );
    $json = array();
    $urls = array();
    foreach ($views as $viewName => $viewSettings) {
        $json[$viewName] = json_encode($viewSettings);
        $urls[$viewName] = '/apps/'.encode_base64_url($json[$viewName]);
    };
    */
    $rootPath_vkdmd = realpath(dirname(__FILE__).'/../../../../../..');
    require_once ($rootPath_vkdmd.'/NicerAppWebOS/boot.php');
    require_once ($rootPath_vkdmd.'/domainConfig/'.$naWebOS->domainFolder.'/mainmenu.items.php');
    global $naURLs;
    //var_dump ($naURLs);
?>
    <link href="https://fonts.googleapis.com/css?family=Krona+One&display=swap" rel="stylesheet"> 
	<script type="text/javascript">
        delete na.site.settings.current.loadingApps;
	</script>
    <style>
        p {
            color : white;
            background : rgba(0,0,0,0.4);
            border-radius : 14px;
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
            text-align : center;
        }
        
    </style>
    <div class="container">
    <div class="bg">
        <p>
        Due to a change in the copyright laws of my country,
        I can no longer serve music or video clips to the world :(
        </p>
    </div>
    </div>
