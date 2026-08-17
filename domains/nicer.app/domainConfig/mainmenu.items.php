<?php 
    global $naWebOS;
    global $na_apps_structure;
    $na_apps_structure = array(
        // documentation
        "docs__overview" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'diskText' => [
                            'file' => '/NicerAppWebOS/documentation/NicerApp-WebOS--overview.php',
                            'title' => '/NicerAppWebOS/documentation/NicerApp-WebOS--overview.title.php',
                            'seoValue' =>  [ 'docs', 'docs-overview' ]
                        ]
                     ]
                ]
            ]
        ],

        "docs__license" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'diskText' => [
                            'file' => '/NicerAppWebOS/LICENSE.php',
                            'title' => '/NicerAppWebOS/LICENSE.title.php',
                            'seoValue' =>  [ 'license', 'LICENSE', 'docs-license' ]
                        ]
                     ]
                ]
            ]
        ],
        "docs__todoList" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'diskText' => [
                            'file' => '/NicerAppWebOS/documentation/NicerApp-WebOS--todoList--v5.y.z.php',
                            'title' => '/NicerAppWebOS/documentation/NicerApp-WebOS--todoList.title.php',
                            'seoValue' =>  [ 'todo', 'docs-todoList' ]
                        ]
                     ]
                ]
            ]
        ],
        "docs__company" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'diskText' => [
                            'file' => '/NicerAppWebOS/documentation/NicerApp--company.php',
                            'title' => '/NicerAppWebOS/documentation/NicerApp--company.title.php',
                            'seoValue' =>  [ 'company', 'co' ]
                        ]
                     ]
                ]
            ]
        ],
        "docs__musicCopyrightDisclaimer" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'diskText' => [
                            'file' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering/copyright.php',
                            'title' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/musicPlayer.javascriptRendering/copyright.title.php',
                            'seoValue' =>  [ 'musicCopyrightDisclaimer' ]
                        ]
                     ]
                ]
            ]
        ],

        "docs__geopolitics" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'diskText' => [
                            'file' => '/NicerAppWebOS/documentation/NicerEnterprises--geopolitics.php',
                            'title' => '/NicerAppWebOS/documentation/NicerEnterprises--geopolitics.title.php',
                            'seoValue' =>  'geopolitics'
                        ]
                     ]
                ]
            ]
        ],

        "docs__memoires" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'diskText' => [
                            'file' => '/NicerAppWebOS/documentation/NicerEnterprises--ownersMemoires.php',
                            'title' => '/NicerAppWebOS/documentation/NicerEnterprises--ownersMemoires.title.php',
                            'seoValue' =>  ['ownersMemoires', 'memoires', 'om']
                        ]
                     ]
                ]
            ]
        ],

        "bb" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'backgroundsBrowser' => [
                            'page' => 'index',
                            'seoValue' => [ 'backgroundsBrowser', 'backgroundBrowser', 'bb', 'wallpapersBrowser', 'wallpaperBrowser', 'wb' ]
                        ]
                     ]
                ]
            ]
        ],

        // content management system
        "cms" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'cmsManager' => [
                            'page' => 'index',
                            'seoValue' => [ 'me' ]
                        ]
                     ]
                ]
            ]
        ],
        "cms_listUsers" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/content-management-systems/NicerAppWebOS'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'usersGroupManager-0.1.0' => [
                            'page' => 'index',
                            'seoValue' => [ 'listUsers' ]
                        ]
                     ]
                ]
            ]
        ],



        // news app :
        "newsHeadlines_englishNews" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    'apps' => [
                        'news' => [ // a more JSON and shorter way of declaring the leaf section of a tree-like JSON array.
                            'section' => 'English_News', // key pointing to value
                            'seoValue' =>  'news' // same here
                        ]
                     ]
                ]
            ]
        ],
        "newsHeadlines_englishNews_worldHeadlines" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'news' => [ // a more JSON and shorter way of declaring a tree-like JSON array.
                            'section' => 'English_News__World_Headlines',
                            'seoValue' => [ 'world-news', 'news-world', 'world-headlines', 'news-world-headlines' ]
                        ]
                     ]
                ]
            ]
        ],
        "newsHeadlines_englishNews_businessHeadlines" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'news' => [
                            'section' => 'English_News__Topics__Business',
                            'seoValue' => [ 'nbh', 'news-business', 'business', 'business-news', 'news-business-headlines' ]
                        ]
                     ]
                ]
            ]
        ],
        "newsHeadlines_nederlandsNieuws" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'news' => [
                            'section' => 'Nederlands_Nieuws',
                            'seoValue' => 'nieuws'
                        ]
                     ]
                ]
            ]
        ],
        "newsHeadlines_nederlandsNieuws_wereldNieuws" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'news' => [ 
                            'section' => 'Nederlands_Nieuws__Wereld',
                            'seoValue' => 'nieuws-internationale-headlines'
                        ]
                     ]
                ]
            ]
        ],
        
        "newsHeadlines_deutscheNachrichten" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    'misc' => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    'apps' => [
                        'news' => [ 
                            'section' => 'Deutsche_nachrichten',
                            'seoValue' => 'nachrichten'
                        ]
                     ]
                ]
            ]
        ],
        "newsHeadlines_arabic" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D',
                       'additionalCodeLocations' => [
                            'files' => [
                                '.../NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/class.newsApp-3.php' => [

                                ],
                                '.../NicerAppWebOS/apps/NicerAppWebOS/applications/2D/news/functions.php' => [

                                ]
                            ]
                       ]
                    ],
                    "apps" => [
                        'news' => [ 
                            'section' => 'Arabic',
                            'seoValue' => 'Arabic-news-English'
                        ]
                     ]
                ]
            ]
        ],

        "wikipedia.org" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        '3rd-party-site.wikipedia.org' => [
                            'page' => 'index',
                            'seoValue' => [ 'wiki', 'wikipedia', 'wikipedia.org' ]
                        ]
                     ]
                ]
            ]
        ],

        "grokipedia.com" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                        'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        '3rd-party-site.grokipedia.com' => [
                            'page' => 'index',
                            'seoValue' => [ 'grokipedia', 'gp' ]
                        ]
                    ]
                ]
            ]
        ],

        "tarot" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'cardgame.tarot' => [
                            'deck' => '/Waite-Smith/Pam Colman Smith/Original Rider Waite',
                            "reading" => "3-Cards",
                            'seoValue' => [ 'tarot', 'cardgame-tarot' ]
                        ]
                     ]
                ]
            ]
        ],
        
        "music" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                        'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'musicPlayer.beatPulse' => [
                            'set' => 'index',
                            'seoValue' => [ 'music-2D-beatPulse', 'music' ]
                        ]
                    ]
                ]
            ]
        ],

        "music.fancy" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'musicPlayer.fancy.latest.2D' => [
                            'set' => 'index',
                            'seoValue' => [ 'music-2D-fancy', 'music-fancy' ]
                        ]
                     ]
                ]
            ]
        ],

        // signalsManager (tiny) controls : tasksManager (large) + filePhoenix (large) + data[bases]Phoenix (large)
        "signalsManager" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/businessLogic'
                    ],
                    "apps" => [
                        'signalsManager' => [
                            'seoValue' => ['signalsManager', 'signalsManager']
                        ]
                     ]
                ]
            ]
        ],
        "dataPhoenix" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/businessLogic'
                    ],
                    "apps" => [
                        'dataPhoenix' => [
                            'seoValue' => ['dataPhoenix', 'DataPhoenix']
                        ]
                     ]
                ]
            ]
        ],
        "filePhoenix" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/businessLogic'
                    ],
                    "apps" => [
                        'filePhoenix' => [
                            'seoValue' => ['filePhoenix', 'FilePhoenix']
                        ]
                     ]
                ]
            ]
        ],
        "tasks" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/businessLogic/tasksManager'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'manager' => [
                            'page' => 'index',
                            'seoValue' => ['tasksManager', 'TasksManager']
                        ]
                     ]
                ]
            ]
        ],




        // outdated, depcrated :
        "analytics" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'analytics' => [
                            'seoValue' => 'analytics'
                        ]
                     ]
                ]
            ]
        ],


        // 3D early demos
        "3Dcube" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/3D'
                    ],
                    "apps" => [
                        'demo.3D.cube' => [
                            'parameters' => [],
                            'seoValue' => 'demo-3D-cube'
                        ]
                     ]
                ]
            ]
        ],
        "3Dmodels" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/3D'
                    ],
                    "apps" => [
                        'demo.3D.modelLoading' => [
                            'parameters' => [],
                            'seoValue' => 'demo-3D-models'
                        ]
                     ]
                ]
            ]
        ],
        "backgroundsBrowser" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/3D'
                    ],
                    "apps" => [
                        'app.3D.fileExplorer' => [
                            'parameters' => [
                                'thumbnails' => './thumbs/300/$filename.$ext'
                            ],
                            'seoValue' => [ 'demo-3D-fileBrowser', '3D-data', '3D', '3d' ]
                        ]
                     ]
                ]
            ]
        ],
        

        "webmail" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'webmail' => [
                            'page' => 'index',
                            'seoValue' => 'mail'
                        ] 
                     ]
                ]
            ]
        ],
        
        "forums__view_index" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'forums__view_index' => [
                            'page' => 'index',
                            'seoValue' => 'forums-configuration'
                        ] 
                     ]
                ]
            ]
        ],
        
        
        "jsonViewer" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D/ui'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'jsonViewer' => [
                            'page' => 'index',
                            'seoValue' => [ 'jv', 'jsonViewer' ]
                        ]
                     ]
                ]
            ]
        ],

        "logs" => [
            'apps-current' => [
                'NicerApp-WebOS__pageSettings' => [
                    "misc" => [
                       'folder' => '/NicerAppWebOS/apps/NicerAppWebOS/applications/2D'
                    ],
                    "apps" => [
                        'meta' => [
                            'mustBeLoggedIn' => true
                        ],
                        'logs' => [
                            'page' => 'index',
                            'seoValue' => 'logs'
                        ]
                     ]
                ]
            ]
        ]


        
     );
             
				
