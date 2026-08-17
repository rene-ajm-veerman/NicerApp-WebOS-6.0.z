webappObfuscator-1.0.0, released 2015-July-03 02:17 CEST (CEST === UTC/GMT+1 in summertime === UTC/GMT+2)
homepage : http://seductiveapps.com/webappObfuscator


LICENSE BEGIN

  webappObfuscator is copyrighted (c) 2015-2016, by Rene AJM Veerman, Amsterdam.nl, CEO + CTO of seductiveapps.com..

  webappObfuscator comes with NO WARRANTIES OF ANY KIND of course - if webappObfuscator ends up losing you money, or *any* other problem whatsoever - i Rene AJM Veerman nor my company/companies can not be blamed for that, nor brought to any court for it. 

  Other than that : You and your company/companies/organisation(s)/government(s) may do with webappObfuscator as you please, at no cost of any kind to me at all, ever.. I don't think the Dutch government has an export-restriction for software that does what webappObfuscator does, but checking that is UP TO YOU.

  If you build any cool extensions (wait a few weeks, I suggest), I'd like a copy to include it in my distribution of webappObfuscator, but you're not obligated to pass ur improvements back to me ("forking allowed" + "porting to other server-side programming languages is allowed"). Please start the name of ur forks of this thing if distributed by you publicly, with "webappObfuscator_". You're not obligated to name ur fork that way, but it would b good for ppl searching the web for forks eh.

  Yes, you may even make your forks and ports (to other server-side programming languages) closed-source, and keep them to yourself/yourselves, and/or distribute them (as (obfuscated-)closed-source or open-source) any way you want - including selling them / renting them out.

LICENSE END


TERMINOLOGY

	source code encryption === obfuscation
	
CODE PHILOSOPHY 

  webappObfuscator is more than a source-code encrypter,
  it also seperates website-code and -content into seperate files in seperate folders on the server, making .../webappObfuscator-1.0.0/1.0.0/class.website.php (see -TAG1.2-) act like the base half of a CMS (Content Management System) for your website. 
  
  Since each website is different from the other, class.website.php links into your own CMS code (see -TAG1.1-) for anything that is clearly part of the website you want to source-encrypt rather than being part of the obfuscation mechanics (see -TAG1.2-) like enforcing that you use seperate files for HTML template and HTTP <meta> and <title> tags and such, which is important when building fully asynchronous websites (AJAX loading of content etc).
  
  WebappObfuscator uses the actual sourcecodes (HTML, CSS, Javascript and JSON) of your website to produce obfuscated output files into a subfolder of a folder called webappObfuscator__output.
  This means your actual sourcecodes have to be password protected from manual HTTP:// reads or reads by guessing HTTP:// folder and filenames.
  
  Websites should be able to show and run an unlimited number of pages / apps, but website code is often specific to certain types of pages (called apps from here on), signified in principle by the $untranslatedContentURL variable.
  When users browse around a site, the location bar reflects this change by changing to a new URL.
  To make an obfuscator that encrypts all pages/apps work, the HTML, CSS and Javascripts for these apps must be in their own subfolder, hidden away from direct public browsing and inventorized so that the CMS part of webappObfuscator can serve them when and where needed.
  
  a website consists of HTML, CSS and Javascript code that is shared between all pages/URLs, and code that is only needed on specific URLs on that website.
  
  most websites only use one HTML template, but just to be extensible, i've enabled an unlimited number of templates chosen based on the URL that the visitor requests off your website.
  
  variables go from "untranslated" to "translated" to "resolved".
  
