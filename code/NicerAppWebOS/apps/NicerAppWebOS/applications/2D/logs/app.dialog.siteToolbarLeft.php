    <div style=\'display:flex;align-items:center;height:100px;max-height:100px;align-items: center;justify-content: space-between\'>
    <script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/naLog.source.js?m=<?=date('YMD-hms')?>"></script>'
    <link rel="StyleSheet" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/naLog.css?m=<?=date('YMD-hms')?>"/>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-timepicker-addon.css">

  <style>
    .container {
      max-width: 500px;
      margin: 20 auto;
      background: rgba(255,255,255,0.555);
      color : navy;
      font-weight:bold;
      padding: 10px;
      border-radius: 20px;
      box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    .ui-datepicker select, .ui-datepicker input, .ui-datepicker a {
        border-radius : 15px;
        padding : 5px;
    }
    .ui-datepicker input {
        display:flex;
        justify-items:center;align-items:center;
    }
    .ui-datepicker td a {
        text-align:center !important;
    }
    .input-group {
      display: flex;
      gap: 12px;
      align-items: center;
    }
    #displayInput {
      flex: 1;
      padding: 12px 16px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 4px;
      background: blue;
      color : white;
      cursor: pointer;
    }
    .ui-slider-horizontal {
        background : rgba(0,0,255,0.45) !important;
      color : yellow !important;
      font-weight : bold !important;
    }
    .ui-slider-handle {
        background : rgba(255,255,255,0.45) !important;
        border : 2px ridge yellow;
    }
    .ui-slider-handle:hover {
        background : rgba(255,255,255,0.7) !important;
        border : 3px ridge yellow;
    }
    .ui-datepicker-calendar a {
        background : rgba(0,0,255,0.45) !important;
      color : white !important;
      font-weight : bold !important;
    }
    .ui-datepicker-calendar a:hover {
      color : gold !important;
      font-weight : bold !important;
    }
    .ui_tpicker_time_input, .ui-datepicker-title select, .ui-datepicker-current-day a {
        background : rgba(255,0,0,0.45) !important;
      color : yellow !important;
      font-weight : bold !important;
    }
    .ui-datepicker, .ui-timepicker-div {
      z-index: 3000000 !important;
      color : white !important;
      font-weight : bold;
      background : none;
    }
    .ui-datepicker:before, .ui-timepicker-div:before {
    content : '';
    position:absolute;
top:0;left:0;width:100%;height:100%;
      background : url(/siteMedia/backgrounds/Tiled/Green/green-rust-1.png) repeat;
      opacity : 0.3 !important;
      z-index : -1;
    }
  </style>
<?php
global $naWebOS;
global $naLAN;
//echo '<pre style=\'color:yellow;background:rgba(0,0,50,0.5);border-radius:10px;margin:10px;\'>'; var_dump ($naWebOS->view); echo '</pre>';