global $naURLs;
$naURLs = array();
foreach ($na_apps_structure as $pageID => $pageStructure) {
foreach ($pageStructure as $viewKeyID => $viewKeySettings) {
foreach ($viewKeySettings as $softwareKey => $softwareKeySettings) {
foreach ($softwareKeySettings as $softKey => $softSettings) {

    if ($softKey==='misc') {
        $folder = $softSettings['folder'];
    }
    if ($softKey==='apps') {
    
        foreach ($softSettings as $viewFolderName => $viewSettings) {
        
            if (array_key_exists('seoValue', $viewSettings)) {
                    if (is_array($viewSettings['seoValue'])) {
                        foreach ($viewSettings['seoValue'] as $seovIdx => $seoValue) {
                            $url = '/'.$seoValue;
                            $json = json_encode ($viewSettings);
                            $naURLs[$pageID] = $url; // will get modified into /apps-content/.* links by .../.htaccess and fed from that .htaccess to .../ajax_get_content.php 
                            break;
                        }
                    } else {
                        $url = '/'.$viewSettings['seoValue'];
                        $json = json_encode ($softwareKeySettings['apps']);
                        $naURLs[$pageID] = $url;
                    }
            } else {
                if (false) {
                    echo '<pre style="color:red">';
                    echo $folder.PHP_EOL;
                    var_dump ($softwareKeySettings);
                    echo '</pre>';
                }
                $naURLs[$pageID] = '/views/'.encode_base64_url(json_encode($softwareKeySettings['apps'])); // will get modified into /apps-content/.* links by na.site.transformLinks() in JS.
            }
        }
    }

                   
}
}
}
}  
?>
