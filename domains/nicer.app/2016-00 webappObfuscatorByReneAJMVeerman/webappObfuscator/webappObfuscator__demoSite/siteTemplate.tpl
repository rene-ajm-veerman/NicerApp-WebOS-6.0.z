<html>
<!--*
	{$copyrightNotice}
*-->
<head>
	<title>{$pageTitle}</title>
	<meta name="robots" content="all"> <!-- allows searchengines to crawl your entire site -->
	<meta name="copyright" content="Copyrighted (c) 2002-2015 and All Rights Reserved (r) by YOUR_NAME, YOUR_COUNTRY [, YOUR_CITY]">
	<meta name="author" content="YOUR NAME (i suggest you include your middle initials)">
	<meta http-equiv="cache-control" content="no-cache">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="pragma" content="no-cache">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en">
	<meta http-equiv="content-language" content="english">
	
	<!-- you might wanna use http://realfavicongenerator.net/ and put it's output in $dfoPUBLICurl/siteArtwork/favIcon and the tags 
			it generates for you, here..
	-->

	{$headIndex}
</head>
<body style="margin:0px;padding:0px;" onload="dfo.site.code.startSiteCode();">
	<div id="dfo__menu" class="dfo__dialog dfo__menu">
		<!-- search google.com for "free javascript menu" if you want something fancier-looking.
			ALL proper HTML menus use the <ul><li> structure to provide the menu-items!
		-->
		<ul>
			<li><a href="http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/">Front page</a></li>
			<li><a href="http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/appOne">App One</a></li>
			<li><a href="http://new.localhost/opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator__demoSite/appTwo">App Two</a></li>
			
			<!-- DOUBLE QUOTES AT THE HTML LEVEL, SINGLE QUOTES AT THE JS LEVEL : THE BEST WAY -->
			<li><a href="javascript:dfo.site.code.testHTMLinsertion('test 1A', 'test 1B');">JS.1</a></li>
			
			<!-- SINGLE QUOTES AT THE HTML LEVEL, DOUBLE QUOTES AT TE JS LEVEL : The road to misery in more complicated apps. 
				Included here so ppl dont have to rewrite entire stacks of code just to get obfuscation going -->
			<li><a href="javascript:dfo.site.code.testHTMLinsertion (dfo.s.c.getString('bla'),'test 2B');">JS.2</a></li>
			
			<li><a href="javascript:dfo.site.code.testRegexps (/H3ll0/, /Hello/, 'H3ll0 world');">JS.3</a></li>
			<li><a href="javascript:dfo.site.code.testRegexps (undefined, undefined, 'H3ll0 world');">JS.4</a></li>
		</ul>
	</div>
	
	<div id="dfo__content" class="dfo_dialog dfo_content">
		{$pageContent}
	</div>

	<div id="dfo__leftSidebar" class="dfo_dialog dfo_sidebar">
		<p>left side-bar</p>
	</div>
	
	<div id="dfo__rightSidebar" class="dfo_dialog dfo_sidebar">
		<p>right side-bar</p>
	</div>
	

	<div id="jplayer" class="jp-jplayer"></div>
</body>
</html>