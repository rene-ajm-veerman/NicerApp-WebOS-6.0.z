var zat = {
    globals : {},
    settings : {},
    
    onload : function () {
        $('#results').hide(5);
        
        setTimeout(function() {
            $('#offer_'+$('#planName').val()).show(5);
        }, 250);
        
        if (
            $('#redirectOrNot')[0]
            && $('#manualRedirectSetting').html()==='false'
        ) zat.startCountdown(); else $('#redirectOrNot, #countdown, #btnCancelRedirect').hide(5);
        
        zat.initializeSignup();
    },
    
    initializeSignup : function () {
        $('.tooltipLinks').each(function(idx,el){
            el.title = 'Total number of URLs that can get translated for you by https://zoned.at';
        });
        $('.tooltipRedirects').each(function(idx,el){
            el.title = 'Total number of times your shortened links can get redirected to your chosen destination for such a link, per month.';
        });
        $('.tooltipChange').each(function(idx,el){
            el.title = 'Whether or not (and if so, how) the URL redirected to can be changed.';
        });
        $('.tooltipCustom').each(function(idx,el){
            el.title = 'Whether or not (and if so, how many) you can use https://zoned.at/yourBrandLink to point to a destination, instead of https://zoned.at/cjd8J';
        });
        $('.tooltipDelay').each(function(idx,el){
            el.title = 'Whether or not (and if so, for how long) there will be a forced delay before the user is redirected to the destination.';
        });
        $('.tooltipPrice').each(function(idx,el){
            el.title = 'I will keep all redirection data that you entered into this site safe at all times.';
        });

        
        $('.tooltip').tooltipster({
            theme : 'siteMainTooltipsterTheme',
            animation : 'grow',
        });
        
        /*
        $('.offerBefore').each(function(idx,el){
            $(el).css({ padding : 10, width : $('.flex-title').width(), height : $(el).parent('div.offer').height() });
        });
        $('div.offer').css({ padding : 10, margin : 25 }).mouseover(function(){
            if (!$(this).is('.anim')) $(this).addClass('anim');
        });
        $('div.offer').hover(function() {
            $(this).removeClass('offer').addClass('offerHover');
            if (!$(this).is('.freeOffer')) $('h3',this).prepend('Buy '); else $('h3',this).prepend('Use ');
            
        }, function() {
            $(this).addClass('offer').removeClass('offerHover');
            $('h3',this).each(function(idx,el){ el.innerHTML = el.innerHTML.replace('Buy ','').replace('Use ',''); });
        });*/
    },
    
    onchangeTimespanMin : function () {
        var elMin = $('#timespanMin');
        if (elMin.val()==0) {
            $('#timespanSec').attr('min', 10).val(10);
        } else {
            $('#timespanSec').attr('min', 0).val(0);
        }
    },
    
    onchangeManualRedirect : function () {
        if ($('#manualRedirect').prop('checked')) {
            $('#timespan').hide(500);
        } else {
            $('#timespan').show(500);
        };
    },
    
    shorten : function () {
        if (!zat.settings.shorteningInProgress) {
            event.preventDefault();
            zat.settings.shorteningInProgress = true;
            if ($('#urlToShorten').val().match(/</)) {
                alert ('We don\'t allow HTML in URLs to shorten! :p');
                return false;
            }
            var ac = {
                type : 'POST',
                url : 'ajax_shorten.php',
                data : {
                    urlToShorten : $('#urlToShorten').val(),
                    timespanMin : $('#timespanMin').val(),
                    timespanSec : $('#timespanSec').val(),
                    manualRedirect : $('#manualRedirect').prop('checked') ? true : false
                },
                success : function (data, ts, xhr) {
                    zat.settings.shorteningInProgress = false;
                    var d = JSON.parse(data);
                    if (d) {
                        $('#shortened').val('https://zoned.at/'+d.shortened);
                        $('#results').slideDown('slow');
                    } else {
                        alert ('That\'s not a valid URL, sorry.');
                    };
                },
                failure : function (xhr, ajaxOptions, thrownError) {
                    zat.log ({
                        msg : '[FATAL ERROR] zat.source.js:zat.shorten() failed.',
                        dbg : {
                            thrownError : thrownError,
                            shortened : $('#urlToShorten').html()
                        }
                    });
                    alert ('Oops! something went wrong. Apologies.\Tnhis failure was logged and will be fixed soon.');
                }
            }
            $.ajax(ac);
        }
    },
    
    copyShortenedToClipboard : function () {
        event.preventDefault();
        var copyText = $('#shortened')[0];
        
        copyText.select();
        copyText.setSelectionRange(0, 99999); /* For mobile devices */

        /* Copy the text inside the text field */
        document.execCommand("copy");
    },
    
    gotoFrontpage : function () {
        document.location.href = '/';
    },
    
    gotoExplanationsPage : function () {
        document.location.href = '/explanation';
    },
    
    gotoPricingPage : function () {
        document.location.href = '/pricing';
    },
    
    startCountdown : function (seconds) {
        zat.countdownCounterMin = $('#countdownTimespanMin').html();
        zat.countdownCounterSec = $('#countdownTimespanSec').html();
        clearInterval (zat.countdownInterval);
        zat.countdownInterval = setInterval (function() {
            zat.countdownCounterSec--;
            if (zat.countdownCounterSec===0) {
                if (zat.countdownCounterMin > 0) {
                    zat.countdownCounterMin--;
                    zat.countdownCounterSec = 59;
                } else {
                    zat.redirectNow();
                }
            };
            $('#countdownTimespanMin').html (zat.countdownCounterMin);
            $('#countdownTimespanSec').html (zat.countdownCounterSec);
        }, 1000);
    },
    
    redirectNow : function () {
        var ac = {
            type : 'POST',
            url : 'ajax_redirect.php',
            data : {
                shortened : $('.shortened').html()
            },
            success : function (data, ts, xhr) {
                debugger;
                var url = $('.destinationAddress').html();//.replace('To : ', '');
                document.location.href = url;
            },
            failure : function (xhr, ajaxOptions, thrownError) {
                zat.log ({
                    msg : '[FATAL ERROR] zat.source.js:zat.redirectNow() failed.',
                    dbg : {
                        thrownError : thrownError,
                        shortened : $('.shortened').html()
                    }
                });
                alert ('Oops! something went wrong. Apologies.<br/>This failure was logged and will be fixed soon.');
            }
        };
        $.ajax(ac);
    },
    
    cancelRedirect : function () {
        event.preventDefault();
        clearInterval (zat.countdownInterval);
        $('#redirectOrNot').html('NOT redirecting you (at your request)').css({background:'red', color : 'yellow', borderRadius:5, padding : 3});
    },
    
    formSignup_plan_change : function (plan) {
        var 
        planName = $(plan).val(),
        shown = false;
        $('.offer').not(':hidden').slideUp(500, function () {
            if (!shown) {
                shown = true;
                $('#offer_'+planName).slideDown(500);            
            }
        });
        if (true) {
            if (planName=='free') {
                if ($('#payment_comingSoon').css('display')!=='none') $('#payment_comingSoon').slideUp(500);
            } else {
                if ($('#payment_comingSoon').css('display')=='none') $('#payment_comingSoon').slideDown(500);
            }
        }
        if (false) {
            if (planName=='free') {
                if ($('#payment_creditcard').css('display')!=='none') $('#payment_creditcard').slideUp(500);
            } else {
                if ($('#payment_creditcard').css('display')=='none') $('#payment_creditcard').slideDown(500);
            }
        }
    },
    
    buy : function (plan) {
        event.preventDefault();
        document.location.href = '/buy-'+plan;
    },
    
    log : function (data) {
        data._id = (new Date).getTime();
        data.clientIP = zat.globals.clientIP;
        var
        ac = {
            type : 'POST',
            url : 'ajax_log.php',
            data : { 
                json : JSON.stringify(data)
            },
            success : function (data, ts, xhr) {
            },
            error : function (xhr, ajaxOptions, thrownError) {
                debugger;
            }
        };
        $.ajax (ac);
    }    
};

