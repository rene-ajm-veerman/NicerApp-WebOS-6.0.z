#!/bin/bash
cd ../../../..
git add .
git commit -m "$1"
git push -u origin main
cd domains/nicer.app/NicerAppWebOS/scripts.maintenance
exit

#for f in $(ls ./do_upgrade_repositoriesFor_*.sh)
#do
    #echo "NOW UPGRADING VIA $f"
    #source $f
    #echo "DONE UPGRADING VIA $f"
#done


