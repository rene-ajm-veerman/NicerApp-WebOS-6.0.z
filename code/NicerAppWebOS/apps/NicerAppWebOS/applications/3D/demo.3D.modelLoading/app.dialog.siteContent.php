<?php 
    global $naWebOS;
    $view = $naWebOS->view;
?>
<div id="site3D_demo_models" class="na3D" style="width:100%;height:100%;" theme="{$theme}">
</div>
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.js"></script>
<!--<script async src="https://unpkg.com/es-module-shims@1.6.3/dist/es-module-shims.js"></script> <script type="importmap"> { "imports": { "three": "https://unpkg.com/three@v0.152.0/build/three.module.js", "three/addons/": "https://unpkg.com/three@v0.152.0/examples/jsm/" } } </script>-->
<script type="module">
    import { na3D_fileBrowser, na3D_demo_models, na3D_demo_cube } from '/NicerAppWebOS/logic.userInterface/na3D.source.js';
    setTimeout (function () {
        $('.na3D').each(function(idx,el){
            //na.site.settings.na3D['#'+el.id] = new na3D_demo_cube (el, $(el).parent()[0]);
            na.site.settings.na3D['#'+el.id] = new na3D_demo_models (el, $(el).parent()[0]);
            //na.site.settings.na3D['#'+el.id] = new na3D_fileBrowser(el, $(el).parent()[0], na.site.settings.backgroundsRecursive);
        });
    }, 2500);
</script>
