# ras.sh = reset all servers.sh
#!/bin/bash
set -x # echo on
service nginx stop
service apache2 stop
service couchdb stop
sleep 5 
service couchdb start
service apache2 start
service nginx start



