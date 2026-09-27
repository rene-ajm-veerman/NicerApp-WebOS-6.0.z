#!/bin/bash
cd ~/Downloads
apt update
apt -y upgrade
apt -y dist-upgrade
apt install -y wget composer apache2 php php-dev libapache2-mod-php php-imap curl php-curl php-mailparse curl git imagemagick npm net-tools python3-chardet apt-transport-https gnupg wordnet
a2enmod headers rewrite
curl https://couchdb.apache.org/repo/keys.asc | gpg --dearmor | sudo tee /usr/share/keyrings/couchdb-archive-keyring.gpg >/dev/null 2>&1
source /etc/os-release
echo "deb [signed-by=/usr/share/keyrings/couchdb-archive-keyring.gpg] https://apache.jfrog.io/artifactory/couchdb-deb/ noble main" | sudo tee /etc/apt/sources.list.d/couchdb.list >/dev/null
apt update
apt install -y couchdb
cd /var/www
if [ ! -d "/var/www/NicerAppWebOS-v5.10.z" ]; then
  mkdir NicerAppWebOS-v5.10.z
  chown www-data:www-data NicerAppWebOS-v5.10.z
  chmod 750 NicerAppWebOS-v5.10.z
  cd NicerAppWebOS-v5.10.z
  mkdir NicerAppWebOS-code
  chown www-data:www-data NicerAppWebOS-code
  chmod 750 NicerAppWebOS-code
  mkdir downloads
  cd downloads
  wget https://nicer.app/downloads/latest/NicerApp-WebOS-v5.10.z-latest.zip
  unzip NicerApp-WebOS-v5.10.z-latest.zip -d ..
  mv ../var/www/nicer.app-5.10.z/nicer.app-3rd-party ../NicerAppWebOS-3rd-party
  chown -R www-data:www-data ../NicerAppWebOS-3rd-party
  chmod -R 750 ../NicerAppWebOS-3rd-party
  mv ../var/www/nicer.app-5.10.z/domains ../domains
  chown -R www-data:www-data ../domains
  chmod -R 750 ../domains
  mv ../var/www/nicer.app-5.10.z/nicer.app-siteMedia ../NicerAppWebOS-siteMedia
  chown -R www-data:www-data ../NicerAppWebOS-siteMedia
  chmod -R 750 ../NicerAppWebOS-siteMedia
  mv ../var/www/nicer.app-5.10.z/nicer.app-tarotSite-decks ../NicerAppWebOS-tarotSite-decks
  chown -R www-data:www-data ../NicerAppWebOS-tarotSite-decks
  chmod -R 750 ../NicerAppWebOS-tarotSite-decks
  cd ..
  git clone https://github.com/Rene-AJM-Veerman/NicerApp-WebOS-5.10.z NicerAppWebOS-code

  rm NicerAppWebOS-code/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/decks
  ln -s NicerAppWebOS-tarotSite-decks NicerAppWebOS-code/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/decks

  rm domains/nicer.app/siteMedia
  ln -s NicerAppWebOS-siteMedia domains/nicer.app/siteMedia
  chown www-data:www-data domains/nicer.app/siteMedia
  chmod 750 domains/nicer.app/siteMedia

  mkdir domains/nicer.app/siteData
  chown www-data:www-data domains/nicer.app/siteData
  chmod 750 domains/nicer.app/siteData

  rm domains/said.by/siteMedia
  ln -s NicerAppWebOS-siteMedia domains/said.by/siteMedia
  chown www-data:www-data domains/said.by/siteMedia
  chmod 750 domains/said.by/siteMedia

  mkdir domains/said.by/siteData
  chown www-data:www-data domains/said.by/siteData
  chmod 750 domains/said.by/siteData
fi
