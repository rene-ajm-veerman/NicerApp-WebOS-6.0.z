<?php
    global $naWebOS;
    require_once ($naWebOS->domainPath.'/domainConfig/pageHeader.php');
    $fp = $naWebOS->codePath.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.diaries.php';
    require_once ($fp);
    $diaries = new naDiaries();

    global $naLAN;
    if (
        true
        || $naLAN
        || (
            array_key_exists('pw',$_GET)
            && (
                $_GET['pw'] == 'valkyri3'
            )
        )
    ) {
?>
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
        <h2>The Griffin-US-EU files</h2>
        <h3>Introduction</h3>
        <p class="backdropped" style="margin-left:50px;">2026-March : Europe and the US stand before their biggest Cold War yet; multiple potential regional wars, and threat of nuclear war with more and more countries desiring and reaching Mutually Assured Destruction status.<br/>
        https://said.by/guseu?pw=valkyri3<br/>
        </p>
        <p class="backdropped" style="margin-left:50px;">This page is still under construction. It is purposefully delayed until next Thursday 19:00CET to allow government diplomats to do their work.</p>
        </p>
        <h3>Last modified</h3>
        <div class="lm" style="display:flex;flex-wrap:wrap;margin-left:70px">


        <?php
            $path = $naWebOS->domainPath.'/siteDiaries/Griffin-US-EU';
            $files = getFilePathList ($path, true, FILE_FORMATS_code, null, ['file']);
            $m = 0;
            $fi = [];
            foreach ($files as $i => $fp) {
                $m2 = filemtime($fp['realPath']);
                $fi[$m2] = $fp;
            }
            krsort ($fi);
            foreach ($fi as $m => $fp) {
                echo '<span class="backdropped">'.$fp['webPath'].'  : '.date('Y-m-d H:i:s', $m+3600).' CET</span>';
            }
        ?>


        </div>
        <h3>The Griffin</h3>
        <img src="/siteDiaries/Griffin-US-EU/files/griffin.avif" style="margin-left : 50px; width : 500px;"/>
        <p class="backdropped" style="margin-left : 50px;">
        A legendary creature with the body, tail, and hind legs of a lion; the head and wings of an eagle; and sometimes the talons of an eagle.<br/>
        Featured prominently in ancient Greek and Roman mythology, griffins are frequently depicted as guardians of treasure.
        </p>
            <?=$diaries->getDiary('Griffin-EU-US', $naWebOS->domainPath.'/siteDiaries/Griffin-US-EU/diary', '/siteDiaries/Griffin-US-EU/files/index-print.php');?>

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
                if ($(evt.currentTarget).is('.shown')) {
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
                if ($(evt.currentTarget).is('.shown')) {
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
    $('.naDiaryDaySegmentHeader, .naDiaryDayHeader').css({cursor:'hand'}).removeClass('todoList').removeClass('active');
    $('.naDiaryDaySegment, .naDiaryEntry').hide();
</script>
<?php
    }
?>
