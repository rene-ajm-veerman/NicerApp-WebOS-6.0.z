#!/bin/bash
apt update
apt upgrade
apt dist-upgrade
apt install -y composer apache2 php php-dev libapache2-mod-php php-imap curl php-curl php-mailparse curl git imagemagick npm net-tools apt-transport-https gnupg wordnet
a2enmod headers rewrite
curl https://couchdb.apache.org/repo/keys.asc | gpg --dearmor | sudo tee /usr/share/keyrings/couchdb-archive-keyring.gpg >/dev/null 2>&1
source /etc/os-release
echo "deb [signed-by=/usr/share/keyrings/couchdb-archive-keyring.gpg] https://apache.jfrog.io/artifactory/couchdb-deb/ noble main" | sudo tee /etc/apt/sources.list.d/couchdb.list >/dev/null
apt update
apt install couchdb
npm install -g add-cors-to-couchdb
