<?php
require_once (realpath(dirname(__FILE__).'/../NicerAppWebOS').'/boot.php');
global $menuStructure;
global $multiplier;
/*
unset ($_SESSION['na_js__hasContentMenu']);
unset ($_SESSION['na_js__menuItemWidth']);
unset ($_SESSION['na_js__screenWidth']);
unset ($_SESSION['na_js__menuSpace']);
*/
if (is_array($_POST) && array_key_exists('na_js__screenWidth', $_POST)) {
    $_SESSION['na_js__screenWidth'] = $_POST['na_js__screenWidth'];
    $_SESSION['na_js__menuSpace'] = $_POST['na_js__menuSpace'];
    $_SESSION['na_js__menuItemWidth'] = $_POST['na_js__menuItemWidth'];
    $_SESSION['na_js__hasContentMenu'] = $_POST['na_js__hasContentMenu'];
}

if (is_array($_SESSION) && array_key_exists('na_js__screenWidth',$_SESSION)) {
    $menuWidth = (float)$_SESSION['na_js__menuSpace'];
    $menuItemWidth = (float)$_SESSION['na_js__menuItemWidth'];
    $hasContentMenu = $_SESSION['na_js__hasContentMenu']=='true'?true:false;
    $itemGap = 5;
    
    $menuStructure = 'forWidestScreen';
    
    // reserve one menu-item in #siteMenu's topmost row for the apps menu
    $multiplier = $hasContentMenu ? 6 : 5;
    if ($menuWidth < ($multiplier * $menuItemWidth) + ($multiplier * $itemGap) ) $menuStructure = 'forMax5itemsWide';
    
    $multiplier = $hasContentMenu ? 5 : 4;
    if ($menuWidth < ($multiplier * $menuItemWidth) + ($multiplier * $itemGap) ) $menuStructure = 'forMax4itemsWide';
    
    $multiplier = $hasContentMenu ? 4 : 3;
    if ($menuWidth < ($multiplier * $menuItemWidth) + ($multiplier * $itemGap) ) $menuStructure = 'forMax3itemsWide';
    
    
    $multiplier = $hasContentMenu ? 3 : 2;
    if ($menuWidth < ($multiplier * $menuItemWidth) + ($multiplier * $itemGap) ) $menuStructure = 'forMax2itemsWide';

    $multiplier = $hasContentMenu ? 2 : 1;
    if ($menuWidth < ($multiplier * $menuItemWidth) + ($multiplier * $itemGap) ) $menuStructure = 'forMax1itemWide';
} else {
    $menuStructure = 'forWidestScreen';
}
//var_dump ($menuStructure); exit();
//var_dump ($_SERVER); 
//var_dump ($_SESSION); 
//var_dump ($menuStructure); 
/*
switch ($menuStructure) {
    case 'forWidestScreen':
        forWidestScreen();
        break;
    case 'forMax5itemsWide':
        forMax5itemsWide();
        break;
    case 'forMax4itemsWide':
        forMax4itemsWide();
        break;
    case 'forMax3itemsWide':
        forMax3itemsWide();
        break;
    case 'forMax2itemsWide':
        forMax2itemsWide();
        break;
    case 'forMax1itemWide':
        forMax1itemWide();
        break;
}*/
//exit();


function mainmenu_includeAllResolutionsAndSegments() {
    global $menuStructure;
    forMax1itemWide();
    forMax2itemsWide();
    forMax3itemsWide();
    //forMax4itemsWide();
    //forMax5itemsWide();
    //forWidestScreen();
    echo '<ul class="vividMenu_segments" style="display:none;">';
    echo require_return (dirname(__FILE__).'/mainmenu.items.apps-games.php');
	echo require_return (dirname(__FILE__).'/mainmenu.items.new-background.php');
    echo '</ul>';

}


