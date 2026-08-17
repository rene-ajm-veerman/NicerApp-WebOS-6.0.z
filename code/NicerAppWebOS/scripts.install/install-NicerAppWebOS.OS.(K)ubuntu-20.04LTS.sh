#!/bin/bash
sudo su -

apt update

apt upgrade

apt dist-upgrade

apt install aptitude

aptitude install composer apache2 php php-dev libapache2-mod-php php7.4-mbstring php-imap curl php-curl php-mailparse curl git imagemagick npm net-tools python-chardet apt-transport-https gnupg wordnet libauthen-ntlm-perl libclass-load-perl libcrypt-ssleay-perl libdata-uniqid-perl libdigest-hmac-perl libdist-checkconflicts-perl libencode-imaputf7-perl libfile-copy-recursive-perl libfile-tail-perl libio-compress-perl libio-socket-inet6-perl libio-socket-ssl-perl libio-tee-perl libmail-imapclient-perl libmodule-scandeps-perl libnet-dbus-perl libnet-ssleay-perl libpar-packer-perl libreadonly-perl libregexp-common-perl libsys-meminfo-perl libterm-readkey-perl libtest-fatal-perl libtest-mock-guard-perl libtest-mockobject-perl libtest-pod-perl libtest-requires-perl libtest-simple-perl libunicode-string-perl liburi-perl libtest-nowarnings-perl libtest-deep-perl libtest-warn-perl make cpanminus nodejs node-gyp libnode-dev libnode64 nodejs-legacy npm dovecot-imapd

cpanm Mail::IMAPClient

cpanm JSON::WebToken

a2enmod headers rewrite


# couchdb
curl https://couchdb.apache.org/repo/keys.asc | gpg --dearmor | sudo tee /usr/share/keyrings/couchdb-archive-keyring.gpg >/dev/null 2>&1 source /etc/os-release

echo "deb [signed-by=/usr/share/keyrings/couchdb-archive-keyring.gpg] https://apache.jfrog.io/artifactory/couchdb-deb/ 3.2.2 main" | sudo tee /etc/apt/sources.list.d/couchdb.list >/dev/null

apt update

apt install couchdb

mv /var/www/$DOMAIN_TLD/NicerAppWebOS/apps/manufacturerNameForDomainName_127.0.0.1_val.txt /var/www/$DOMAIN_TLD/NicerAppWebOS/apps/manufacturerNameForDomainName_$DOMAIN_TLD\_val.txt

## if you want to access CouchDB through PouchDB from Javascript (INSECURE!) :
### npm install -g add-cors-to-couchdb
### add-cors-to-couchdb -u admin -p YOURADMINPASSWORDFORCOUCHDB
