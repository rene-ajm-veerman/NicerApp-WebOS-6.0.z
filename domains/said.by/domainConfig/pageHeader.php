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
    </div>
    <div>
    <div>
        <h1 class="contentSectionTitle1" title="Said.by" style="text-shadow:1px 1px 5px skyblue;">Said.by Social Media</h1>
    </div>
    <div>
        <h1 class="contentSectionTitle1" title="a nicer.app company" style="font-size:small">a <a href="https://nicer.app" target="na" class="noPushState nomod">nicer.app</a> company.</h1>
    </div>
    </div>

    <script type="text/javascript">
        na.site.settings.loadingApps = false;
        na.site.settings.startingApps = false;
    </script>