function forWidestScreen() {
global $menuStructure;
global $multiplier;
?>
	<ul class="vividMenu_layout" style="display:none;" itemsLevel1="3" menuStructure="forWidestScreen">
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.apps-games.php');?>
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.new-background.php');?>
		<li class="subMenu" subMenuID="apps-games"><a href="-subMenu-">-subMenu-</a></li>
		<li class="subMenu" subMenuID="new-background"><a href="-subMenu-">-subMenu-</a></li>
		<li class="contentMenu"><a class="contentMenu" href="-contentMenu-">-contentMenu-</a></li>
	</ul>
<?php
}

function forMax5itemsWide() {
global $menuStructure;
global $multiplier;
?>
	<ul class="vividMenu_layout" style="display:none;" itemsLevel1="3" menuStructure="forMax5itemsWide">
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.apps-games.php');?>
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.new-background.php');?>
		<li class="subMenu" subMenuID="apps-games"><a href="-subMenu-">-subMenu-</a></li>
		<li class="subMenu" subMenuID="new-background"><a href="-subMenu-">-subMenu-</a></li>
		<li class="contentMenu"><a class="contentMenu" href="-contentMenu-">-contentMenu-</a></li>
	</ul>
<?php
}

function forMax4itemsWide () {
global $menuStructure;
global $multiplier;
?>
	<ul class="vividMenu_layout" style="display:none;" itemsLevel1="3" menuStructure="forMax4itemsWide">
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.apps-games.php');?>
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.new-background.php');?>
		<li class="subMenu" subMenuID="apps-games"><a href="-subMenu-">-subMenu-</a></li>
		<li class="subMenu" subMenuID="new-background"><a href="-subMenu-">-subMenu-</a></li>
		<li class="contentMenu"><a class="contentMenu" href="-contentMenu-">-contentMenu-</a></li>
	</ul>
<?php
}

function forMax3itemsWide () {
global $menuStructure;
global $multiplier;
?>
	<ul class="vividMenu_layout" style="display:none;" itemsLevel1="3" menuStructure="forMax3itemsWide">
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.apps-games.php');?>
		<?php //echo require_return (dirname(__FILE__).'/mainmenu.items.new-background.php');?>
		<li class="subMenu" subMenuID="apps-games"><a href="-subMenu-">-subMenu-</a></li>
		<li class="subMenu" subMenuID="new-background"><a href="-subMenu-">-subMenu-</a></li>
		<li class="contentMenu"><a class="contentMenu" href="-contentMenu-">-contentMenu-</a></li>
	</ul>
<?php
}

function forMax2itemsWide () {
global $menuStructure;
global $multiplier;
?>
	<ul class="vividMenu_layout" style="display:none;" itemsLevel1="2" menuStructure="forMax2itemsWide">
        <li><a href="/ ">Site</a>
            <ul>
                <?php //echo require_return (dirname(__FILE__).'/mainmenu.items.apps-games.php');?>
                <?php //echo require_return (dirname(__FILE__).'/mainmenu.items.new-background.php');?>
                <li class="subMenu" subMenuID="apps-games"><a href="-subMenu-">-subMenu-</a></li>
                <li class="subMenu" subMenuID="new-background"><a href="-subMenu-">-subMenu-</a></li>
            </ul>
		</li>
        <li class="contentMenu"><a class="contentMenu" href="-contentMenu-">-contentMenu-</a></li>
	</ul>
<?php
}

function forMax1itemWide () {
global $menuStructure;
global $multiplier;
?>
	<ul class="vividMenu_layout" style="display:none;" itemsLevel1="1" menuStructure="forMax1itemWide">
        <li><a href="/ ">Site</a>
            <ul>
                <?php //echo require_return (dirname(__FILE__).'/mainmenu.items.apps-games.php');?>
                <?php //echo require_return (dirname(__FILE__).'/mainmenu.items.new-background.php');?>
                <li class="subMenu" subMenuID="apps-games"><a href="-subMenu-">-subMenu-</a></li>
                <li class="subMenu" subMenuID="new-background"><a href="-subMenu-">-subMenu-</a></li>
                <li class="contentMenu"><a class="contentMenu" href="-contentMenu-">-contentMenu-</a></li>
            </ul>
		</li>
        
	</ul>
<?php
}

?>
