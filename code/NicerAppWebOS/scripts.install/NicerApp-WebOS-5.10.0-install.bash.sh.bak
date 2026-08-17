#!/bin/bash
echo "Your website will be installed under ~/www.NicerAppWebOS/nicer.app-5.10.z"
echo "Question 1of7 : What is the name of your website (your MYDOMAIN.COM / MYDOMAIN.TLD)?"
read DOMAIN_TLD
echo "----- NicerApp WebOS INSTALLATION SCRIPT BEGINS -----"
sudo su -
echo "Hold on, this could take quite a while and take up to NNgb in storage in ~/www.NicerAppWebOS"
apt update
apt upgrade
apt dist-upgrade
apt install -y nginx apache2 php curl git unzip zip wget
#echo "Your website will be installed under /var/www.NicerAppWebOS/nicer.app-5.10.z"
#cd /var/www.NicerAppWebOS
cd ~
rm -rf www
rm -rf www.NicerAppWebOS
mkdir www.NicerAppWebOS
cd ~/www.NicerAppWebOS
mkdir -p ~/www.NicerAppWebOS/nicer.app-5.10.z/code/nicer.app-5.10.0
mkdir -p ~/www.NicerAppWebOS/nicer.app-5.10.z/data/$DOMAIN_TLD/cms/Users
mkdir -p ~/www.NicerAppWebOS/nicer.app-5.10.z/data/$DOMAIN_TLD/cms/Groups
mkdir -p ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/data
mkdir -p ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/downloads/nicer.app
cd ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/downloads/nicer.app/
wget https://nicer.app/downloads/3rd-party.zip
cd ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/downloads/nicer.app/
wget https://nicer.app/downloads/tarotSite-decks.zip
cd ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/downloads/nicer.app/
wget https://nicer.app/downloads/siteMedia.zip
git clone https://github.com/NicerEnterprises/NicerApp-WebOS-MIT-5.10.0 ~/www.NicerAppWebOS/nicer.app-5.10.z/code/nicer.app-5.10.0/NicerAppWebOS
unzip -o ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/downloads/nicer.app/3rd-party.zip -d ~/www.NicerAppWebOS/nicer.app-5.10.z/code/nicer.app-5.10.0/NicerAppWebOS/3rd-party
unzip -o ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/downloads/nicer.app/siteMedia.zip -d ~/www.NicerAppWebOS/nicer.app-5.10.z/data/siteMedia
unzip -o ~/www.NicerAppWebOS/nicer.app-5.10.z/domains/$DOMAIN_TLD/downloads/nicer.app/tarotSite-decks.zip -d ~/www.NicerAppWebOS/nicer.app-5.10.z/data/nicer.app-tarotSite-decks
#mkdir nicer.app-5.10.z/code/nicer.app-5.10.0/downloads
#curl -LO https://nicer.app/downloads/3rd-party.zip MYDOMAIN.TLD/downloads
#unzip MYDOMAIN.TLD/downloads/3rd-party.zip MYDOMAIN.TLD/NicerAppWebOS
#mv -r MYDOMAIN.TLD/NicerAppWebOS/var/www.NicerAppWebOS/nicer.app/NicerAppWebOS MYDOMAIN.TLD/NicerAppWebOS
#rm -rf MYDOMAIN.TLD/NicerAppWebOS/var
#cd .../NicerAppWebOS/scripts.maintenance/
#chmod u+x setPermissions.sh
#./setPermissions.sh
#cd .../NicerAppWebOS/scripts.install/
#chmod u+x *.sh
#./install-NicerAppWebOS.OS.(K)ubuntu-20.04LTS.sh
