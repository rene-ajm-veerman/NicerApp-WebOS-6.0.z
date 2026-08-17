# intended to be run from the server machine on the LAN
if [ -f "LAN_passwords.sh" ]; then
    source LAN_passwords.sh
    for RECIPIENT_IP in ${RECIPIENTS[@]}; do
        mkdir -p /var/logs/apache2/NicerAppWebOS/$date
        echo $LAN_PASSWORD | rsync -rvzhpP $LAN_USER@$LAN_SERVER_IP:/var/www/$DOMAIN_TLD/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/technology/crawlers/imageSearch/output/* rsync://$LAN_USER@$LAN_SERVER_IP/root/var/www/$DOMAIN_TLD/NicerAppWebOS/apps/NicerAppWebOS/application-programmer-interfaces/technology/crawlers/imageSearch/output > /var/logs/apache2/NicerAppWebOS/$date/rsync_to_$RECIPIENT_IP.api.crawler.imageSearch_output.log.txt
    done
else 
    echo ".../NicerAppWebOS/scripts.maintenance/LAN_passwords.sh does not exist. For a template, see .../NicerAppWebOS/scripts.maintenance/LAN_passwords_EXAMPLE.sh"
fi

