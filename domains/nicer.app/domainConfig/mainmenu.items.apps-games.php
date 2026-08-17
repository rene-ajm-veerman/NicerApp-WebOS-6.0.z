<?php
    require_once (dirname(__FILE__).'/mainmenu.items.php');
    global $naURLs;
?>
<li id="subMenu__apps-games" class="subMenu"><a class="linkToNewPage" href="/ ">Apps</a>
<ul>
    <li><a class="linkToNewPage" href="/ " title="Front page" alt="Front page">Front page</a></li>
    <li><a class="linkToNewPage" href="<?php echo $naURLs['tarot'];?>" title="Tarot" alt="Tarot">Tarot</a></li>

    <?php require_once(dirname(__FILE__).'/../NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/mainmenu.liOnly.php');?>

    <li><a class="linkToNewPage" href="/demo-3D-fileBrowser" title="3Dfe" alt="3D file explorer">3D file explorer</a></li>

    <li><a class="linkToNewPage" href="<?php echo $naURLs['docs__overview'];?>" alt="Documentation">Documentation</a>
        <ul>
            <li><a class="linkToNewPage" href="<?php echo $naURLs['docs__overview'];?>" alt="Documentation">Platform Overview</a></li>
            <li><a class="linkToNewPage" href="/license" alt="License">License</a></li>
            <li><a class="linkToNewPage" href="/todo" alt="To-Do List">To-Do List</a></li>
            <li><a class="linkToNewPage" href="/company" alt="Company Overview">Company Overview</a></li>
        </ul>
    </li>
</ul>
</li>
