<?php
    require_once (dirname(__FILE__).'/mainmenu.items.php');
    global $naURLs;
?>
<li id="subMenu__apps-games" class="subMenu"><a class="linkToNewPage" href="/ ">Apps</a>
<ul>
    <li><a class="linkToNewPage" href="/ " title="Front page" alt="Front page">Front page</a></li>
    <li><a class="linkToNewPage" href="<?php echo $naURLs['tarot'];?>" title="Tarot" alt="Tarot">Tarot</a></li>
    <li><a class="linkToNewPage" href="<?php echo $naURLs['newsHeadlines_englishNews'];?>" title="News headlines" alt="News headlines">News</a></li>
    <li><a class="linkToNewPage" href="<?php echo $naURLs['newsHeadlines_englishNews_worldHeadlines'];?>" title="News headlines" alt="News headlines">International News</a></li>
    <!--<li><a class="linkToNewPage" href="<?php echo $naURLs['forums__view_index'];?>" title="Forums" alt="Forums">Forums</a></li>-->
    <li><a class="linkToNewPage" href="<?php echo $naURLs['music'];?>" alt="Music">Music</a></li>
    <!--<li><a class="linkToNewPage" href="<?php echo $naURLs['webmail'];?>" title="Webmail" alt="Webmail">Webmail</a></li>-->
    <li><a class="linkToNewPage" href="<?php echo $naURLs['docs__overview'];?>" alt="Documentation">Documentation</a>
        <ul>
            <li><a class="linkToNewPage" href="<?php echo $naURLs['docs__overview'];?>" alt="Documentation">Platform Overview</a></li>
            <li><a class="linkToNewPage" href="<?php echo $naURLs['docs__license'];?>" alt="License">License</a></li>
            <li><a class="linkToNewPage" href="<?php echo $naURLs['docs__todoList'];?>" alt="To-Do List">To-Do List</a></li>
            <li><a class="linkToNewPage" href="<?php echo $naURLs['docs__companyOverview'];?>" alt="Company Overview">Company Overview</a></li>
        </ul>
    </li>
</ul>
</li>
