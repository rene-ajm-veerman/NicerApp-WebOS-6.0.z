#place a shortcut to this windows batch file on your desktop..
# Z:\ is an internal-network windows networking (samba, smb, smbd on linux) link to your webserver htdocs folder

copy "c:\www_htdocs\webappObfuscator\output_stable\sources.obfuscated.fast.js" "z:\htdocs_YOURSITE.COM\YOURFRAMEWORK\scripts\obfuscated.js"

# from here on, you include in your <head> tag only the scripts that can't be obfuscated (like jQuery-2.1.1!), followed by http://YOURSITE.COM/YOURFRAMEWORK/scripts/obfuscated.js
