<?php
    global $naWebOS;
    require_once ($naWebOS->webPath.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/pageHeader.php');
    $fp = $naWebOS->codePath.'/NicerAppWebOS/businessLogic/class.NicerAppWebOS.diaries.php';
    require_once ($fp);
    $diaries = new naDiaries();
    $diaryName = 'psychiatric-evolution';

    global $naLAN;
    if (
        true
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
        <h2>The case of a male patient in psychiatry (born in May 1977), against psychiatry.Amsterdam.nl (mentrum.nl)</h2>

        <h2>Introduction</h2>
        <p class="backdropped" style="margin-left:50px">
            <b>the "Mighty" Western Government as seen from psychiatric patients' viewpoints.jpg</b><br/>
            <img src="<?='/siteDiaries/'.$diaryName.'/files/the Mighty Western Government as seen from psychiatric patients viewpoints.jpg';?>"/>
        </p>

        <?=$diaries->getDiary($diaryName, $naWebOS->domainPath.'/siteDiaries/'.$diaryName.'/diary', '/siteDiaries/'.$diaryName.'/files/index-print.php');?>

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
