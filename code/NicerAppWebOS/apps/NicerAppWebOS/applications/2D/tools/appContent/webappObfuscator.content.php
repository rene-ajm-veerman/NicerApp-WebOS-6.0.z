<html>
<head>
<link type="text/css" rel="StyleSheet" media="screen" href="http://new.localhost/apps/tools/appContent/webappObfuscator.content.css">
<link type="text/css" rel="StyleSheet" media="screen" href="http://seductiveapps.com/apps/tools/appContent/webappObfuscator.content.css">
<link type="text/css" rel="StyleSheet" media="screen" href="http://code.jquery.com/ui/1.11.4/themes/ui-darkness/jquery-ui.css">
<script type="text/javascript" src="http://code.jquery.com/jquery-2.1.4.js"></script>
<script type="text/javascript" src="http://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script type="text/javascript" src="http://lib.localhost/jQuery.jPlayer-2.9.1/dist/jplayer/jquery.jplayer.js"></script> 
<!--<script type="text/javascript" src="http://seductiveapps.com/seductiveapps/lib/jQuery.jPlayer-2.9.1/dist/jplayer/jquery.jplayer.js"></script> -->
</head>
<body style="padding:0px;margin:0px;">


<div id="wo__background" style="position:absolute;z-index:99;width:100%;height:100%;">
	<img src="http://media.localhost/siteMedia/backgrounds/landscape/active/Paradise-Beach-Summer-01-Wallpaper.jpg" style="width:100%;height:100%;"/>
</div>

<div id="wo__content" style="position:absolute;z-index:101;width:100%;height:100%;">
<div id="webappObfuscator" style="width:100%;height:100%">

<div id="wo__title" style="position:absolute;left:2%;width:96%;top:2%;height:4em;background:black;border:2px solid navy;border-radius:5px;opacity:0.5">
					<h1 id="pageTitle" style="color:white;">webappObfuscator : *actual* obfuscation ("encryption") for browser-language source-codes.</h1>
					<script type="text/javascript">
					var vividTextCmd = {
						el : jQuery('#pageTitle')[0],
						theme : sa.cg.themes.saColorgradientScheme_text_005, 
						animationType : sa.vividText.globals.animationTypes[0],
						animationSpeed : 2500
					};
					sa.vividText.initElement (vividTextCmd);	
					</script>
</div>