AN OVERVIEW OF THE FOLDERSTRUCTURE FOR ANY .../htdocs/mysite.com/ :
  
  .../htdocs/mysite.com/ contains (among other files and folders):
    .../.htaccess
      this file contains at least :
      OPTIONS ALL -INDEXES 
	to prevent URL guessing
	
    .../index.php
      this file will call up code in class.website.php (part of webappObfuscator) and class.website.php will call up your own website CMS code when needed according to variable and function names that are polymorphic global variables in PHP.
    
    .../siteFramework-pw-AZ19_/
	All of your site's website plaintext unencrypted code and content should be moved into this folder (protected by the AZ19_ password and the .htaccess file in the mysite.com root folder)
	Be sure to use a long password (10-20 characters at least).
      
    .../siteFramework-pw-AZ19_/20150624 1200/  
	That's a YYYYMMDD HHMM date format to allow for forking of .../siteFramework-pw-AZ19
      
    .../siteFramework-pw-AZ19_/20150624 1200/core/ 
    .../siteFramework-pw-AZ19_/20150624 1200/core/functions.php 
	-TAG1.1- the functions.php that holds common code used all over your website
      
    .../siteFramework-pw-AZ19_/20150624 1200/cms/ 
    .../siteFramework-pw-AZ19_/20150624 1200/cms/class.yourWebsite.php
	-TAG1.1- Holds class yourWebsite that organizes your website code (rather than stuff tons of code in .../htdocs/mysite.com/index.php 
	This is an extension of .../core/functions.php that you should write for more complicated websites or as common code for multiple different websites.
      
    .../siteFramework-pw-AZ19_/20150624 1200/db/ 
    .../siteFramework-pw-AZ19_/20150624 1200/ui/ 
	ui = user-interface components (javascript, css, html sources) each in their own folder (not listed here)
	
    .../siteFramework-pw-AZ19_/20150624 1200/siteContent/
    .../siteFramework-pw-AZ19_/20150624 1200/siteContent/index.tpl
	These folder and filenames are suggested further seperations of your site's website plaintext code files.
      
    .../obfuscate_sa-pw-ZA361/
    .../obfuscate_sa-pw-ZA361/obfuscate.php
    .../obfuscate_sa-pw-ZA361/ajax_obfuscate.php
    .../obfuscate_sa-pw-ZA361/globals.php
	These are the password-protected files used to encrypt the sources in .../siteFramework-pw-AZ19/
	You call up obfuscate.php or when faced with fatal errors that go unlisted, ajax_obfuscate.php directly from the browser location bar.
	ajax_obfuscate.php is meant to be tweaked by the end-user of webappObfuscator (you).

	
    .../obfuscate_sa-pw-ZA361/webappObfuscator__cache/	
	This holds (large) JSON data files used internally by webappObfuscator (a JSON file with all your sources collected by webappObfuscator for instance) 
	Do not use or edit the files in this folder.
    
    .../obfuscate_sa-pw-ZA361/webappObfuscator__output/
	This holds (large) JSON data files used internally by webappObfuscator 
	(the real -> encrypted tokens (strings) for instance)..
	Do not use or edit the files in this folder.
      
    .../opensourcedBySeductiveApps.com/tools/webappObfuscator/
	This contains the webappObfuscator sourcecodes
	
    .../opensourcedBySeductiveApps.com/tools/webappObfuscator/boot_latestDevelopment.php
	This is the file you require_once() from .../htdocs/mysite.com/index.php to link all required webappObfuscator files into your index.php
	This is designed to be cheap to include and only use CPU and memory, etc resources when you order it to do actual work.
	It is designed to be cheap to include obfuscated sources.
	
    .../opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator-1.0.0/1.0.0/class.website.php
	-TAG1.2- this is the webappObfuscator part of your website's CMS
	
	
    .../siteFramework-pw-AZ19_/20150624 1200/apps/
	This holds all the code specific to specific (types of) pages.

    .../siteFramework-pw-AZ19_/20150624 1200/apps/apps_available.php
	contains:
	  <?php
	  $dfo__apps = array('apps'=>array());

	  require_once (dirname(__FILE__).'/appOne/appLogic/boot.php');
	  require_once (dirname(__FILE__).'/appTwo/appLogic/boot.php');
	  ?>

    .../siteFramework-pw-AZ19_/20150624 1200/apps/apps_installed.php
	note : this file is unused at the moment
    
    .../siteFramework-pw-AZ19_/20150624 1200/apps/appOne/
    .../siteFramework-pw-AZ19_/20150624 1200/apps/appOne/appContent/
    .../siteFramework-pw-AZ19_/20150624 1200/apps/appOne/appData/
    .../siteFramework-pw-AZ19_/20150624 1200/apps/appOne/appLogic/
    .../siteFramework-pw-AZ19_/20150624 1200/apps/appOne/appLogic/boot.php
	contains require_once() statements that link in the rest of the code used by appOne
    
    .../webappObfuscator__output/html/
    .../webappObfuscator__output/html/siteTemplate.html
    .../webappObfuscator__output/css/
    .../webappObfuscator__output/javascript/
    .../webappObfuscator__output/json/
	This is where your obfuscated output will be written to.

AN OVERVIEW OF HOOKS/HANDLERS LINKING webappObfuscator TO YOUR SITE'S CMS/CODE

    LISTING OF GLOBAL VARIABLES
    all folders (.../) and files listed here are again under .../htdocs/mysite.com/
    
    
      global $woWebsite__settings;
      global $woWebsite 
	= new class woWebsite ($woWebsite__settings); from .../opensourcedBySeductiveApps.com/tools/webappObfuscator/webappObfuscator-1.0.0/1.0.0/class.website.php
      
      global $myWebsite = $saWebsite
	= new class saCMS ($cmsSettings); from  .../siteFramework-pw-AZ19_/20150624 1200/cms/class.main.php
      
      .../index.php
	calls $woWebsite->displaySiteTemplate() which then calls
	  calls $myWebsite->prepareSiteTemplateVars() which is expected to return an array like:
	  array (
	    'templateVar1' => 'value1',
	    'templateVar2' => 'value2'
	  );
	  templateVar1 and templateVar2 should be listed as {$templateVar1} and {$templateVar2} in .../siteFramework-pw-AZ19_/20150624 1200/siteContent/index.tpl
	  
	  
      global $webappObfuscator__settings = array();
      global $webappObfuscator = new webappObfuscator ($woSettings);
      
	

INSTALLATION INSTRUCTIONS

	on Linux, you have to give write access to apache+php to some folders..
		open up a "terminal" application, 
		change directory to the webappObfuscator distribution root,
		and enter :
		
		chmod 777 webappObfuscator__cache
		chmod 777 webappObfuscator__output
		

URLs available : 

	YOUR_INSTALLATION_FOLDER/demo_obfuscate.php?n=y 
		// obfuscates the sources of the following URL :
		
	YOUR_INSTALLATION_FOLDER//webappObfuscator__demoSite/
		// the demo site used to demonstrate obfuscation.

RELEASE NOTES : 

	global settings for obfuscation are listed in ./demo_globals.php

	what to obfuscate is listed in ./demo_globals.php::$sources, as URLs on your development server / live server

	sources to obfuscate are fetched using ./webappObfuscator-1.0.0-DEV/webappObfuscator-1.0.0.php::fetchSources(), which tacks on a ?contentOnly=yes to all pure-content URLs,
		saving a ton of site-template HTML that you don't need a gazillion-times extra. 
		!!! You do need to adapt your site CMS to output *only* the content (so NOT the site-template HTML code) when ?contentOnly=yes is added to a URL.
			see the .htaccess file in webappObfuscator__demoSite/
				#the QSA flag will pass on /someSubURL/?contentOnly=yes to index.php::$_GET['contentOnly']==yes - nothing else will work!

TODO (incomplete list) :
	
	warning for $randomStringLength being too short.
