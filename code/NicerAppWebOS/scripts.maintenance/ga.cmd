git add .
set /p COMMITMSG="Commit message=? "
git commit -m "%COMMITMSG%"
git push -u origin main

rem for f in $(ls ./do_upgrade_repositoriesFor_*.sh)
rem do
rem     echo "NOW UPGRADING VIA $f"
rem     source $f
rem     echo "DONE UPGRADING VIA $f"
rem done