<form id="dummy" style="width:100%;height:100%">
<table id="holder" cellpadding="4" border="0" style="position:absolute;top:4%;left:4%;width:92%;height:92%;">
	<tr>
		<td>
			<div id="tabs" class="jQueryUI">
				<ul>
					<li><a href="#tabs-a">Home</a></li>
					<li><a href="#tabs-b">License</a></li>
					<li><a href="#tabs-c">To-Do</a></li>
					<li><a href="#tabs-d">Change-log</a></li>
				</ul>
				<div id="tabs-a" class="tabPage">
				<div id="tabHome" class="tabContent" style="overflow-y:scroll;">

					
					<p>
					WebappObfuscator is an opensourced PHP software package (free to be used commercially), that allows you to replace all your own HTML id="", class="" and 
					javascript variable names (identifiers/tokens) with random strings, leaving only the browser-internal identifiers intact.
					</p>
					
					<h2>Demo</h2>
					<p><a class="nomod" target="demoSite" href="http://seductiveapps.com/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite">webappObfuscator__demoSite</a></p>
					<p><a class="nomod" target="obfuscator" href="http://seductiveapps.com/opensourcedBySeductiveApps.com/tools/webappObfuscator/demo_obfuscate.php?n=y">webappObfuscator</a></p>
					
					<h2>Output examples</h2>
					<p>
					HTML : <a class="nomod" target="gh3" href="https://github.com/seductiveapps/webappObfuscator/blob/master/webappObfuscator__demoSite/siteTemplate.tpl">siteTemplate</a>
						-> <a class="nomod" target="gh3_obf" href="https://github.com/seductiveapps/webappObfuscator/blob/master/webappObfuscator__demoSite/public/webappObfuscator__output/html/siteTemplate.html">obfuscated</a>.<br/>
					CSS : <a class="nomod" target="gh2" href="https://github.com/seductiveapps/webappObfuscator/blob/master/webappObfuscator__demoSite/index.css">siteTemplate</a> 
						-> <a class="nomod" target="gh2_obf" href="https://github.com/seductiveapps/webappObfuscator/blob/master/webappObfuscator__demoSite/public/webappObfuscator__output/css/siteTemplate.css">obfuscated</a>.<br/>
					Javascript : <a class="nomod" target="gh1" href="https://github.com/seductiveapps/webappObfuscator/blob/master/webappObfuscator__demoSite/secret-pw-JCn._-SA.LJ/siteLogic/siteCode.source.js">siteTemplate</a>
						-> <a class="nomod" target="gh1_obf" href="https://github.com/seductiveapps/webappObfuscator/blob/master/webappObfuscator__demoSite/public/webappObfuscator__output/javascript/secret-pw-JCn._-SA.LJ/siteLogic/siteCode.source.js">obfuscated</a>.<br/>
					</p>
					
					<h2>Screenshots</h2>
					<table cellspacing="10">
						<tr>
							<td><a class="nomod" target="wo_ss1" href="http://seductiveapps.com/siteMedia/opensourcedBySeductiveApps.com/tools/webappObfuscator/Screenshot%20from%202015-07-03%2002_04_27.png"><img src="http://seductiveapps.com/siteMedia/opensourcedBySeductiveApps.com/tools/webappObfuscator/thumbs/Screenshot%20from%202015-07-03%2002_04_27.png"/></a>
							<td><a class="nomod" target="wo_ss1" href="http://seductiveapps.com/siteMedia/opensourcedBySeductiveApps.com/tools/webappObfuscator/Screenshot%20from%202015-07-03%2002_04_58.png"><img src="http://seductiveapps.com/siteMedia/opensourcedBySeductiveApps.com/tools/webappObfuscator/thumbs/Screenshot%20from%202015-07-03%2002_04_58.png"/></a>
						</tr>
					</table>

					<h2>Downloads</h2>
					<p>
					2015 July 3rd, 01:57 CEST : current release : <a class="nomod" href="http://seductiveapps.byethost14.com/webappObfuscator-20150703_0157CET.zip">webappObfuscator-20150703_0157CET.zip</a>.(now on fast free hosting by <a class="nomod" href="http://byethost.com">byethost.com</a>)<br/>
					<br/>
					<br/>
					NOW UPDATED : Also available at <a class="nomod" target="github" href="https://github.com/seductiveapps/webappObfuscator">https://github.com/seductiveapps/webappObfuscator</a> (might receive less frequent updates).<br/>
					</p>
					
					<p>
					NOTES : delete everything in webappObfuscator/webappObfuscator__demoSite/public/webappObfuscator__output and use webappObfuscator/demo_obfuscate.php?n=y or webappObfuscator/ajax_demo_obfuscate.php?n=y to generate new obfuscated files in that folder.<br/>
					</p>
	
				</div>
				</div>
				
				<div id="tabs-b" class="tabPage">
				<div id="tabLicense" class="tabContent" style="overflow-y:scroll;">
					<p>
					webappObfuscator is copyrighted (c) 2015, by Rene AJM Veerman, Amsterdam.nl, CEO + CTO of seductiveapps.com..<br/>
					<br/>
					webappObfuscator comes with NO WARRANTIES OF ANY KIND of course - if webappObfuscator ends up losing you money, or *any* other problem whatsoever - i Rene AJM Veerman nor my company/companies can not be blamed for that, nor brought to any court for it. 
					<br/>
					Other than that : You and your company/companies/organisation(s)/government(s) may do with webappObfuscator as you please, at no cost of any kind to me at all, ever.. I don't think the Dutch government has an export-restriction for software that does what webappObfuscator does, but checking that is UP TO YOU.<br/>
					<br/>
					If you build any cool extensions (wait a few weeks, I suggest), I'd like a copy to include it in my distribution of webappObfuscator, but you're not obligated to pass ur improvements back to me ("forking allowed" + "porting to other server-side programming languages is allowed"). Please start the name of ur forks of this thing if distributed by you publicly, with "webappObfuscator_". You're not obligated to name ur fork that way, but it would b good for ppl searching the web for forks eh..<br/>
					<br/>
					Yes, you may even make your forks and ports (to other server-side programming languages) closed-source, and keep them to yourself/yourselves, and/or distribute them (as (obfuscated-)closed-source or open-source) any way you want - including selling them / renting them out.<br/>
					</p>

				</div>
				</div>
				
				<div id="tabs-c" class="tabPage">
				<div id="tabTodo" class="tabContent" style="overflow-y:scroll;">
					<p>
					2015 July 3nd, 01:57 CEST --- Status of project : <br/>
					Seemingly functional. Will serve obfuscated website (html, css, javascript, content), siteCode seems to work.<br/>
					<br/>	
					TODO:<br/>
					<ul>
					<li>test webappObfuscator__demoSite siteCode to perform javascript regular expression operations correctly.
					<li>test against my own seductiveapps.com sources.</li>
					<li>build some kinda interface to do dynamic obfuscation (for at least html, css, javascript) - my own seductiveapps.com/tarot-reading will need this too. - no aint gonna be hard or take long.. what might take long is getting the 1.5mb of js for my seductiveapps to run properly with the new faster obfuscation routines (skipping about 130 milliion preg_replace() interations atm - keeping fingers crossed..)</li>
					</ul>
					<br/>
					ETA for delivery of what's listed above here: no more than a week i hope..<br/>
					<br/>
					<ul>
					<li>JSON obfuscation.... well... you're probably gonna need some kinda whitelisting functionality (not just for keys t get whitelisted (not obfuscated), but for values and even entire sub-objects.. for this reason it's last on the todo-list, but once again, my own seductiveapps.com could use json obfuscation (i do *everything* in json these days), so it'll get done ok..<br/>
					white listing method? meta-keys *inside the JSON to-be-obfuscated of course, named like "__wo__blabla" : "some_webappObfuscator_functionalitySwitch_ID" ofcourse..</li>
					<li>SQL and XML obfuscation... argh.. ok ok, i'll do it (to keep things standard accross the web-industry)..
						<ul>
						<li>for SQL i'll include adodb.sourceforge.net into the webappObfuscator__demoSite code (yes, with a SQL hello-world example that gets obfuscated)</li>
						<li>for XML i'll have to re-read the specs. never liked to use XML, but it's not mind-bogglingly hard..</li>
						<li>naturally, SQL obfuscated output stays in SQL (tablename + "__webappObfuscated"), and XML in XML (ehh i'll look into how to store that neatly)</li>
						</ul>
					</li>
					<li>JSON inside SQL or XML, XML inside SQL, etc, etc, etc... ehh.. when i have time folks.. i do wanna make some long-overdue money with my own seductiveapps.com as well. but if it's not too hard, i'll include "something" for it ok..</li>
					</ul>

				</div>
				</div>
				<div id="tabs-d" class="tabPage">
				<div id="tabChangelog" class="tabContent" style="overflow-y:scroll;">


					<p>
					2015 July 2nd, 09:26 CEST :<br/>
					Completed these two to-do-list items:<br/>
					<ul>
					<li>tokens should not get replaced inside the actual content (meaning : in any normal human text encapsulated by html tags).</li>
					<li>URLs should not get obfuscated..</li>
					</ul>
					</p>

					<p>
					2015 July 1st, 09:05 CEST :<br/>
					Now produces HTML, CSS and Javascript output.<br/>
					Output yet to get tested by getting it loaded by webappObfuscator__demoSite/index.php - will do that tonight / tomorrow morning<br/>
					</p>

					<p>
					2015 June 29, 09:14 CEST :<br/>
					Making good progress, the thing is now able to parse html-within-javascript and javascript-within-html to any level deep.. Well, needs more work before that actually works, but the codestructure for it is there now.
					</p>

					<p>
					2015 June 21, 18:14 CEST:<br/>
					I decided to make the obfuscator itself run-able, as promised, but also found that I had leaked access to my own full sources for my own closed-source seductiveapps.com framework in the first release of today..<br/>
					I'd like to remind you of my very very firm copyright notice, to be found in the page sources for http://seductiveapps.com<br/>
					Please show the decency and sanity to delete any of my "seductiveapps/com_pw-ldkjf07a/*.*" files that you may have downloaded.
					</p>
						
					<p>
					-all times listed below here are in the CEST timezone- (CEST === UTC/GMT+1 in summertime = UTC/GMT+2)
					</p>

					<p>
					2015 June 17th<br/>
					Taking the day off.
					</p>

					<p>
					2015 June 16, 22:53 :<br/>
					Release aint gonna happen today folks. Still have to plug in jquery-ui into the status report thing, and today was so filled with frozen computers that I decided to go for a walk at night instead of frustrate myself more and more..
					</p>

					<p>
					2015 June 16, 12:17 :<br/>
					<a class="nomod" target="googleplusScreenshots" href="https://plus.google.com/photos/112674559333230230765/albums/6160914132921094849">Made some screenshots</a>.
					</p>

					<p>
					2015 June 16, 11:15 :<br/>
					I'll probably make a new release of what i got so far, with jQuery-ui as status-report interface, before midnight today. No promises though.
					</p>


					<p>
					2015 June 16, 11:04 :<br/>
					understatement for this week : i'm liking javascript's pointer functionality astronomically better than PHP's..<br/>
					In fact, one had better avoid "pointers" in PHP like one would the plague back in the dark ages..
					</p>

					<p>
					2015 June 15, 19:11 :<br/>
					Eh, the unicode identifier experiment is dead in the water, with Chrome v43 claiming they're all ILLEGAL TOKENs.. :( ;)<br/>
					See <a class="nomod" target="googleDiscussion" href="https://groups.google.com/a/chromium.org/forum/#!topic/chromium-discuss/uuVukTiR6Sc">https://groups.google.com/a/chromium.org/forum/#!topic/chromium-discuss/uuVukTiR6Sc</a>.<br/>
					Tomorrow i'll continue with ascii-only identifier/token generation, and see about obfuscating javascript, id="" and class="" in the HTML that's used inside a site-template as actual page-content..<br/>
					</p>

					<p>
					2015 June 15, 09:23 :<br/>
					The thing now produces code; for now only the site template javascript is obfuscated... But since i'm now obfuscating into unicode javascript identifiers (more combinations possible), that javascript doesn't run in the browser yet.
					</p>

					<p>
					2015 June 14, 00:56 :<br/>
					Made a ton of progress today. Working exclusively on this component. Will use jQuery-ui to replace my own framework components in the next release, which might not take more than a few days..
					</p>

					<p>
					2015 June 12, 19:05 : Status : being worked on. I have a development plan and skeleton code for webappObfuscator - that is; a way to collect the sources necessary for obfuscation (that includes the html + js site template, plus all the apps/games that run in that template as only their app/game html+js), to stick those sources into 4 obfuscation-worker-classes (html, css, javascript, json), and to extract only the necessary bits of HTML and javascript out of those sources.<br/>
					I also have a way to visualize it all being requested from the webserver and being processed, with my own seductiveapps components.. Making the webappObfuscator release not use those components of mine, is gonna take a bit of time, so i've decided not to make any more releases until it's all ready and debugged and stripped of seductiveapps components..
					</p>
					
				
				</div>
				</div>
				
			</div>
		</td>
	</tr>
</table>
</form>

<div id="jplayer" class="jp-jplayer"></div>

<script type="text/javascript">
function onWindowResize () {
	jQuery('#holder').css({left:'4%',top:'4%',width:'92%',height:'92%'});
}
jQuery('#tabs').tabs();
jQuery(window).resize(onWindowResize);
</script>

</div>
</div>
</body>
</html>