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
            border-radius:10px;
            text-shadow : 0px 0px 2px rgba(0,0,0,0.7), 2px 2px 4px rgba(0,0,0,0.7);
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
            <a href="https://nicer.app" target="naHP">https://nicer.app</a>, <a href="https://said.by" target="sbHP">https://said.by</a>, <a href="https://zoned.at" target="zAt">https://zoned.at</a>, <a href="https://github.com/Rene-AJM-Veerman" target="githubNicerEnterprises">https://github.com/Rene-AJM-Veerman</a>, <br/>in addition to ALL of the content listed at the social media URLs below, <br/>
            are ENTIRELY Copyrighted (C) 2002-2026 and are 100% Owned by <a href="mailto:rene.veerman.netherlands@gmail.com" target="_new" class="nomod noPushState">Rene A.J.M. Veerman &lt;rene.veerman.netherlands@gmail.com&gt;</a>.<br/>
            https://x.com/Gavan1977, https://facebook.com/rene.veerman.90, https://youtube.com/@CheetahKungFu
        </p>

        <h2>Going commercial</h2>

        <p class="backdropped naComments_onTheSide">
        The entire Copyright (C) and All Rights Reserved (R) status of this Software and the domain-names and -content at said.by, zoned.at and nicer.app are on sale for ten million euro.<br/>
        Email me for more details. I'd be willing to, but not insisting on, become/becoming a remote working employee of the buyer too. :-)
        </p>

        <h2>Business plan</h2>

        <p class="backdropped naComments_onTheSide">
        For at least 2026 as well i will keep NicerApp WebOS (https://nicer.app) MIT-licensed open source that can even be used commercially for free, but without warranty and you'll need your own full stack http://kubuntu.com web-development team to work with it.
        </p>


        <h2>Executives</h2>

        <div class="backdropped naComments_onTheSide">
        <div class="backdropped naComments_onTheSide">Owner, Founder, Senior Coder : <a href="https://www.youtube.com/watch?v=nO5KNu-Qwcs" target="naReneMemoires" class="nomod noPushState">Rene A.J.M. Veerman</a><br/>[ rene.veerman.netherlands@gmail.com ]<br/>


        That's a 70W speaker for my smartphone draped around my shoulder,<br/>not a 'gay bag'.<br/>
        i'm straight, but i can not date any military or intelligence women.<br/>
        Both of my parents are Leos in the western zodiac, and therefore i can't be with an overly assertive woman.<br/>
        Sorry, but that's just the way it is.</div>
        <img src="https://nicer.app/NicerAppWebOS/documentation/selfies/rene-ajm-veerman/IMG_20251109_145323_1.jpg" style="width:95%;"/><br/>
        </div>
        <p class="backdropped naComments_onTheSide">
        Should I unexpectedly die for some strange reason, for instance by long standing "dissident" disputes (In addition to a software and graphics developer, i'm also an assertive peace activist who is not without the ability to look at his own ranks with criticism) suddenly becoming lethal in some way, I want my belongings donated to my parents initially, and to the Amsterdam.NL stedelijk museum after their eventual death, who may all do with it all as they please, on condition of keeping copies of https://nicer.app plus https://said.by up and running.<br/>
        After my death, I'd also like my one creditor re-imbursed : 730 Euro to GVB.NL.<br/>
        My protective custody agent's details are well engraved in medical records at https://mentrum.nl<br/>
        </p>


        <h2>Political views</h2>
        <p class="backdropped naComments_onTheSide">
        It is my firm belief that humanity should not allow itself to be ever again goaded into conventional war after conventional war by the ruling classes.
        </p>




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
