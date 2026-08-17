<?php
    require_once (dirname(__FILE__).'/mainmenu.items.php');
    global $naURLs;
?>
<li id="subMenu__apps-games" class="subMenu"><a class="linkToNewPage" href="/ ">Apps</a>
<ul>
    <li><a class="linkToNewPage" href="/ " title="Front page" alt="Front page">Front page</a></li>
    <li><a class="linkToNewPage" href="<?php echo $naURLs['cms'];?>" title="Blogging" alt="Blogging">Blogging</a></li>
    <li>
        <a class="linkToNewPage" href="<?php echo $naURLs['forums__view_index'];?>" title="Forums" alt="Forums">Forums</a>
    </li>
</ul>
</li>
