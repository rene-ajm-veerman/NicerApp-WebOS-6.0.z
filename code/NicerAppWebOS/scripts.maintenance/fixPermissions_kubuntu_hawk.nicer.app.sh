#!/bin/bash
while true; do
  mode=$(stat -c %a /run/user/1000)
  if [ "$mode" != "700" ]; then
    echo "$(date): /run/user/1000 is now $mode !"
    # optional: automatically fix it
    chmod 700 /run/user/1000
  fi
  mode=$(stat -c %a /tmp/runtime-gavan)
  if [ "$mode" != "700" ]; then
    echo "$(date): /tmp/runtime-gavan is now $mode !"
    # optional: automatically fix it
     chmod 700 /tmp/runtime-gavan
  fi
  mode=$(stat -c %a /tmp/snap-private-tmp)
  if [ "$mode" != "700" ]; then
    echo "$(date): /tmp/snap-private/tmp is now $mode !"
    # optional: automatically fix it
  	chmod -R 700 /tmp/snap-private-tmp/
  	chown -R root:root /tmp/snap-private-tmp/
  fi
 
  chmod 755 /usr /usr/bin /usr/bin/readlink /usr/bin/dirname /usr/bin/cat /bin/bash
  chown root:root /usr/bin/readlink /usr/bin/dirname /usr/bin/cat
  sleep 5
done
