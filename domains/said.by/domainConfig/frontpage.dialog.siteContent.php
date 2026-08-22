<meta name="description" content="Take a video or (tiled) background of any resolution, and project information onto that using any weblanguage you want. Written in a style simple enough for children to learn from.">
<?php
    require_once(dirname(__FILE__).'/../NicerAppWebOS/boot.php');
    global $naWebOS;
    global $naURLs;
    //$src = $naWebOS->codePath.'/../../domains/'.$naWebOS->domainFolder.'/domainConfig/pageHeader.php'; echo $src; var_dump(file_exists($src)); exit();
    require_once ($naWebOS->codePath.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/pageHeader.php');
?>
    <script type="text/javascript">
        //delete na.site.settings.current.app;
        //na.m.waitForCondition ('frontPage.dialog.siteContent.php : desktopIdle?', na.m.desktopIdle, function() {
            //na.d.s.visibleDivs.push('#siteToolbarLeft');
            //na.d.s.visibleDivs.push('#siteToolbarRight');
            //na.d.s.visibleDivs.push('#siteToolbarTop');
            //na.desktop.resize();
       // }, 100);
       const onload = function() {
           na.d.s.visibleDivs.push ('#siteToolbarRight');
           $('#siteToolbarRight').css({width:600});
           na.d.resize();
       };
        $(document).ready(onload);
        onload();
    </script>

    <!--
                    <div style="max-width:1000px;margin:8px;padding:8px;border-radius:5px;background:rgba(250, 233, 52,0.35);background:rgba(0,0,250,0.47);box-shadow:0px 0px 8px 4px rgba(0,0,0,0.631), 2px 2px 2px rgba(0,0,0,0.7);line-height:1.5em;">
                        <blockquote style="background:rgba(0,0,0,0.55);padding:8px;border-radius:5px;margin-block-start:0;margin-block-end:0;margin-inline-start:0;margin-inline-end:0;">
                        </blockquote>
                    </div>
    -->

    <table style="width:100%;margin:20px;"><tr>
    <td style="width:40%;height:100%;vertical-align:top"><iframe src="/NicerAppWebOS/businessLogos/models/angel-book-1.1.0.html" style="transform: scale(2.0);transition: transform 0.5s ease-in-out; transform-origin: top left;border:0px;height:100%;"></iframe></td>
        <td>

            <h2 class="contentSectionTitle2" style="margin-left:40px;"><a href="/me" class="nomod noPushState contentSectionTitle2_a">Cloudhost new rich text documents and photo albums (with zoom capabilities!).</a></h2>
            <h2 class="contentSectionTitle2" style="margin-left:40px;"><a href="javascript:if (!$(this).is('.disabled')) { $('#siteLogin').fadeIn('fast').animate({top:$(window).height()/2-$('#siteLogin').height()/2}); }" class="contentSectionTitle2_a nomod noPushState">Login</a></h2>
            <h2 class="contentSectionTitle2" style="margin-left:40px;"><a href="javascript:if (!$(this).is('.disabled')) { $('#siteLogin').fadeOut('fast').animate({top:-750}); $('#siteRegistration').fadeIn('fast').animate({top:$(window).height()/2-$('#siteRegistration').height()/2});  }" class="contentSectionTitle2_a nomod noPushState">Register</a></h2>



            <p class="backdropped">The publishing interface is in the final stages of it's beta phases; some errors are yet to be ironed out, but I'm actively seeking beta-testers nonetheless.<br/>
            Those that have visited this website in the past and are currently experiencing problems are advised to try logging in as user 'Guest' with password 'Guest' first. You can even host documents under that ID.</p>
            <p class="backdropped">This site partners with <a href="https://youtube.com" class="nomod noPushState" target="youtubeDotCom">YouTube</a> for it's HD and 4K video backgrounds feature.</p>

            <h1 class="contentSectionTitle1">Said.By/myNickname : <a href="https://grok.com/share/c2hhcmQtMw_c976a2cd-4e44-4a68-abf3-b2888930dbe5" class="nomod noPushState">Terms and Conditions</a>.</h1>

            <p class="backdropped">You will not impersonate anyone or any group, organisation, foundation, non-profit, non-state actors, government (officers), or company (representatives), not with your actual ID and not with any of your "nickname IDs" that you can create on this Website.</p>

            <p class="backdropped">You are allowed to export your data at any time of your choosing, or have it emailed to you at a regular interval (a feature which is to be made available before Dec 1st, 2028).</p>

            <p class="backdropped">If needed, the same "Content Collections" that posts and media files can be posted into (as one or more pages each linked to potentially many Comments), can be used to shield (an) audience(s) from such a Content Collection.</p>

            <p class="backdropped">You or your company, whichever is applicable under law, is solely responsible for any and all content you host on this platform, to the fullest extent of the law.<br/>
            You will not engage in any breaches of Freedom of Speech or any other laws that apply to any of the jurisdictions that govern your audience(s).<br/>
            </p>

            <p class="backdropped">You will not offer people money or prizes or investment advice geared to your own investment company/firm/etc on this Website.<br/>
            I kindly refer you to my <a href="https://nicer.app/business-news" class="nomod noPushState" target="naNewsBusiness">business news segment</a> for that.
           </p>

<p class="backdropped">The 'delete comment' button will get disabled soon, now that the view/edit history button is just about finished.</p>

<p class="backdropped">Dutch Freedom of Expression – Key Legal Limits (concise reference)  </p>

<p class="backdropped">No prior censorship (Art. 7 Constitution), but speakers remain responsible under the law.
</p>

<p class="backdropped">Main criminal restrictions (Wetboek van Strafrecht):</p>

<p class="backdropped">Group insult (Art. 137c): Public intentional insult of a group based on race, religion/belief, sexual orientation, or disability. Includes (since Oct 2024) insulting denial, condoning, or gross trivialisation of genocide, crimes against humanity, or war crimes (when judicially established). Max. 1 year (2 years if professional/habitual or joint).
Incitement to hatred/discrimination/violence (Art. 137d): Publicly inciting hatred, discrimination, or violence against persons or property on the same protected grounds. Max. 1–2 years.
Dissemination of prohibited material (Art. 137e): Publicly spreading statements known (or that should reasonably be known) to be insulting or inciting on protected grounds (outside factual reporting). Max. 6 months.
Defamation (smaad, Art. 261): Intentionally attacking someone’s honour/reputation by accusing them of a specific fact with intent to publicise. Max. 6 months (1 year if written/image).
Slander (laster, Art. 262): Defamation while knowing the accusation is false. Max. 2 years.
Simple insult (belediging, Art. 266): Intentional insult not amounting to smaad. Max. 3 months (higher when directed at certain public officials or members of the royal family in their capacity).
Incitement to crime / sedition (opruiing, Arts. 131–132): Publicly urging commission of a criminal offence or violent action against public authority.
Threats (Art. 285 and related provisions).</p>

<p class="backdropped">Other notes:</p>

<p class="backdropped">Specific lèse-majesté largely abolished (2020); royal insults now fall under ordinary insult rules with possible enhancement.
Glorification of terrorism / public support for terrorist organisations: bill submitted to Parliament (mid-2026); not yet in force.
Criticism of ideas, religions, or ideologies as such is generally more protected than attacks on groups of persons.
Civil liability (tort) and platform obligations (e.g. DSA) can apply independently.
Application is contextual; political debate and public-interest speech receive strong protection.
</p>

<p class="backdropped">This is a practical summary only, not legal advice. Verify current law and consult counsel for operational use.</p>
        </td>
    </tr></table>
