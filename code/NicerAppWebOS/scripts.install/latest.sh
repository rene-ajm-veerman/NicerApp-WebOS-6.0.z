#!/bin/bash


ROOT_PATH=/var/www/NicerAppWebOS-v6.y.z-beta/
VERSION=NicerAppWebOS-v6.0.0-beta-6.0.0

cd ~/Downloads
sudo apt update
sudo apt -y upgrade
sudo apt -y dist-upgrade
sudo apt install -y wget composer apache2 php php-dev libapache2-mod-php php-imap curl php-curl php-mailparse curl git imagemagick npm net-tools python3-chardet apt-transport-https gnupg wordnet
sudo a2enmod headers rewrite
if grep -q "couchdb-archive-keyring.gpg" /etc/apt/sources.list.d/couchdb.list; then
  echo "couchdb found on this system, no need to download it again."
else
  curl https://couchdb.apache.org/repo/keys.asc | gpg --dearmor | sudo tee /usr/share/keyrings/couchdb-archive-keyring.gpg >/dev/null 2>&1
  source /etc/os-release
  echo "deb [signed-by=/usr/share/keyrings/couchdb-archive-keyring.gpg] https://apache.jfrog.io/artifactory/couchdb-deb/ noble main" | sudo tee /etc/apt/sources.list.d/couchdb.list >/dev/null
  sudo apt update
  sudo apt install -y couchdb
fi

cd /var/www
if [ ! -d $ROOT_PATH ]; then
  sudo mkdir -p $ROOT_PATH
  sudo chown www-data:www-data $ROOT_PATH
  sudo chmod 750 $ROOT_PATH
  sudo cd $ROOT_PATH
  sudo mkdir code
  sudo chown www-data:www-data code
  sudo chmod 750 code

  sudo mkdir downloads
  sudo cd downloads
  sudo wget https://nicer.app/downloads/NicerAppWebOS-v6.y.z/$VERSION.zip
  sudo unzip $VERSION.zip -d ..

  echo "=== NicerAppWebOS is now loaded to the local machine, now where to put it.."

  cd ..
  read -p "On which domain will you be hosting this?" MY_DOMAIN
  sudo mkdir -p domains/$MY_DOMAIN
  sudo mv downloads/$VERSION code
  sudo ln -s code/$VERSION domains/$MY_DOMAIN/NicerAppWebOS
  sudo chown -R www-data:www-data domains
  sudo chmod -R 750 domains
fi
