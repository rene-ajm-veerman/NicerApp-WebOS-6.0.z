<?php
    global $naWebOS;
    global $naLAN;
    $view = $naWebOS->view;
    require_once (realpath(dirname(__FILE__).'/../../../../..').'/functions.php');

    $rootURL = 'http://localhost';

if (
    $naLAN
    /*|| ((array_key_exists('pw',$_GET)
            && (
                $_GET['pw']=='efv7750'
                || $_GET['pw']=='xmas2025ai-d'
                || $_GET['pw']=='pl-2025-10-24-15-03'
                || $_GET['pw']=='AllahuaAckbar507788'
                || $_GET['pw']=='alwaysXMASohNoes-50s'
                || $_GET['pw']=='alwaysXMASzzz'
            )
        )
    )*/
) {


    if (array_key_exists('inputJSONurl', $_REQUEST)) {
        $inputJSONurl = $_REQUEST['inputJSONurl'];
    } else {
        $inputJSONurl = '/siteMedia/backgrounds';
    }
    //echo '<pre>'; var_dump ($naWebOS->view); die();


    foreach ($view as $k => $rec) {
        break;
    }
    if ($k!=='/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer') {
            $views = encode_base64_url(json_encode([
                "misc" => [
                    'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/3D'
                ],
                "apps" => [
                    'app.3D.fileExplorer' => [
                        'parameters' => [
                            'thumbnails' => './thumbs/300'
                        ],
                        'seoValue' => '3D'
                    ]
                ]
            ]));
            $view = json_decode (decode_base64_url($views), true);
    }
    $theme = '{$theme}';
    if ($theme === '{$theme}') $theme = 'dark';
?>
    <script type="text/javascript" id="naWebOS__js_app_3D_fileExplorer__data">

        na.m.waitForCondition('/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer/main.js loaded?', function() {
            var r = na.site.settings.na3D && typeof na.site.settings.na3D['#app_3D_fileExplorer'] === 'object';
            return r;
        }, function() {
            na.site.settings.na3D['#app_3D_fileExplorer'].settings.parameters =

                <?php echo json_encode($view, JSON_PRETTY_PRINT); ?>;
        }, 100);
    </script>
    <style>
    .na-node-tooltip {
        position: fixed;
        z-index: 9999;
        pointer-events: none;
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 8px;
        padding: 8px 12px;
        color: #fff;
        font-size: 13px;
        max-width: 280px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        transition: opacity 0.1s ease;
    }

    .na-tooltip-icon {
        font-size: 20px;
        margin-bottom: 4px;
    }

    .na-tooltip-name {
        font-weight: bold;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .na-tooltip-path {
        opacity: 0.6;
        font-size: 11px;
        margin-top: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    </style>
    <div id="na3D_progress" style="display:none; position:absolute; top:10px; left:10px; right:10px; z-index:1000; background:rgba(0,0,0,0.7); padding:8px; border-radius:6px;">
        <div style="color:#0ff; font-size:13px; margin-bottom:6px;">Initializing 3D File Browser...</div>
        <div style="height:6px; background:#222; border-radius:3px; overflow:hidden;">
        <div id="na3D_progressBar" style="height:100%; width:0%; background:linear-gradient(90deg, #0ff, #0f8); transition:width 0.2s ease-out;"></div>
        </div>
        <div id="na3D_progressText" style="color:#aaa; font-size:12px; margin-top:4px; text-align:center;">0%</div>
    </div>
    <div id="site3D_backgroundsBrowser" class="na3D" theme="<?php echo $theme;?>">
    </div>
    <div id="site3D_label" class="label" theme="<?php echo $theme;?>"></div>
    <script type="module" src="/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js"></script>
<!--     <script src="/NicerAppWebOS/businessLogic/vividUserInterface/v6.y.z/3D/3d-force-graph/src/3d-force-graph.js"></script> -->
    <script src="//cdn.jsdelivr.net/npm/3d-force-graph"></script>
<!--     <script src="//cdn.jsdelivr.net/npm/three-spritetext"></script> -->
    <script type="module" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer/main.js"></script>
    <!--<script type="module" src="/NicerAppWebOS/businessLogic/ajax/ajax_loadJSmodule.php?file=/NicerAppWebOS/apps/NicerAppWebOS/applications/3D/app.3D.fileExplorer/main.js"></script>-->
<?php
}
?>
