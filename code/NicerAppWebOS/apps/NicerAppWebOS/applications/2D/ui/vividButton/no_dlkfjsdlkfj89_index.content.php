<?php
require_once (dirname(__FILE__).'/../../../../boot.php');
main();

function main() {
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" style="width:100%;height:100%;">
	<head>
		<title><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></title>
		<?php require (dirname(__FILE__).'/index.php.meta.php');?>
		<link rel="StyleSheet" href="<?php echo SA_WEB?>/content.css"/>
		<link rel="StyleSheet" href="<?php echo SA_WEB?>/com/vividButton/content.css"/>
		<link type="text/css" rel="StyleSheet" media="screen" href="<?php echo SA_WEB?>/get_css.php?want=all"/>
		<script type="text/javascript" src="<?php echo SA_WEB?>/get_javascript.php?want=all"></script>
		<script type="text/javascript">
			window.onresize = function () {
				sa.sp.containerSizeChanged(document.getElementById('iframeContent'));
			};
			function getPage (pageNo)  {
				var ajaxCommand = {
					type : 'GET',
					url : '<?php echo SA_WEB?>/com/ui/vivid/button/ajax_buttons.php',
					data : {
						page : pageNo
					},
					success : function (data, ts) {
						var form = document.getElementById ('controlForm');
						if (form.children[0].tagName.toUpperCase()=='SPAN') form.style.display = 'none';
						form.innerHTML = data;
						jQuery(form).show('slow');
						sa.vcc.init(form);
					}
				};
				jQuery.ajax (ajaxCommand);
			};
		</script>		
	</head>
	<body onload="getPage(1); sa.vcc.init();" style="margin:0px;padding:0px;overflow:hidden;width:100%;height:100%">
		<div id="iframeContent" class="content vividScrollpane vividTheme__<?php echo SA_SITES_SEDUCTIVEAPPS_SCROLLPANE_THEME?>" style="width:100%;height:100%;">
			<h1><?php readfile (dirname(__FILE__).'/index.php.title.txt');?></h1>
	
			<p>
			First, the currently available themes:
			</p>		
			<form id="controlForm">
				<span style="background-color:blue; color:white; font-weight:bold">Loading..</span>
			</form>
			
	
			<p>
			<a href="http://seductiveapps.com/blog/animated-video-buttons" target="_parent">Here</a> is an article that explains how to put video on buttons and menus.<br/>
			</p>
	
			<p>
			Example of button code:
			<pre>
&lt;div id="button1" class="vividButton vividTheme__fish_001"&gt;
	&lt;a href="someURL"&gt;label for button&lt;/a&gt;
&lt;/div&gt;
			</pre>
			The &lt;a&gt; tag serves as the onclick handler. This ensures that search-engines will be able to follow the link as well.<br/>
			For "someURL" you can also put "javascript:someJavascript();" and that will be eval-ed onclick.<br/>
			</p>
			
			<p>
			Next, for all the vividWidgets on your page, put this script in your &lt;head&gt;:
			<pre>
&lt;script type="text/javascript" src="/seductiveapps/get_javascript.php?want=all"&gt;&lt;/script&gt;
&lt;script type="text/javascript"&gt;
	function onLoad() {
		sa.vividControls.init();
	}
&lt;/script&gt;
			</pre>
			Then call this script function as an onload handler for your &lt;body&gt;;<br/>
			<pre>
	&lt;body onload="onLoad()"&gt;
			</pre>
			The sa.vividControls.init() call can take an <a href="http://en.wikipedia.org/wiki/HTML_element" target="_new">HTML DOM element</a> as argument, which means it will only initialize vividWidgets in that element or any of it's children. This useful for when you load new buttons via <a href="http://en.wikipedia.org/wiki/Ajax_%28programming%29" target="_new">AJAX</a> <a href="http://www.chazzuka.com/ajaxify-your-web-pages-using-jquery-88/" target="_new">techniques</a>, like this page does.<br/>
			</p>		
			
			<p>
			You can style the label text with regular CSS code. Don't specify the &lt;a&gt; tag in your css code.<br/>
			Buttons each need a valid and UNIQUE .id property.<br/>
			</p>
			
			<p>
			Example of the CSS code (for all buttons with the birds_001 theme):<br/>
			<pre>
.vividTheme__birds_001 td {
	color : green;
	font-family : "Tahoma","Verdana";
}
			</pre>		
			OR (for a specific button with id="btn_birds_001")
			<pre>
#btn_birds_001 td {
	color : green;
	font-family : "Tahoma","Verdana";
}
			</pre>
			</p>
	
			<script type="text/javascript">
			setTimeout ("getPage(1);", 2000);
			</script>
		</div>		
	</body>
</html>
<?php
}
?>