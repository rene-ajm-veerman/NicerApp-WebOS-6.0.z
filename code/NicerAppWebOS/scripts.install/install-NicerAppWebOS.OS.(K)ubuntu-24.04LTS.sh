#!/bin/bash
cd /var/www

echo "Your website will be installed under /var/www"
echo "Question 1of7 : What is the name of your website (your MYDOMAIN.COM / MYDOMAIN.TLD)?"
read DOMAIN_TLD

echo "Question 2of7 : Under what domain name (localhost / MYDOMAIN.COM) or IP address do you want to publish your website?"
read SERVER_NAME

echo "Question 3of7 : What email address do you wish to use for webserver level errors and notifications?"
read APACHE_EMAIL

echo "Question 4of7 : What is the name of the Owner of the MYDOMAIN.TLD company?"
read OWNER_NAME

echo "Question 5of7 : What is the email address of the Owner of the MYDOMAIN.TLD company?"
read OWNER_EMAIL

echo "Question 6of7 : What do you want the end-user password for the Owner of the MYDOMAIN.TLD company to be? Please be sure to make this a strong password, with at least a mix of alphanumerical characters and some punctuation."
read OWNER_PASSWORD

set -o xtrace

apt update

apt upgrade

apt dist-upgrade

apt install -y nginx apache2 php8 curl git unzip zip aptitude ufw

echo 'Allowing in traffic on this machine on port 443 (https).'
ufw allow 443

aptitude install composer apache2 php php8.3-gd php-dev libapache2-mod-php php-imap curl php-curl php-mailparse curl git imagemagick npm net-tools apt-transport-https gnupg wordnet libauthen-ntlm-perl libclass-load-perl libcrypt-ssleay-perl libdata-uniqid-perl libdigest-hmac-perl libdist-checkconflicts-perl libencode-imaputf7-perl libfile-copy-recursive-perl libfile-tail-perl libio-compress-perl libio-socket-inet6-perl libio-socket-ssl-perl libio-tee-perl libmail-imapclient-perl libmodule-scandeps-perl libnet-dbus-perl libnet-ssleay-perl libpar-packer-perl libreadonly-perl libregexp-common-perl libsys-meminfo-perl libterm-readkey-perl libtest-fatal-perl libtest-mock-guard-perl libtest-mockobject-perl libtest-pod-perl libtest-requires-perl libtest-simple-perl libunicode-string-perl liburi-perl libtest-nowarnings-perl libtest-deep-perl libtest-warn-perl make cpanminus nodejs node-gyp libnode-dev npm dovecot-imapd pass gh

cpanm Mail::IMAPClient

cpanm JSON::WebToken

a2enmod headers rewrite

service apache2 restart

# installing the NicerAppWebOS source files
#cd /var/www
#git clone https://github.com/NicerEnterprises/NicerApp-WebOS $DOMAIN_TLD

#git clone https://github.com/NicerEnterprises/businessLogic-forPHP-generic-databases $DOMAIN_TLD/NicerAppWebOS

#cd $DOMAIN_TLD/NicerAppWebOS/install.scripts
#php apache2.virtualHost.php $SERVER_NAME $APACHE_EMAIL $DOMAIN_TLD

cd /var/www/$DOMAIN_TLD/NicerAppWebOS/3rd-party
rm -rf composer.json

git clone https://github.com/thephpleague/oauth2-client

##git clone https://github.com/NicerAppWebOS/sag

git clone https://github.com/zingchart/zingtouch

cd /var/www/$DOMAIN_TLD/NicerAppWebOS/3rd-party/3D
#rm -rf libs
mkdir libs
cd libs
git clone https://github.com/mrdoob/three.js three.js

cd /var/www/$DOMAIN_TLD/NicerAppWebOS/3rd-party/jQuery
rm -rf spectrum
git clone https://github.com/seballot/spectrum

cd /var/www/$DOMAIN_TLD/NicerAppWebOS/3rd-party/vendor

rm -rf adodb
echo composer require adodb/adodb-php ^5.22
composer require adodb/adodb-php ^5.22

rm -rf defuse
echo composer require defuse/php-encryption
composer require defuse/php-encryption

rm -rf league
echo composer require league/oauth2-facebook
composer require league/oauth2-facebook

echo composer require league/oauth2-google
composer require league/oauth2-google

echo composer require league/oauth2-instagram
composer require league/oauth2-instagram

echo composer require league/oauth2-linkedin
composer require league/oauth2-linkedin

# couchdb
#apt install libmozjs-78-0
#dpkg -i /var/www/$DOMAIN_TLD/NicerAppWebOS/install.scripts/couchdb_3.2.2-2_jammy_amd64.deb
sudo apt update && sudo apt install -y curl apt-transport-https gnupg
curl https://couchdb.apache.org/repo/keys.asc | gpg --dearmor | sudo tee /usr/share/keyrings/couchdb-archive-keyring.gpg >/dev/null 2>&1
source /etc/os-release
echo "deb [signed-by=/usr/share/keyrings/couchdb-archive-keyring.gpg] https://apache.jfrog.io/artifactory/couchdb-deb/ ${VERSION_CODENAME} main" \
    | sudo tee /etc/apt/sources.list.d/couchdb.list >/dev/null
echo "Question 7of7 : For sheer installation ability, you shouldn't list non-alphanumerical characters in your CouchDB ErLang cookie. Agree (y/n)?"
read AGREE_COUCHDB_NON_ALPHA_COOKIE
apt update
apt install -y couchdb

echo "{ \"OWNER_NAME\" : \"$OWNER_NAME\", \"OWNER_EMAIL\" : \"$OWNER_EMAIL\", \"OWNER_PASSWORD\" : \"$OWNER_PASSWORD\" }" > /var/www/$DOMAIN_TLD/domainConfig/$DOMAIN_TLD/company.owner.json

# mysql
#apt install mysql

# postgresql
#apt install postgresql

mv /var/www/$DOMAIN_TLD/NicerAppWebOS/apps/manufacturerNameForDomainName_127.0.0.1_val.txt /var/www/$DOMAIN_TLD/NicerAppWebOS/apps/manufacturerNameForDomainName_$DOMAIN_TLD\_val.txt

chmod u+x /var/www/$DOMAIN_TLD/NicerAppWebOS/maintenance.scripts/setPermissions.sh

/var/www/$DOMAIN_TLD/NicerAppWebOS/maintenance.scripts/setPermissions.sh

echo "Installation of the pre-requisites is (hopefully) complete now."
echo "You must now complete the database installation by populating it with 2 admin users : DOMAIN_TLD___OWNER_NAME (with your OWNER_PASSWORD for password), and DOMAIN_TLD___Guest (with password : Guest) and initial data, by browsing to https://$DOMAIN_TLD/NicerAppWebOS/db_init.php".

echo "After running db_init.php, you must still add a cron job to get automated news fetching going. Use 'crontab -e' on the linux commandline for that, and add"
echo "SHELL=/bin/bash"
echo "to the top of the file, and"
echo "*/5 * * * * source /var/www/DOMAIN_TLD/NicerAppWebOS/scripts.install/install.cron.d/nicerapp__init_globals.sh && php \"/var/www/DOMAIN_TLD/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/crontabEntry_manageDatabase.php\" >> \"$NA_LOGS_PATH/$dateMinutesSeconds-news_crontabEntry_manageDatabase.txt\" 2>&1"
echo "to the bottom of it."
