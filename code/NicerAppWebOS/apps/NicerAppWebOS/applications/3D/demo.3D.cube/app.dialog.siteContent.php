<?php
    global $naWebOS;
    $view = $naWebOS->view;
?>

<div id="site3D_demo_cube" class="na3D" theme="{$theme}">
</div>
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js"></script>
<script type="module">
    import { na3D_demo_cube } from '/NicerAppWebOS/logic.userInterface/na3D.source.js';
    setTimeout (function () {
        $('.na3D').each(function(idx,el){
            na.site.settings.na3D['#app_3D_demo_cube'] = new na3D_demo_cube (el, $(el).parent()[0]);
        });
    }, 1000);
</script>
