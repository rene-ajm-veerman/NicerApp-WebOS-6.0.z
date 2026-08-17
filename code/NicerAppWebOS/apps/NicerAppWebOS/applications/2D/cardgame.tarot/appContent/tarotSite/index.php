<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once (dirname(__FILE__).'/functions.php');
require_once (realpath(dirname(__FILE__).'/../../../../../../../..').'/NicerAppWebOS/boot.php');
	/*
	global $saSiteHTTP; global $saSiteDomain; global $saSiteRootFolder; global $saFrameworkFolder;
	global $saSiteHD; global $saFrameworkHD; global $saSiteURL; global $saFrameworkURL;
	global $saIsLocalhost; global $saHTDOCShd;
	global $saServerOperatingSystem; global $saDeveloperMode;
	
	global $saUpstreamRootURL; global $locationbarInfo;
	global $saUIdefaults;
	//$viewParams = getLocationbarInfo($_SERVER['QUERY_STRING']);
	*/

	global $naWebOS;
	$view = $naWebOS->view;
	//echo '<pre>';var_dump($view); echo '</pre>'; exit;
	foreach ($view as $viewPath => $viewRec) {
		foreach ($viewRec as $appName => $appRec) {
			//echo '<pre>';var_dump($appRec); echo '</pre>';
			if (is_array ($appRec)) {
				$_GET['deck'] = $appRec['deck'];
				try {
					if (
						is_array($appRec)
						&& array_key_exists('reading', $appRec)
						&& is_string($appRec['reading'])
					) {
						$x = json_decode($appRec['reading'], true);
						if (is_array($x)) $appRec = $x;
					}
				} catch (Exception $e) {};
				$_GET['reading'] = (
					is_array($appRec)
					&& array_key_exists('reading', $appRec)
					&& is_string($appRec['reading'])
					? $appRec['reading']
					: (
						is_array($appRec)
						&& array_key_exists('reading', $appRec)
						&& is_array($appRec['reading'])
						? $appRec['reading']['path']
						: '3 Cards'
					)
				);
			} else {
				$_GET['deck'] = $appRec;
				$_GET['reading'] = '3 Cards';
			}
		}
	}
	//echo '<pre>'; var_dump ($_GET); echo '</pre>'; exit();

	/*
	$url = $_SERVER['REQUEST_URI'];
	//var_dump ($url); 
	$deckp1 = strpos($url, 'deck\'');
	$deckp2 = strpos($url, '\'', $deckp1+strlen('deck\''));
	$deck = substr($url, $deckp1+strlen('deck\''), $deckp2-$deckp1-strlen('deck\''));
	$readingp1 = strpos($url, ',reading\'');
	$readingp2 = strpos($url, '\'', $readingp1+strlen(',reading\''));
	$readingName = substr($url, $readingp1+strlen(',reading\''), $readingp2-$readingp1-strlen(',reading\''));
	//var_dump ($readingName); exit();
	$_GET['deck'] = $deck;
	$_GET['reading'] = $readingName;
	*/
	//var_dump ($_GET); exit();
	$explanationID = 'tarot';
	
	//var_dump ($viewParams); exit;
	//echo '<pre>'; var_dump ($_GET); echo '</pre>'; exit();
	$readingType = t2_get_readingType($_GET['reading']);
	//var_dump ($readingType); exit();
	$reading = t2_draw_cards($readingType, $explanationID);
	//echo '<pre>';var_dump ($reading); exit();
	$reading = t2_calculate_size_for_cards ($reading); 
	//var_dump ($reading); exit();
	$numberOfDecks = t2_getNumberOfDecks();
	$numberOfReadings = t2_getNumberOfReadings();
	//exit();

//	<!--<meta name="keywords" content="tarot, free, free tarot, free tarot reading, psychic, play, anation, consult, future, prediction, tarot consult, tarot reading, tarot game, tarot deck, tarot decks, tarot set, game, cards, card game, angel card">-->

