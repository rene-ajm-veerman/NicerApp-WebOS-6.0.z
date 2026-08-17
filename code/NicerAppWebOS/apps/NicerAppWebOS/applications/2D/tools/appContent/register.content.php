<h2 id="headerLicense">SeductiveApps Web-UI framework LICENSE</h2>
<script type="text/javascript">
var vividTextCmd = {
	el : jQuery('#headerLicense')[0],
	theme : sa.cg.themes.saColorgradientSchemeRed, 
	animationType : sa.vividText.globals.animationTypes[0],
	animationSpeed : 2500
};
sa.vividText.initElement (vividTextCmd);	

var 
pw = window.parent.window,
psa = pw.sa,
saLicense = {
	buttons : {
		btnAccept : {
			click : function (evt) {
				var ajaxCommand = {
					type : 'POST',
					url : psa.m.globals.urls.app + 'seductiveapps/com/core/registration4sa/1.0.0/ajax_seductiveapps_registration.php',
					data : {
						clientName : $('#formAccept_clientName').val(),
						clientBirthdate : $('#formAccept_clientBirthdate').val(),
						clientCompany : $('#formAccept_clientCompany').val(),
						clientSite : $('#formAccept_clientSite').val(),
						clientEmail : $('#formAccept_clientEmail').val()
					},
					success : function (data, ts) {
						alert ('You registred successfully, the email with your download link should arrive soon.\n\n'+data);
					}
				};
				jQuery.ajax (ajaxCommand);						
			}
		},
		btnDontAccept : {
			click : function (evt) {
				
			}
		}
	}
}

</script>

<div id="textLicense">
<?php echo '<p>'.str_replace ("\n", '</p><p>', str_replace ("\n\r", '</p><p>', file_get_contents(dirname(__FILE__).'/../../../seductiveapps/license.txt'))).'</p>'; ?>
</div>

<div id="divForm">
<p>All of the fields with (*) below must be entered correctly, or you lose your permission to run seductiveapps. All other fields are optional for you to fill in.</p>
<form id="formAccept">
	<table>
		<tr>
			<td>(*) Your name</td>
			<td><input id="formAccept_clientName" type="text" style="width:100%;"></input></td>
		</tr>
		<tr>
			<td>(*) Your birthdate (DD-MM-YYYY)</td>
			<td><input id="formAccept_clientBirthdate" type="text" style="width:100%;"></input></td>
		</tr>
		<tr>
			<td>Your Company</td>
			<td><input id="formAccept_clientCompany" type="text" style="width:100%;"></input></td>
		</tr>
		<tr>
			<td>URL where you'll be running seductiveapps</td>
			<td><input id="formAccept_clientSite" type="text" style="width:100%;"></input></td>
		</tr>
		<tr>
			<td>(*) Your email address (download link will be sent here)</td>
			<td><input id="formAccept_clientEmail" type="text" style="width:100%;"></input></td>
		</tr>
		<tr>
			<td colspan="2">
				Your IP address will also be logged.
			</td>
		</tr>
	</table>
</form>
</div>

<table style="width:300px;">
<tr>
<td><div id="btnDontAccept" class="vividButton vividTheme__menu_002" style="position:relative;"><a href="javascript:saLicense.buttons.btnDontAccept.click();">Cancel</a></div></td>
<td><div id="btnAccept" class="vividButton vividTheme__menu_002" style="position:relative;"><a href="javascript:saLicense.buttons.btnAccept.click();">Accept</a></div></td>
</a></div></td>
</tr>
</table>

