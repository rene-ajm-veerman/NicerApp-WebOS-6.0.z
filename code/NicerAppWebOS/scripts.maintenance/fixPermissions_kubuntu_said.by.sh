#!/bin/bash
while true; do
  echo "fixPermissions_kubuntu_said.by.sh Running at $(date)"
  chown -R gavan:www-data /var/www/NicerAppWebOS-v6.y.z/domains/said.by/siteData
  chmod -R 770 /var/www/NicerAppWebOS-v6.y.z/domains/said.by/siteData
  sleep 1
done