?>
	<link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/index.css?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/index.css'));?>">
	<script type="text/javascript">
			delete na.apps.loaded['app.2D.cardgame.tarot'];
			na.m.waitForCondition ('na.apps.loaded["/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot"] ?',
			function () { return (typeof na === 'object'); },
			function () {
				//debugger;
				//na.analytics.logMetaEvent ('tarot : init-stage-1');
				//game.onresize('#siteContent');
				$('head').append('<script type="text/javascript" src="/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/app.2D.cardgame.tarot_siteContent.source.js?changed=<?php echo date('Ymd-His', filemtime(dirname(__FILE__).'/app.2D.cardgame.tarot_siteContent.source.js'));?>"/>');
				na.m.waitForCondition ('game js loaded?', function () {
					var game = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'];
					return typeof game == 'object' && game !== null && game !== undefined
				}, function () {
					var game = na.apps.loaded['/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot'];
					game.globals.rootURL = 'http://nicer.app/';
					game.globals.request_uri = "<?php echo $_SERVER['REQUEST_URI']?>";
					game.globals.url = "<?php echo str_replace('content/app.2D.cardgame.tarot/','', $_SERVER['REQUEST_URI'])?>";
					game.settings.reading = <?php echo json_encode($reading); ?>;
					//na.apps.loaded['app.2D.cardgame.tarot'].settings.ready = true;
					game.settings.loadedIn['#siteContent'].settings.ready = true;
					na.site.settings.startingApps = false;
				}, 200);
			},100);
	</script>


	<div id="saZoomCard" style="display:none;z-index:10000000;text-align:center;"></div>
	
	<div id="app_mainmenu" class="vividMenu vividTheme__menu_006 saAppMenu saAppMenu_defaultDiv__siteContent" style="position:absolute; visibility:hidden; left:-420px; top:10px; width:400px; height:37px; z-index:990000">
		<ul style="display:none">
            <li><a class="menu__dontKeepSelected" href="#">Tarot</a>
                <ul>
                    <li><a class="menu__dontKeepSelected" href="#">Card Decks</a>
                        <ul>
                        <?php //echo t2_html_menu_decks(); ?>
                        </ul>
                    </li>
                    <li><a class="menu__dontKeepSelected" href="#">Readings</a>
                        <ul>
                            <?php //echo t2_html_menu_readings(); ?>
                        </ul>
                    </li>
                    <li><a href="#">Misc</a>
                        <ul>
                            <li><a href="javascript:na.apps.loaded['app.2D.cardgame.tarot'].setOptions('majors',true);">Major cards only</a></li>
                            <li><a href="javascript:na.apps.loaded['app.2D.cardgame.tarot'].setOptions('majors',false);">All cards</a></li>
                            <li><a href="javascript:na.apps.loaded['app.2D.cardgame.tarot'].getNewReading();">New Reading</a></li>
                        </ul>
                    </li>
                </ul>
            </li>
		</ul>
	</div>
	
	<div id="appGame" style="display:none;width:100%;height:100%;overflow:hidden;">
		<?php //getBadDecks(); ?>
		<div id="card" style="position:relative;float:right;display:none;"></div>
		<div id="cards" style="position:relative;float:right;display:none;overflow:hidden;margin:30px;height:<?php echo $reading['theme']['size']['y']?>px;width:<?php echo $reading['theme']['size']['x']?>px;">
			<?php echo t2_html_draw_cards($reading); ?>
		</div>
		<div id="intro" class="text" style="display:none;">
			<h1 class="nicerEnterprises_tarot_cardExplanation" id="pageTitle" style="text-shadow:2px 2px 1px rgba(0,0,0,0.7)">Free Tarot Reading (191 decks, 9 reading types)</h1>
			<h2>For paid live-interactive card readings look no further than <a href="https://oranum.com/?referal-origin=nicer-dot-app-slash-tarot_looking-for-donation-back-of-around-1000-euros-per-year_see-my-Apps-Documentation-Company-Overview-page-at-nicer-dot-app-for-my-email-to-acquire-my-streetaddress" target="nicerAppWebOS-oranum">oranum.com</a></h2>
			<script type="text/javascript">
			/*
                setTimeout(function() {
                    var vividTextCmd = {
                        el : jQuery('#pageTitle')[0],
                        theme : na.cg.themes.naColorgradientSchemeMagicalBlue, 
                        animationType : na.vividText.globals.animationTypes[0],
                        animationSpeed : 10 * 1000
                    };
                    na.vividText.initElement (vividTextCmd);	   
                }, 1000);
			*/
			</script>

			
			<p class="backdropped">
			Here you can select any of <?php echo $numberOfDecks;?> tarot decks  and <?php echo $numberOfReadings?> types of readings, to play a game of tarot with for free.<br/>
			Use the menu at the top of this web game to select a different deck or reading type.
			</p>
			
			<p class="backdropped">
			If a card is upside down, that is the common notation for the opposite meaning of the card, also known as the "negative" or "reversed" meaning of the card.
			</p>
			
			<p class="backdropped">The card decks for this game app, i found on a torrent site one day (a lucky day for me), and of course, as with any "stolen" copyrighted material, i will list the DMCA complaint filing address as part of my compliance with copyright laws that govern my country, right here and now :<br/>
			<a href="mailto:rene.veerman.netherlands@gmail.com">E-mail rene.veerman.netherlands@gmail.com</a>
			</p>

			<p>
			This card-game, I believe, links in to communication with the dead (the Gods, Demi-Gods, Angels, Spirits and Demons of Humanity) and even with remote viewers whom I believe are still employed by the governments and intelligence services thereof, of several of the largest countries on Earth (at least).<br/>
			See <a target="telepath-manual-by-rene-ajm-veerman" href="https://said.by/telepathy-manual" class="nomod noPushState">my telepathy-manual</a> for more info.<br/>
			I would kindly also like to point you to <a href="https://x.com/ReneVeerman1977/status/1982064841431887947" class="nomod noPushState" target="grokOnTarotPlusTelepathyTestimonies">this conversation I had with @Grok over on <a href="https://x.com/" class="nomod noPushState" target="xDotCom">x.com</a>, which seems to indicate that scientific evidence of the existence of telepathy is covered up by governments world-wide, and it also points to a huge potential coverup of <a href="https://en.wikipedia.org/wiki/Stargate_Project_(U.S._Army_unit)" class="nomod noPushState" target="ciaIknowWhatImDoingHere-stargateRevealedToPublic">the CIA 'stargate' project</a> that ran during the Cold War 1.
			</p>

			<p>
			The only remaining question is : do I add these capabilities into this tarot cardgame; as a compulsory feature by the way, because you'd have to see how much of the population, and under which IP addresses, is telepathically gifted.<br/>
			<b>2025-10-25 16:10CET</b>: So I am putting this up for a vote. Which will very soon get a database counter. :-) <br/>
			Needless to say, it's a huge honor. And I'm already on a path to developing the tools necessary for this card game to be turned into a scientific research clinic. :-)
			</p>
		</div>
		<div id="deckExplanation" class="text" style="display:none;">
			<?php echo t2_html_deck_info()?>
		</div>
		<div id="cardSpotInReadingTitle" class="text" style="display:none;"></div>
		<div id="cardSpotExplanation" class="text" style="display:none;">
		</div>
		<div id="cardExplanation" class="text" style="display:none;">
			<?php 
			foreach ($reading['cards'] as $idx => $card) {
				//echo '<p>'.$card['readingSpotExplanation'].'</p>'."\n\r";
				echo $card['explanation'];
			}
			?>
		</div>
		<div id="readingExplanation" class="text" style="display:none;">
			<?php echo $reading['heading']; ?>
		</div>
	</div>
</body>
</html>
