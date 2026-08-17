<?php
    global $naWebOS;
    require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');
?>
<script type="text/javascript">
na.site.globals.app = { "/docs" : { "page" : "todo" }};
</script>

<h1 class="contentSectionTitle2"><span class="contentSectionTitle2_span">NicerApp WebOS Development Direction</span></h1><br/><br/>

<p class="backdropped" style="width:30%"><b>Last modified : <?=json_decode(file_get_contents(__DIR__.'/../version.json'),true)['history']['lastModified']?></b></p>
<p class="backdropped" style="width:30%">
The yearly release stage of a new <a href="https://github.com/rene-ajm-veerman/NicerApp-WebOS-5.10.z">stable version</a> of NicerApp WebOS is always Dec 1st - Dec 24th.<br/>
</p>

<ol class="todoList" style="width:50%; width:content-width;">
    <li class="todoList"><div>Yields calculated in real time now for <a href="https://nicer.app/fusion/Grok-by-xai-and-Elon-Musk/tokamak-yields-v2.1.1.html" class="nomod noPushState" target="groksTokamak">nuclear fusion power plant designs</a>! With <a href="https://grok.com/share/c2hhcmQtMw_f2621998-1b7b-4d6f-81e0-993a6de5524e" class="nomod noPushState" target="grokFusionPowerPlantsDiscussion">assistance</a> by <a href="https://grok.com" class="nomod noPushState" target="grokHomepage">Grok.com</a> from <a href="https://x.ai" class="nomod noPushState" target="xaiHomepage">x.ai</a> (see also <a href="https://x.com/xai" class="nomod noPushState" target="xaiHomepageOnXdotCom">x.com/xai</a>).<br/>(C) + (R) 2026 by Rene AJM Veerman, building upon general progress in the science and engineering fields. I do not want to cheat anyone out of their legitimate own paychecks though! :-)</div></li>

    <li class="todoList"><div>(2026) Implemented automated screenshot fetching for all comments on any page of any NicerApp domain.</div></li>

    <li class="todoList"><div>(2026-2028/2029) Start on .../logic.databases/uDB-2.0.0, to be based on .../logic.databases/generalizedDatabasesAPI-1.0.0<br/>
        <a href="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-uDB.png" class="nomod noPushState" target="tl-udb-1"><img src="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-uDB.png" style="width:320px"/></a>
        <a href="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-taskManager.png" class="nomod noPushState" target="tl-udb-1"><img src="/NicerAppWebOS/documentation/__README__documentation/v5.10.z/v5.10.z-taskManager.png" style="width:320px"/></a>
        <ol class="todoList_l1">
            <li class="todoList_l1"><div>Add FilesystemDB (by Rene AJM Veerman), MySQL, and PostgreSQL to the list of supported database architectures (via .../NicerAppWebOS/3rd-party/adodb5), currently only couchdb is supported.</div></li>
            <li class="todoList_l1"><div>Add query routines in uDB to translate couchdb index names to index ids.</div></li>
            <li class="todoList_l1"><div>Add and test MySQL, PostgreSQL and 'filesystemDB' (fsDB) sub-modules to uDB <b>(also on Window 11)</b>, as well as their interactions with the CouchDB part of uDB.</div></li>
            <li class="todoList_l1"><div>Start on .../businessLogic/dataPhoenix.
                <ol class="todoList_l2">
                <li class="todoList_l2"><div>(2026) Add a seperate, PHP based, database integrity check script, for the entire WebOS.</div></li>
                <li class="todoList_l2"><div>(2026-April&amp;May) Fix the very few remaining bugs in said.by/me document handling.</div></li>
                </ol>
            </div></li>

            <li class="todoList_l1"><div>Start on .../businessLogic/class.diaries.2.y.z.php.</div></li>
            <li class="todoList_l1"><div>Start on .../businessLogic/regionalCloud.</div></li>
            </ol>
    </div></li>

    <li class="todoList"><div>(2026) Add database based caching of wikipedia.org output for /wiki on nicer.app (fairly easy to implement these days actually).</div></li>
    <li class="todoList"><div>(2026) Complete a <a href="/3D">3D folder structure viewer</a>, which will be linked to all sorts of 2D user interface components/widgets.</div></li>

    <li class="todoList"><div>(2026) Allow log entries (of console.log in the browser, and all database traffic and error messages on the server) to be logged to a different machine on the LAN than the main server.</div></li>

    <li class="todoList"><div>(2026) Get na.m.log() to log per datetimeRangeBegin.milliseconds to datetimeRangeEnd.milliseconds into a database on the server, for use in /view/logs as the PHP data belonging to a PHP session_id().<br/>
    Status : Completed javascript code, now needs revamped generalizedDatabaseAPI written and called, and db businesslogic PHP code.</div></li>

    <li class="todoList"><div>(2027) Start work on a self-healer component for this WebOS, and a lot more Desktop OS level automated security to be installed via the <a href="https://github.com/rene-ajm-veerman/nicerApp-WebOS-5.10.z/tree/main/NicerAppWebOS/scripts.install" target="mainBashInstallScript_for_NicerAppWebOS" class="nomod noPushState contentSectionTitle3_a"><span class="contentSectionTitle3_span">main Bash install script</span></a>.</div></li>

    <li class="todoList"><div>(2027) Write forum- and hashtag-features.</div></li>

    <li class="todoList"><div>(2027) Rewrite the help system and comments system.
        <ol class="todoList_l1">
            <li class="todoList_l1"><div>Write a notification API based on the VAPID browser API for this WebOS.</div></li>

            <li class="todoList_l1"><div>Write an in-app tutorial API for this WebOS.</div></li>
        </ol>
    </div></li>

    <li class="todoList"><div>Add the next set of animations to .todoList p, span, h1, h2, and h3 HTML elements (config data for which is to be visualized and interacted with in the theme editor by the way).<br/>
    These will be javascript + PHP generated visualizations for SVG (animated) graphics.<br/>
    I promise to bring you all some level of CSS animations in the generation config language :)<br/>
    Maybe even a real translation of CSS animations (they're easy to parse), into SVG color and transparency animations! :D</div></li>

    <li class="todoList"><div>Create a donations button (again, in collaboration with paypal.com), with monthly goal indicator, for the news app.</div></li>

    <li class="todoList"><div>Implement bandwidth throttling in the <a href="https://github.com/NicerEnterprises/NicerApp-WebOS/tree/main/NicerAppWebOS/scripts.install" target="mainBashInstallScript_for_NicerAppWebOS" class="nomod noPushState">main Bash install script</a>.</div></li>

    <li class="todoList"><div>Add a checkbox in the Theme Editor to select backgrounds and stretch instead of tile them for any DIV.</div></li>

    <!--<li class="todoList"><div>Restore the automatic retrieval of new backgrounds download routines for nicerapp via free to use methods of delivery at Google image search and (TODO :)Bing image search.</div></li>-->

    <li class="todoList"><div>(2027) Upgrade the news app and vividDialog : add siteToolbarLeft functionality :<br/>
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

    <li class="todoList"><div>
        Build a webshop module
        <ol class="todoList_l1">
        <li class="todoList_l1"><div>Integration of payment platforms (as plugins) for paypal.com, creditcards, and the Dutch banking system iDeal.</div></li>
        </ol>
    </div></li>
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
