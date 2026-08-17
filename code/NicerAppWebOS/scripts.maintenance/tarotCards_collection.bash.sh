#!/bin/bash

START="/var/www/nicer.app-5.10.z/code/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/cardgame.tarot/appContent/tarotSite/decks/"   # ← Change if needed

cd "$START" || exit 1

echo "Lowercasing all back.jpg files recursively..."

find . -type f -iname "back.jpg" -print0 | while IFS= read -r -d '' file; do
    dir=$(dirname "$file")
    oldname=$(basename "$file")
    newname="back.jpg"

    if [ "$oldname" != "$newname" ]; then
        echo "Renaming: $file → $newname"
        mv -i "$file" "$dir/$newname"   # -i asks before overwriting (safe)
    fi
done

echo "Done!"
