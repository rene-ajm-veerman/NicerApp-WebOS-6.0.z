<?php
    global $naWebOS;
    require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');
?>

<h1 class="contentSectionTitle2"><span class="contentSectionTitle2_span">NicerApp WebOS Development Direction</span></h1><br/><br/>

<p class="backdropped" style="width:30%"><b>Last modified : 2025-11-01-Saturday 12:05CET Amsterdam.NL</b></p>
<p class="backdropped" style="width:30%">
The yearly release stage of a new <a href="https://github.com/Rene-AJM-Veerman/NicerApp-WebOS-5.10.z">stable version</a> of NicerApp WebOS is always Dec 1st - Dec 24th.<br/>
A major upgrade of the documentation will happen well before Dec 1st 2025! :-)<br/>
</p>

<ol class="todoList" style="width:30%; width:content-width;">
    <li class="todoList"><div>(2025) Fix the final bugs in vividMenu.onmouseover(), vividMenu.onmouseout(), theme handling and in the theme editor.</div></li>

    <li class="todoList"><div>(2025) Add site registration options and login capabilities</div></li>

    <li class="todoList"><div>(2025) Start on .../logic.databases/uDB-2.0.0, to be based on .../logic.databases/generalizedDatabasesAPI-1.0.0
        <a href="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-uDB.png" class="nomod noPushState" target="tl-udb-1"><img src="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-uDB.png" style="width:320px"/></a>
        <a href="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-taskManager.png" class="nomod noPushState" target="tl-udb-1"><img src="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-taskManager.png" style="width:320px"/></a>
        <ol class="todoList_l1">
            <li class="todoList_l1"><div>Add query routines in uDB to translate couchdb index names to index ids.</div></li>
            <li class="todoList_l1"><div>Add and test MySQL, PostgreSQL and 'filesystemDB' (fsDB) sub-modules to uDB, as well as their interactions with the CouchDB part of uDB.</div></li>
        </ol>
    </div></li>

    <li class="todoList"><div>(2025) Start doing daily testruns of all apps and features.</div></li>

    <li class="todoList"><div>(2026) Build a color progressbar into the background of the photo album upload component that I use.</div></li>

    <li class="todoList"><div>(2026 to 2027) Complete a <a href="/demo-3D-fileBrowser">3D folders + files structures viewer</a>, linked to all sorts of 2D user interface components/widgets.</div></li>

    <li class="todoList"><div>(2026) Add a log viewer dialog, for both console, server, for the current PHP session, to all browsers.<br/>
    Add "Admin" option to view the logs of others within a given datetimestamp range.</div></li>

    <li class="todoList"><div>(2026) Add statistics/analytics to NicerApp</div></li>

    <li class="todoList"><div>(2026) Allow log entries (of console.log in the browser, and all database traffic and error messages on the server) to be logged to a different machine on the LAN than the main server.</div></li>

    <li class="todoList"><div>(2026) Write a forums/posts + replies/posts API for this WebOS, for the v5.11.z branch, which might be published under a proprietary license.</div></li>

    <li class="todoList"><div>(2026) Write a notification API based on the VAPID browser API for this WebOS.</div></li>

    <li class="todoList"><div>(2026) Write an in-app tutorial API for this WebOS.</div></li>

    <li class="todoList"><div>(2027) Start on .../businessLogic/filePhoenix</div></li>

    <li class="todoList"><div>(2027) oAuth + email authentication + password authentication leading to a username + groups-names list and from there into a set of CSS themes, subscription IDs, and anything that can be described by a CouchDB search query (which would also define a binary search ID/name).</div></li>

    <li class="todoList"><div>(2027) Webshop functionality; Secure IMAP email facilities (also cloudhosting compatible versions), Banking Accountancy and Tax Filing modules, and the actual webshop interface with product placement admin interface and email marketing also done from the same admin interface, as just it's 1st finalized feature set. For version 5.9.z, to be released between Jan 2026 and Dec 1st 2026, on a domain name that's yet to be thought up (by me myself) :-)</div></li>

    <li class="todoList"><div>(2026) Get na.m.log() to log per datetimeRangeBegin.milliseconds to datetimeRangeEnd.milliseconds into a database on the server, for use in /view/logs as the PHP data belonging to a PHP session_id().<br/>
    Status : Completed javascript code, now needs revamped generalizedDatabaseAPI written and called, and db businesslogic PHP code.</div></li>

    <li class="todoList"><div>(2025 or 2026) Start work on a self-healer component for this WebOS, and a lot more Desktop OS level automated security to be installed via the <a href="https://github.com/NicerEnterprises/NicerApp-WebOS/tree/main/NicerAppWebOS/scripts.install" target="mainBashInstallScript_for_NicerAppWebOS" class="nomod noPushState contentSectionTitle3_a"><span class="contentSectionTitle3_span">main Bash install script</span></a>.</div></li>

    <li class="todoList"><div>(2025 or 2026) Upgrade the blogging features.
        <ol class="todoList_l1">
            <li class="todoList_l1"><div>(Jan 2025) (FROZEN[2]) Create a new HTML WYSIWYG rich-text editor component (that will be entirely created from scratch by me, ensuring i have the copyright and rights reserved for this component), that ties into the NicerApp Theme Editor.<br/>
            This is stalled because browser makers need to start supporting a window.getSelection() that returns a .anchorOffset and .extentOffset that works on the .innerHTML instead of the .innerText of any given element (usually the .commonAncestorElement).<br/>
            <br/>
            [1] I need 'id' and 'classNames' input fields in the tinymce toolbar (to tie edited content into my universal web theme editor for my CMS/WebOS), creating a tinymce-4.9.11-na-1.0.0 branch of the <a href="https://www.tiny.cloud/" class="noPushState" target="tinymce">tinymce</a> MIT licensed v4.9 code.<br/>
            I've taken the liberty of web form contacting the tinymce sales team to ask if they can implement this for universal web theme editor builders for free.<br/>
            <br/>
            [2] Turns out someone made a snippet of code for tinymce that'll do what I need. Much excitement lies ahead when I officially tie tinymce into my theme editor :-)
            </div></li>

            <li class="todoList_l1"><div>(2026) (NEARLY DONE)<br/>
            Supply data from a HTML+CSS form (as a tabpage in the universal web theme editor) into <a href="https://github.com/NicerEnterprises/NicerApp-WebOS/blob/main/NicerAppWebOS/logic.business-5.8.z/class.core.WebsiteOperatingSystem-5.y.z.php#L1374" class="noPushState contentSectionTitle3_a" target="naGH_wos1088"><span class="contentSectionTitle3_span">css_keyframes_to_array() and css_animation_template_to_animation()</span></a>, colors to mix will be defined by two 'colorpicker' (3rd-party) pieces of JS.
            </div></li>

            <li class="todoList_l1"><div>(CURRENT) (2023 Nov,Dec) Extend the current limited permissions system to a full CMS, Web User Interfaced, permissions system for the WebOS.
                <ol class="todoList_l2">
                    <li class="todoList_l2"><div>(CURRENT) (2023 Nov,Dec) Extend vividButton to bring up vividDialogs and vividMenus when hovered over. In fact, it shouldn't matter anymore whether you've hovering from a vividMenu through a vividDialog filled with vividButtons to a goal either anymore.<br/>So I need to create a new 'vividUI' (vividUserInterface) component, that will direct all of this.<br/>
                    I've already started on this, and it seems to be easy going :)</div></li>
                </ol>
            </div></li>
        </ol>
    </div></li>

    <li class="todoList"><div>(CURRENT) Expand functionality of the 3D file and data browser/viewer app
    </div></li>

    <li class="todoList"><div>Add the next set of animations to .todoList p, span, h1, h2, and h3 HTML elements (config data for which is to be visualized and interacted with in the theme editor by the way).<br/>
    These will be javascript + PHP generated visualizations for SVG (animated) graphics.<br/>
    I promise to bring you all some level of CSS animations in the generation config language :)<br/>
    Maybe even a real translation of CSS animations (they're easy to parse), into SVG color and transparency animations! :D</div></li>

    <li class="todoList"><div>Create a webshop app with a subscription model (that i'll publish as https://nicer.app/shop), in collaboration with paypal.com</div></li>

    <li class="todoList"><div>Create a donations button (again, in collaboration with paypal.com), with monthly goal indicator, for the news app.</div></li>

    <li class="todoList"><div>Create an app-store app that links into an eCommerce app component set (a bunch of javascript, svg, css and html snippet files).</div></li>

    <li class="todoList"><div>Implement bandwidth throttling in the <a href="https://github.com/NicerEnterprises/NicerApp-WebOS/tree/main/NicerAppWebOS/scripts.install" target="mainBashInstallScript_for_NicerAppWebOS" class="nomod noPushState">main Bash install script</a>.</div></li>

    <li class="todoList"><div>Add a checkbox in the Theme Editor to select backgrounds and stretch instead of tile them for any DIV.</div></li>

    <!--<li class="todoList"><div>Restore the automatic retrieval of new backgrounds download routines for nicerapp via free to use methods of delivery at Google image search and (TODO :)Bing image search.</div></li>-->

    <li class="todoList">
    <div><pre class="todoList">
    rewrite the backgrounds analysis and automatic resizing routines;
    - put all of the backgrounds in a DOMAIN_TLD___backgrounds dataSet with relative filepath (starting at siteMedia/backgrounds) and image size.
    - let users search for backgrounds based on filepath, then save those searches in their account settings and make them viewable as photoalbums.
    </pre></div>
    </li>

    <li class="todoList"><div>Upgrade the news app and vividDialog : add siteToolbarLeft functionality :<br/>
        <ol class="todoList_l1">
            <li class="todoList_l1"><div>add a 'translate' dropdown box to the app-specific options menu</div></li>
            <li class="todoList_l1"><div>add French news sources</div></li>
            <li class="todoList_l1"><div>add/enable/disable/remove any URL to a combination of lists that are each given a name, which get stored in several database-stored dataSubSets (records/documents) inside a dataSet (table/couchdb-database).<br/>
            </li>
            <li class="todoList_l1"><div>the ability to assign specific 'theme' and 'sub-theme' settings to such a URL.</div></li>
            <li class="todoList_l1"><div>the ability to do keyphrase searches (perhaps later with 'or' and 'and' logic support) on the news content gatered, and paint that content with specific 'theme' and/or 'sub-theme' settings.<br/>
            (putting all of this in siteToolbarLeft and the rest in the siteThemeEditor, and that those can already be shown at the same time, means you can edit *all* user-interface settings for *any* app or service on any HD screen or pad screen.</div></li>
            <li class="todoList_l1"><div>let vividDialog have a vividMenu, with vividButton icons that will lead to vividMenus and vividDialogs and vividDialogPopups, at the top-right of it's borders.<br/>
            the contents of this menu should be defined in a &lt;UL&gt; structure (that can, if needed, get loaded with fresh content via AJAX), much like the vividMenu already is today.</div></li>
        </ol>
    </div>
    </li>

    <li class="todoList"><div>Build a comments engine and user-interface again, this time comments get stored in a database instead of on the server filesystem.</div></li>

    <li class="todoList"><div>Figure out a way to store the width and height of each background found in the filesystem in the output of .../domainConfig/DOMAIN.TLD/ajax_backgrounds_recursive.php and .../domainConfig/DOMAIN.TLD/ajax_backgrounds.php.<br/>
    (NOT DONE) Then use this information in the backgrounds menu to select only elligible backgrounds, and popup an error message 'No backgrounds found, reverting to search key = {$someSearchKey}' when no backgrounds are found for the current search / menu-option.</div></li>

    <li class="todoList"><div>Integration of payment platforms (as plugins) for paypal.com, creditcards, and the Dutch banking system iDeal.</div></li>

    <li class="todoList"><div>Music production app via linux commandline app sonic-pi, integration of that app with payment modules and musicPlayer.</div></li>

    <li class="todoList"><div>Add MySQL and PostgreSQL to the list of supported database architectures (via .../NicerAppWebOS/3rd-party/adodb5), currently only couchdb is supported.<br/>
    </div></li>

    <li class="todoList"><div>(2027) Write a forums/posts + replies/posts API for this WebOS, for the v5.8.z branch.</div></li>

    <li class="todoList"><div>(2027 or 2028) Rewrite the YouTube search features to funnel videos into the backgrounds list for a NicerApp site (in addition to playing them via a vividDialog combination).</div></li>
</ol>
<!-- no longer necessary here in v5.8.z, the following is now done in .../NicerWebAppOS/site-6.y.z.js::startUIvisuals() :
<script type="text/javascript">
    na.site.bindTodoListAnimations (
        '.todoList > li, '
        +'.contentSectionTitle3, '
        +'p.todoList, h1.todoList, h2.todoList, h3.todoList, '
        +'.todoList > lI > div, '
        +'.todoList > lI > pre, '
        +'.todoList_l1 > li, '
        +'.todoList_l1 > lI > div, '
        +'.todoList_l1 > lI > pre, '
        +'.todoList_l2 > li, '
        +'.todoList_l2 > lI > div, '
        +'.todoList_l2 > lI > pre '
    );
</script>
-->
