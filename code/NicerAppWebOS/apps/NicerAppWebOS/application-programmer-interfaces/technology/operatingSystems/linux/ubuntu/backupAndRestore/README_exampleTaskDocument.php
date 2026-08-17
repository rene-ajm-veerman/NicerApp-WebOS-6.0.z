 <?php 
//--- the todo list for ALL backup and restore operations running on the webserver for a NicerApp CMS gets stored PER TASK in a couchdb document, 
//--- in the YOURDOMAIN_TLD___backup_and_restore table, which is readable *and* writeable only by NicerApp CMS users with a role 'administrators' listed in
//--- the roles fiels of their specific userID document in the _users couchdb table that governs their NicerApp CMS user account.

//--- in order to facilitate progress bars in the browser during the zipping & unzipping processes on the server,
//--- NicerApp uses the php7.4-zip ubuntu OS library.
$example_taskDocument = [
    'operationType' => 'backup', // or 'restore'
    
    'id' => [
        'sourceAddress' => 'valid external IP address or fully qualified domain name',
        'User-Agent' => 'User-Agent string of the browser on the device that is issuing this backup OR restore command',
        'UserID' => 'NicerApp CMS UserID that issuing this backup or restore command'
    ],
    
    'settingsForProcessingOfData' => [
        'couchdb_sourceAddress' => 'some LAN or outside-world IP Address or fully qualified domain name',
        'couchdb_destinationAddress' => 'some LAN or outside-world IP Address or fully qualified domain name',
        
        'zipFile' => '/path/to/uploadedZipFileToRestore_or_zipFileToCreateAsOutputOfBackupProcedure.zip',
        
        'zipFile_contents_sources' => [ // only gets used for $example_taskDocument['operationType'] == 'backup'
        
            'outputFilepath' => '/path/to/fullyFinishedZipFile.zip',
            
            //-- where-ever you see a list with only 0 => and 1 => listing an item 1 and an item N, 
            //-- you are dealing with a list of any number of items in it, as is quite usual for 'flat list arrays'.
            'phases' => [
            
                'database contents' => [
                    'phaseType' => 'CouchDB',
                    'stages' => [
                        'calculate size of phase' => [ 
                            'stageType' => 'calculate_size',
                            'tableList' => [
                                0 => 'fully qualified couchdb table name 1',
                                1 => 'fully qualified couchdb table name N'
                            ]
                        ],
                        
                        // this ['phases'][0]['stages'][1] would use ONLY 'zipping up data' OR 'synchronising data' that's listed below here.
                        // which, that gets determined by a user-interface form in the web browser that created this $taskDocument.
                        'zipping up data' => [
                            'stageType' => 'CouchDB_zipping',
                            'outputFilepath' => '/path/to/fullyFinishedZipFile_forThisStage.zip'
                        ],
                        'synchronising data' => [
                            'stageType' => 'CouchDB_synchronise_to_other_server'                                
                            // would use ['settingsForProcessingOfData']['couchdb_sourceAddress'] and 
                            // ['settingsForProcessingOfData']['couchdb_destinationAddress'] as well!
                        ]
                    ]
                ],
                
                'emails' => [
                    'phaseType' => 'IMAP_Maildirs',
                    'stages' => [
                        'zip Maildirs' => [ 
                            'stageType' => 'calculate_size',

                            // the size is the number of files, NOT the total number of bytes, for ALL Maildirs! 
                            // otherwise the entire server grinds to a halt during this stage!
                            'folders' => [
                                0 => '/path/to/Maildir-1',
                                1 => '/path/to/Maildir-N'
                            ],
                            
                            'filepathToFoldersIndexFile' => '/path/to/indexFile.json'
                        ]
                    ]
                ],
                
                'backgrounds' => [
                    'phaseType' => 'FileSystem_backgrounds',
                    'stages' => [
                        'calculate size of phase' => [
                            'stageType' => 'calculate_size',

                            'folders' => [
                                0 => '/path/to/backgroundsFolder-1',
                                1 => '/path/to/backgroundsFolder-N'
                            ],
                            
                            // the following JSON file will contain a flat list of all the files that need to be moved into 
                            // all of the paths listed in ['phases'][2]['stages'][0]['folders']
                            'filepathToFoldersIndexFile' => '/path/to/indexFile.json'
                        ]
                        
                    ]
                ]
            ]
        ],
        
        'zipFile_contents_destinations' => [ // only gets used for $example_taskDocument['operationType'] == 'restore'
            'sourceFilepath' => '/path/to/fullyUploadedZipFile.zip',
            'phases' => [
                'database contents' => [
                    'phaseType' => 'CouchDB',
                    'stages' => [
                        'calculate size of phase' => [ 
                            'stageType' => 'calculate_size',
                            'tableList' => [
                                0 => 'fully qualified couchdb table name 1',
                                1 => 'fully qualified couchdb table name N'
                            ]
                        ],
                        
                        // this ['phases'][0]['stages'][1] would use ONLY 'zipping up data' OR 'synchronising data' that's listed below here.
                        // which, that gets determined by a user-interface form in the web browser that created this $taskDocument.
                        'zipping up data' => [
                            'stageType' => 'couchdb_createZipFile',
                            'outputFilepath' => '/path/to/fullyFinishedZipFile_forThisStage.zip'
                        ],
                        'synchronising data' => [
                            'stageType' => 'CouchDB_synchronise_to_other_server'                                
                            // would use ['settingsForProcessingOfData']['couchdb_sourceAddress'] and 
                            // ['settingsForProcessingOfData']['couchdb_destinationAddress'] as well!
                        ]
                    ]
                ],
                
                'emails' => [
                    'phaseType' => 'IMAP_Maildirs',
                    'stages' => [
                        'move Maildirs contents' => [ 
                            'stageType' => 'FileSystem_moveFoldersContents',

                            // the size is the number of files, NOT the total number of bytes, for ALL Maildirs! 
                            // otherwise the entire server grinds to a halt during this stage!
                            'folders' => [
                                0 => '/path/to/destinationMaildir-1',
                                1 => '/path/to/destinationMaildir-N'
                            ],
                            
                            // the following JSON file will contain a flat list of all the files that need to be moved into ['phases'][1]['stages'][0]['folders']
                            'filepathToFoldersIndexFile' => '/path/to/indexFile.json' 
                        ]
                    ]
                ],
                
                'backgrounds' => [
                    'phaseType' => 'FileSystem_backgrounds',
                    'stages' => [
                        'move background files to proper locations' => [
                            'stageType' => 'FileSystem_moveFoldersContents',

                            'sourceFilepaths' => [
                                0 => '/path/to/folder1',
                                1 => '/path/to/folderN'
                            ],
                            
                            'destinationFilepaths' => [
                                0 => '/path/to/folder_XYZ_1',
                                1 => '/path/to/folder_ABC_N'
                            ],

                            // the following JSON file will contain a flat list of all the files that need to be moved into 
                            // all of the paths listed in ['phases'][2]['stages'][0]['sourceFilepaths'] and 
                            // ['phases'][2]['stages'][0]['destinationFilepaths']
                            'filepathToFoldersIndexFile' => '/path/to/indexFile.json' 
                      ],
                        
                        'delete cached backgrounds index files' => [ ],
                        
                        'recalculate backgrounds index files' => [ ]
                    ]
                ]
            ]
        ]
    ]
];
?>
