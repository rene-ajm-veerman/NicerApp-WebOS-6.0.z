#!/bin/bash
source ./setupReplication_credentials.sh

SOURCE="${SOURCE_HOST}analytics"
TARGET="${TARGET_HOST}analytics"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}errorhandling"
TARGET="${TARGET_HOST}errorhandling"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}logentries"
TARGET="${TARGET_HOST}logentries"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}data_by_users"
TARGET="${TARGET_HOST}data_by_users"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}views"
TARGET="${TARGET_HOST}views"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}cms_tree"
TARGET="${TARGET_HOST}cms_tree"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}cms_tree___user___guest"
TARGET="${TARGET_HOST}cms_tree___user___guest"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}cms_documents___role___guest"
TARGET="${TARGET_HOST}cms_documents___role___guest"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}cms_documents___user___guest"
TARGET="${TARGET_HOST}cms_documents___user___guest"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}themes"
TARGET="${TARGET_HOST}themes"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"

SOURCE="${SOURCE_HOST}app_2d_news__rss_items"
TARGET="${TARGET_HOST}app_2d_news__rss_items"
curl -X DELETE $TARGET
curl -X PUT $TARGET
curl -H 'Content-Type: application/json' -X POST $SERVER/_replicator -d \
  "{\"source\": \"$SOURCE\", \
    \"target\": \"$TARGET\", \
    \"continuous\": $continuous}"



#    'errorHandling',
 #   'logEntries',
  #  'data_by_users',
   # 'views',
    #'cms_tree',
    #'cms_tree___role___guests',
    #'cms_tree___user___'.$un1,
    #'cms_tree___user___guest',
    #'cms_documents___role___guests',
    #'cms_documents___user___'.$un1,
    #'cms_documents___user___guest',
    #'themes',
    #'api_wallpaperscraper__plugin_bingImages',
    #'api_wallpaperscraper__plugin_googleImages',
    #'app_2D_news__rss_items',
    #'app_2D_webmail__accounts',
    #'app_3D_fileManager__tree_d_positions'

