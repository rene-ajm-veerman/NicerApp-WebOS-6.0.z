<?php
    global $naWebOS;
    require_once ($naWebOS->webPath.'/../domains/'.$naWebOS->domainFolder.'/domainConfig/pageHeader.php');
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
        <h2>The Online Diary of Rene AJM Veerman</h2>

        <?=$diaries->getDiary('Rene-AJM-Veerman', $naWebOS->domainPath.'/siteDiaries/Rene-AJM-Veerman/diary', '/siteDiaries/Rene-AJM-Veerman/files/index-print.php');?>

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
