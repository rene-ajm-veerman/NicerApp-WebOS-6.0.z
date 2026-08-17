#!/bin/bash
NA_MAIN_HTDOCS_RELATIVE_PATH="/var/www/nicer.app-5.10.z/domains/said.by"
NA_MAIN_SITE_FOLDER=""
NA_MAIN_GROUP="www-data"
NA_MAIN_USER="gavan"

NA_MAIN_PERMISSIONS=770 # PHP needs this +x permission to load scripts!
NA_USERDATA_PERMISSIONS=770
NA_SHELLSCRIPTS_PERMISSIONS=700
NA_SITE_APPS=( restart_allApps.sh )
