<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/3rd-party-site.wikipedia.org/index.js"></script>
<div id="navbar">
<?php
    global $naWebOS;
    global $naIP;
    echo $naWebOS->html_vividButton (
        0, 'align-items:center;justify-content:center;margin-right:10px;margin-left:10px;',

        'btnGrokipediaViewDirect',
        'vividButton_icon_100x100 grouped', '_100x100', 'grouped',
        '',
        'https://URLHERE.COM',
        '',
        '',

        201, 'View at grokipedia.com.',


        'btnCssVividButton_outerBorder.png',
        'backgrounds/Tiled/Blue/0022-blue-velvet-fabric-texture-seamless.jpg',
        null,//'btnCssVividButton_iconBackground.png',
        'Grok-Logo-xAI-Futuristic-AI-785-modded-by-NicerAppSoftware.png',

        '',

        'View at grokipedia.com',
        'grouped btnAdd themes',
        ''
    );
?>
</div>
<?php
    $file = realpath(dirname(__FILE__).'/../../../../../..').'/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/3rd-party-site.grokipedia.com/app.content_fullService.php';
    echo require_return ($file);
?>
