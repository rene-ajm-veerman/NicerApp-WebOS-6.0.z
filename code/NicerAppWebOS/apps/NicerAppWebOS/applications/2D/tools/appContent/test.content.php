<script type="text/javascript">
	function encode () {
		var url = sa.m.globals.urls.app+'tools/test/'+sa.m.urlEncodeJSON(jQuery('#input').val());
		debugger;
		
		window.parent.window.History.pushState (null, null, url);
		evt.preventDefault();
	}
	
	function decode () {
		debugger;
		var jsonURLencoded = window.parent.window.document.location.href.replace (sa.m.globals.urls.app+'tools/test/', '');
		
		var json = eval ('('+sa.m.urlDecodeJSON (jsonURLencoded)+')');
		hm (json, 'json', { htmlID : 'yum', opacity : 0.65 } );
		evt.preventDefault();
	}
	
</script>
<!--

	{"test" : "t" }
	
-->

<form id="form">
<input id="input" type="text" value='{"comments":[{"from":"r","ip":"82.161.37.94","when":"2014-Jan-30 07:05 [UTC\/GMT +1]","whenGetTime":"1391061906441","whenTimezoneOffset":"-60","comment":"<p>rr<span style=\"color: #ffcc00;\">rr<\/span><\/p>"}]}'></input><br/>
<textarea id="output"></textarea><br/>
<input type="submit" onclick="javascript:encode();" value="Encode" style="width:100%;"></input><br/>
<input type="submit" onclick="javascript:decode();" value="Decode" style="width:100%;"></input>
</form>
<div id="yum" style="width:100%;height:200px;"></div>
