<?php
    require_once (dirname(__FILE__).'/../../NicerAppWebOS/boot.php');
    global $naWebOS;
    global $naDate;
    require_once ($naWebOS->codePath.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.diaries.php');
    $diaries = new naDiaries();
?>
<script type="text/javascript" src="/NicerAppWebOS/3rd-party/jQuery/jquery-3.7.0.min.js?c=20250817_120652"></script>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<div style="background:rgba(0,0,50,0.007)">
    <style>
    body {
        background : url(/siteMedia/backgrounds/Tiled/Grey/background.jpg);
    }
    </style>
    <link type="text/css" rel="StyleSheet" href="NicerEnterprises--company--base.css?c=NOW">
    <link type="text/css" rel="StyleSheet" href="NicerEnterprises--company--moods-print.css?c=NOW">


    <div>
    <p class="backdropped naComments_onTheSide">
    <a href="https://nicer.app" target="naHP">https://nicer.app</a>, <a href="https://said.by" target="sbHP">https://said.by</a>, <a href="https://zoned.at" target="zAt">https://zoned.at</a>, <a href="https://github.com/Rene-AJM-Veerman" target="githubNicerEnterprises">https://github.com/Rene-AJM-Veerman</a>, <br/>in addition to ALL of the content listed at the social media URLs below, <br/>are ENTIRELY
    Copyrighted (C) 2002-2025 and are 100% Owned by <a href="mailto:rene.veerman.netherlands@gmail.com" target="_new" class="nomod noPushState">Rene A.J.M. Veerman &lt;rene.veerman.netherlands@gmail.com&gt;</a>.
    </p>

    <?php
    global $naWebOS;
    require_once ($naWebOS->webPath.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/pageHeader.php');
    $fp = $naWebOS->codePath.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.diaries.php';
    require_once ($fp);
    $diaries = new naDiaries();
    ?>
    <!--<script type="text/javascript" src="/NicerAppWebOS/3rd-party/jQuery/jquery-3.7.0.min.js?c=20250817_120652"></script>
    <script type="text/javascript" src="/NicerAppWebOS/3rd-party/jQuery/cookie/jquery.cookie.js?c=20250817_120652"></script>
    -->
    <div style="background:rgba(0,0,50,0.007); width:20%;">
    <style>
    p {
        display : block;
    }
    .naComments_onTheSide {
        background:rgba(0,0,0,0.4);
        padding:26px !important;
        margin:10px;
        color : ivory;
        border-radius:10px;
    }
    .naComments_onTheSide img, .naComments_onTheSide li img {
        margin : 20px;
        width : calc(100% - 40px);
        border : 3px solid rgba(0,0,50,0.7);
        box-shadow : 2px 2px 5px 2px rgba(0,0,0,0.8);
        border-radius : 10px;
    }
    </style>
    <link type="text/css" rel="StyleSheet" href="/NicerAppWebOS/documentation/NicerEnterprises--company--base.css?c=NOW">
    <link type="text/css" rel="StyleSheet" href="/NicerAppWebOS/documentation/NicerEnterprises--company--moods-screen.css?c=NOW">

    <h2 class="contentSectionTitle2" style="width:fit-content;padding:10px;border-radius:10px;">Company overview</h2>
    <div>
    <p class="backdropped naComments_onTheSide">
    <a href="https://nicer.app" target="naHP">https://nicer.app</a>, <a href="https://said.by" target="sbHP">https://said.by</a>, <a href="https://zoned.at" target="zAt">https://zoned.at</a>, <a href="https://github.com/Rene-AJM-Veerman" target="githubNicerEnterprises">https://github.com/Rene-AJM-Veerman</a>, <br/>in addition to ALL of the content listed at the social media URLs below, <br/>are ENTIRELY
    Copyrighted (C) 2002-2025 and are 100% Owned by <a href="mailto:rene.veerman.netherlands@gmail.com" target="_new" class="nomod noPushState">Rene A.J.M. Veerman &lt;rene.veerman.netherlands@gmail.com&gt;</a>.
    </p>

    <h2>Going commercial</h2>

    <p class="backdropped naComments_onTheSide">
    The entire Copyright (C) and All Rights Reserved (R) status of this Software and the domain names said.by, zoned.at and nicer.app are on sale for 75 million euro.<br/>
    Email me for more details. I'd be willing to become a remote working employee of the buyer too. :-)
    </p>

    <h2>Business plan</h2>

    <p class="backdropped naComments_onTheSide">
    I will keep NicerApp WebOS (https://nicer.app) MIT-licensed open source that can even be used commercially for free for at least 2026 too, but without warranty and you'll need your own full stack http://kubuntu.com web-development team to work with it.
    </p>


    <h2>Executives</h2>

    <div class="backdropped naComments_onTheSide">
    <div class="backdropped naComments_onTheSide">Owner, Founder, Senior Coder : <a href="https://www.youtube.com/watch?v=nO5KNu-Qwcs" target="naReneMemoires" class="nomod noPushState">Rene A.J.M. Veerman</a><br/>[ rene.veerman.netherlands@gmail.com ]<br/>


    That's a 70W speaker for my smartphone draped around my shoulder,<br/>not a 'gay bag'. :p<br/>
    i'm straight!</div>
    <img src="https://nicer.app/NicerAppWebOS/documentation/selfies/rene-ajm-veerman/IMG_20251109_145323_1.jpg" style="width:95%;"/><br/>
    </div>
    <p class="backdropped naComments_onTheSide">
    Should I unexpectedly die for some strange reason, for instance by long standing <!--<a href="https://said.by/Refirenda?pw=inS3rvice0fHumanity">-->"dissident" disputes</a> (In addition to a software and graphics developer, i'm also an assertive peace activist who is not without the ability to look at his own ranks with criticism) suddenly becoming lethal in some way, I want my belongings donated to my parents initially, and to the Amsterdam.NL stedelijk museum after their eventual death, who may all do with it all as they please, on condition of keeping copies of https://nicer.app plus https://said.by up and running.<br/>
    After my death, I'd also like my one creditor re-imbursed : 730 Euro to GVB.NL.<br/>
    My protective custody agent's details are well engraved in medical records at https://mentrum.nl<br/>
    </p>

    <p class="backdropped naComments_onTheSide" style="background:rgba(255,0,0,0.7) !important;">
    Personal history : I was severly mistreated by psychiatry at mentrum.nl, and later at arkin.nl as well, really by all psychiatrists that I've ever encountered, in the form of :</p>

    <ol class="backdropped naComments_onTheSide" style="background:rgba(0,0,0,0.4);padding:10px;margin:10px;border-radius:10px;">
    <li><b>Haldol treatments which caused suicidal thoughts</b>, a treatment which is no doubt suited for psychopathically violent individuals above my weightclass and height</b>, but not for someone like me who has never broken the law (except for a few fine-able offenses).</li>
    <li>The near-complete draining of my savings account as I needed to save my own life from these suicidal thoughts, even after quitting the Haldol -against "doctor's" advice- by embarking on a long trip to Australia.</li>
    <li>Epilepsy.</li>
    <li>Tremors.</li>
    <li>Cramps.</li>
    <li>Moderate obesity.</li>
    <li><img src="https://nicer.app/NicerAppWebOS/documentation/selfies/rene-ajm-veerman/Dilan -VVD.NL- on self-sufficiency while the VVDs WVGGZ law prescribed me protective custody.jpeg"/><br/>While the party I voted for in the last elections (vvd.nl) has taken on a new goal now in Feb 2026 (self-sufficiency for the populace), <b/>I like so many other psychiatric victims have been held down by very draconian 'protective custody' contracts, that get stuck to a patient by their own parents, who are in turn panicked into a quick signing procedure by a shrink. I suspect this to be standard practice.</b></li>
    <li>Constant false accusations, transgressing the slander and libel laws (smaad, laster en zwartmakerij) by my 2 latest shrinks. Sheer false rumors exaggerated and then portrayed as truths about me, to judges, who willingly ignore the constitution and all basic ancient medical law (do no harm to a patient), just to keep me in treatment for probably the rest of my life.</li>
    <li>Legal representation that is at every desk a false hope, and/or an outright (bunch of) traitor(s) to my interests.</li>
    </ol>

    <p class="backdropped naComments_onTheSide">And still, <b>the root cause of my need to visit psychiatric care at all, my stubborn day-night-rythm irregularity problem -which can and has led to confused speech-, wasn't properly addressed by mentrum until about 15 years into my treatment</b>, based on advice that <b>I</b> gave them in a hospital of theirs in Amsterdam North (Boven het IJ ziekenhuis); to not allow caffeinated coffee served during the afternoon and evening.<br/>
    Following this advice at home has not been easy, but I suspect it's the only advice I'll ever need from these people at mentrum.nl
    </p>

    <p class="backdropped naComments_onTheSide">2026-04-08Wed : I have managed to <a href="https://said-by.translate.goog/pe/?seoValue=pe&_x_tr_sl=nl&_x_tr_tl=en&_x_tr_hl=en&_x_tr_pto=wapp">end my strife with mentrum.nl</a>.</p>

    <p class="backdropped naComments_onTheSide">I finally managed to get myself honest, detailed and accurate <a href="https://grok.com/share/c2hhcmQtMw_0cdbb9cc-d089-435b-b9f5-7b824a0ef13d" class="nomod noPushState" target="gk1">legal advice on terminating protective custody [in Dutch]</a>.</p>

    <p class="backdropped naComments_onTheSide"><a href="https://grok.com/c/e9337e91-9720-4533-9c8c-0cb943e5135a?rid=2b26aba8-20ca-47f0-beaa-b865ae7306e6" class="nomod noPushState" target="gk1">credit where credit is due, VVD.nl</a>; you and The Hague and our Kingdom created a stable polite honest enough society. And that dire problem "quality legal advice for the general population" is hereby SOLVED .</p>




    <h2>Political views</h2>
    <p class="backdropped naComments_onTheSide" style="background:rgba(0,255,255,0.4) !important;color:black;">
    I admit my political views are tainted by my time in psychiatry and analyzing mass media news output, which does seem to clearly indicate there is a class-justice system in play in the western world, which I sometimes find disheartening, frustrating and a valid reason for verbal anger.<br/>
    </p>

    <p class="backdropped naComments_onTheSide">
    But the road ahead seems clear; use that same class justice system and existing elites' bureaucracies, along with persistent and smart but also strictly peaceful forms of demonstrations, to <b>install online-referendums-about-everything style governments</b> in most or all western countries, and possibly some tag-along countries as well.<br/>
    </p>

    <p class="backdropped naComments_onTheSide">
    It is my firm belief that humanity should not allow itself to be ever again goaded into conventional war after conventional war by the ruling classes.
    </p>


    <?php
    /*
     *        <h2>Rene AJM Veerman's personal diaries</h2>
     *            <?php //echo $diaries->getDiary('Rene AJM Veerman');?>
     *            <?php echo $diaries->getDiary('aivd.nl.juniorJudge-Gavan.PURAD.Hoverswell');?>
     *            <h3></h3>
     *            <iframe style="margin:40px;border:1px solid silver;background:rgba(0,0,255,0.555);width:calc(100% - 80px);height:1080px;" src="https://said.by/Rene/on/frontpage"></iframe>
     */
    ?>


    </div>
    </div>
    <script type="text/javascript">
    $('.naDiaryWebPage p').addClass('backdropped');
    $('.naDiaryDaySegmentHeader').each(function(idx,el){
        var fp = $('.naFilePath',$(el).parent()).html();
        $(el).attr('title', fp);
    });
    $('.naDiaryEntryHeader').each(function(idx,el){
        var fp = $('.naFilePath',$(el).parent()).html();
        $(el).attr('title', fp);
    });
    $('.naDiaryDayHeader')
    .on('click', function (evt) {
        var pn = $(evt.currentTarget).next()[0];
        debugger;
        while ($(pn).is('.naDiaryEntry,.naDiaryDay,.naDiaryDaySegment')) {
            if (!$(evt.currentTarget).is('.shown')) {
                $('.naFilePath,ol,ul,.naDiaryEntry,.naDiaryDay,.naDiaryDaySegment',pn).add(pn).hide('slow');
            } else {
                $('.naFilePath,ol,ul,.naDiaryEntry,.naDiaryDay,.naDiaryDaySegment',pn).add(pn).show('slow');
            }
            pn = $(pn).next()[0];
        }
        if ($(evt.currentTarget).is('.shown')) {
            $(evt.currentTarget).removeClass('shown');
        } else {
            $(evt.currentTarget).addClass('shown');
        }
    });
    $('.naDiaryDaySegmentHeader')
    .on('click', function (evt) {
        var pn = $(evt.currentTarget).next()[0];
        debugger;
        while ($(pn).is('.naDiaryEntry,.naDiaryDay,.naDiaryDaySegment')) {
            if (!$(evt.currentTarget).is('.shown')) {
                $('.naFilePath,ol,ul,.naDiaryEntry,.naDiaryDay,.naDiaryDaySegment',pn).add(pn).hide('slow');
            } else {
                $('.naFilePath,ol,ul,.naDiaryEntry,.naDiaryDay,.naDiaryDaySegment',pn).add(pn).show('slow');
            }
            pn = $(pn).next()[0];
        }
        if ($(evt.currentTarget).is('.shown')) {
            $(evt.currentTarget).removeClass('shown');
        } else {
            $(evt.currentTarget).addClass('shown');
        }
    });
    $('.naDiaryDaySegmentHeader, .naDiaryDayHeader').css({cursor:'hand'}).removeClass('todoList').removeClass('active').addClass('shown');
    $('.naDiaryDaySegment, .naDiaryEntry').hide();
    </script>