echo $naWebOS->html_vividButton (
    1, 'order:1',

    'btnFontLarge', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    '$(\'.naIPlog_entry\').css({fontSize:\'large\'})',
    '',
    '',

    2, 'Large font',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1a.png',
    'Pngtree—vector add icon_4143871.png',

    '',
    '',

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    3, 'order:2',

    'btnFontNormal', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    '$(\'.naIPlog_entry\').css({fontSize:\'medium\'})',
    '',
    '',

    4, 'Regular font',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow4a.png',
    'tree.png',

    '',
    '',

    null,
    null,
    null
);
echo $naWebOS->html_vividButton (
    3, 'order:3',

    'btnFontSmall', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    '$(\'.naIPlog_entry\').css({fontSize:\'small\'})',
    '',
    '',

    4, 'Small font',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.orange1c.png',
    'Pngtree—snowman with shopping bag 3d_5525539.png',

    '',
    '',

    null,
    null,
    null
);
echo '<br/>'.PHP_EOL;
echo $naWebOS->html_vividButton (
    1, 'order:6',

    'pickBeginDateTime', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'showDateTimePicker(\'#beginDateTime\');',
    '',
    '',

    2, 'Pick begin date-time.',

      'btnCssVividButton_outerBorder.png',
      'btnCssVividButton.png',
      'btnCssVividButton.yellow1a.png',
      'pngtree-calendar-3d-icon-isolated-on-a-transparent-background-symbolizing-schedules-and-png-image_20358144.png',

    '',
    '',

    null,
    null,
    null
);
?>
<input type="text" id="beginDateTime" style="order:7;position:absolute;z-index:-70;opacity:0;pointer-events:none;">
<?php
echo $naWebOS->html_vividButton (
    1, 'order:8',

    'pickEndDateTime', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'showDateTimePicker(\'#endDateTime\');',
    '',
    '',

    2, 'Pick end date-time.',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1a.png',
    'calendar.png',

    '',
    '',

    null,
    null,
    null
);
?>
<input type="text" id="endDateTime" style="order:9;position:absolute;z-index:-70;opacity:0;pointer-events:none;">
<?php
echo $naWebOS->html_vividButton (
    1, 'order:8',

    'btnReloadData', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
    '',
    'reloadData();',
    '',
    '',

    2, 'Reload data',

    'btnCssVividButton_outerBorder.png',
    'btnCssVividButton.png',
    'btnCssVividButton.yellow1a.png',
    'btnReload_blueVector.png',

    '',
    '',

    null,
    null,
    null
);
foreach ($naWebOS->view as $afn => $as) break;
$naWebOS->view[$afn]['beginDateTime'] = safeHTTPinput ('beginDateTime', (time() * 1000) - (7 * 24 * 3600 * 1000));
$naWebOS->view[$afn]['endDateTime'] = safeHTTPinput ('endDateTime');
?>
<br/>
<?php
echo $naWebOS->html_vividButton (
  1, 'order:11',

  'btnShowTab_stats', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
  '', 'naLog.view(naLog.data,\'stats\');', '', '',

  11, 'Statistics',

  'btnCssVividButton_outerBorder.png',
  'btnCssVividButton.png',
  'btnCssVividButton.purple1a.png',
  'btnStatistics.png',

  '',
  '',

  null,
  null,
  null
);
echo $naWebOS->html_vividButton (
  1, 'order:12',

  'btnShowTab_mostVisited', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
  '', 'naLog.view(naLog.data,\'mostVisited\');', '', '',

  12, 'Most Visited',

  'btnCssVividButton_outerBorder.png',
  'btnCssVividButton.png',
  'btnCssVividButton.purple1a.png',
  'btnMostVisited.png',

  '',
  '',

  null,
  null,
  null
);
echo $naWebOS->html_vividButton (
  1, 'order:13',

  'btnShowTab_details', 'vividButton_icon_50x50 grouped', '_50x50', 'grouped',
  '', 'naLog.view(naLog.data,\'details\');', '', '',

  13, 'Details',

  'btnCssVividButton_outerBorder.png',
  'btnCssVividButton.png',
  'btnCssVividButton.purple1a.png',
  'btnDetails.png',

  '',
  '',

  null,
  null,
  null
);
?>
<input type="hidden" id="unixMs" value="<?=$naWebOS->view[$afn]['beginDateTime']?>">
<input type="hidden" id="unixMs2" value="<?=$naWebOS->view[$afn]['endDateTime']?>">
<script src="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-timepicker-addon.js"></script>
<script>
    $('#siteToolbarLeft').addClass('naNoSettings').addClass('naNoComments').css({width:70});

  var $hidden = $('');
  var $hidden2 = $('<input type="text" id="endDateTime2" style="position:absolute;z-index:-70;opacity:0;pointer-events:none;">');
  $('body').append($hidden).append($hidden2);
  $hidden = $('#beginDateTime');
  $hidden2 = $('#endDateTime');
  var naDateTimePicker_yearRange = '1971:2055';

  $hidden.datetimepicker({
    dateFormat: 'yy-mm-dd',
    timeFormat: 'HH:mm:ss',
    controlType: 'slider',
    stepMinute: 1,
    changeMonth: true,
    changeYear: true,
    yearRange: naDateTimePicker_yearRange,
    beforeShow: function(input, inst) {
      const btnPos = $('#pickBeginDateTime').offset();
      inst.dpDiv.css({
        top: btnPos.top + $('#pickBeginDateTime').outerHeight() + 5,
        left: btnPos.left
      }).delay(500);
      $hidden.detach().appendTo('body');
    },
    onSelect: function(dateText) {
      const dt = $hidden.datetimepicker('getDate');
      if (dt) {
        const ms = dt.getTime();
        $('#displayInput').val(dateText);
        $('#unixMs').val(ms);
        $hidden.datetimepicker('hide');
      }
    }
  });
  $hidden2.datetimepicker({
    dateFormat: 'yy-mm-dd',
    timeFormat: 'HH:mm:ss',
    controlType: 'slider',
    stepMinute: 1,
    changeMonth: true,
    changeYear: true,
    yearRange: naDateTimePicker_yearRange,
    beforeShow: function(input, inst) {
      const btnPos = $('#pickEndDateTime').offset();
      inst.dpDiv.css({
        top: btnPos.top + $('#pickEndDateTime').outerHeight() + 5,
        left: btnPos.left
      }).delay(500);;
      $hidden2.detach().appendTo('body');
    },
    onSelect: function(dateText) {
      const dt = $hidden2.datetimepicker('getDate');
      if (dt) {
        const ms = dt.getTime();
        $('#displayInput').val(dateText);
        $('#unixMs2').val(ms);

        $hidden.datetimepicker('hide');
      }
    }
  });


  // Button click → toggle picker
  var showDateTimePicker = function (htmlID) {
      switch (htmlID) {
          case '#beginDateTime':
                if ($('#ui-datepicker-div').is(':visible')) {
                    $hidden.datetimepicker('hide');
                    $('#siteToolbarLeft .vividDialogContent').css({scrollLeft:0});
                } else {
                    const current = $('#unixMs').val();
                    if (current) {
                        $hidden.datetimepicker('setDate', new Date(parseInt(current)));
                    } else {
                        $hidden.datetimepicker('setDate', new Date()); // default to now
                    }
                    $hidden.datetimepicker('show');
                }

               break;
          case '#endDateTime':
                if ($('#ui-datepicker-div').is(':visible')) {
                    $hidden2.datetimepicker('hide');
                    $('#siteToolbarLeft .vividDialogContent').css({scrollLeft:0});
                } else {
                    const current = $('#unixMs2').val();
                    if (current) {
                        $hidden2.datetimepicker('setDate', new Date(parseInt(current)));
                    } else {
                        $hidden2.datetimepicker('setDate', new Date()); // default to now
                    }
                    $hidden2.datetimepicker('show');
                }
               break;
    }
  };

  var reloadData = function () {
        var
        dt = $hidden.datetimepicker('getDate'),
        dt2 = $hidden2.datetimepicker('getDate'),
        url = '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/logs/app.dialog.siteContent.php',
        ac = {
            type : 'POST',
            data : {
                beginDateTime : dt?dt.getTime():null,
                endDateTime : dt2?dt2.getTime():null,
            },
            success : function (data, ts, xhr) {
                var json = JSON.parse(data);
                $('#siteContent .vividDialogContent').html(json['siteContent']);
            },
            error : function (a, b, errorThrown) {

            }
        };
        $.ajax(ac);
  }
</script>
</div>

