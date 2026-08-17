<?php 
    global $naWebOS;
    $views = array(
        'newsHeadlines_englishNews' => array(
            //'#siteContent' => 'nicerapp/news/newsApp.siteContent.php?section=English%20News',
            //'news' => array ('section' => 'English_News')
            'app.2D.news.v2.2.0' => array (
                'section' => 'English_News'
            )
        ),
        'newsHeadlines_englishNews_worldHeadlines' => array(
            //'#siteContent' => 'nicerapp/news/newsApp.siteContent.php?section=English%20News%20World%20Headlines',
            //'news' => array ('section' => 'English_News__World_Headlines')
            'app.2D.news.v2.2.0' => array (
                'section' => 'English_News__World_Headlines'
            )
        ),
        'newsHeadlines_nederlandsNieuws' => array (
            'app.2D.news.v2.2.0' => array (
                'section' => 'Nederlands_Nieuws'
            )
        ),
        'newsHeadlines_nederlandsNieuws_wereldNieuws' => array (
            'app.2D.news.v2.2.0' => array (
                'section' => 'Nederlands_Nieuws__Wereld'
            )
        ),
        'newsHeadlines_deutscheNachrichten' => array (
            'app.2D.news.v2.2.0' => array (
                'section' => 'Deutsche_nachrichten'
            )
        ),
        'newsHeadlines_arabic' => array (
            'app.2D.news.v2.2.0' => array (
                'section' => 'Arabic'
            )
        ),
        'analytics' => array (
            'app.analytics' => array()
        ),
        '3Dcube' => array (
            '3D.demo.cube' => array()
        ),
        '3Dmodels' => array (
            '3D.demo.modelLoading' => array()
        ),
        'backgroundsBrowser' => array (
            'app.3D.fileExplorer' => array()
        ),
        'music' => array (
            'app.musicPlayer' => array (
                'set' => 'index'
            )
        ),
        'cms' => array (
            'meta' => array (
                'mustBeLoggedIn' => true
            ),
            'cms' => array (
                'page' => 'index'
            )
        ),
        'naThemeViewer' => array (
            'naThemeViewer' => array (
                'page' => 'index'
            )
        ),
        'verticalMenuDemo' => array (
            'EXAMPLE_vertical_vividMenu' => array (
                'page' => 'index'
            )
        ),
        
        'imageSearch_google' => array (
            'wallpaperScraper' => array (
                'page' => 'index'
            )
        ),
        
        'imageDescriber_clarifai' => [
            'engine.imageDescriber' => [
                'page' => 'index'
            ]
        ],
        
        'naturalLanguageEngine' => [
            'engine.naturalLanguageEngine' => [
                'page' => 'index'
            ]
        ],
		
		'diskText_books by Rene AJM Veerman_human rights - an urgent request for poly-amourism to be recognized as a legitimate form of marriage' => [
			'diskText_books by Rene AJM Veerman_human rights - an urgent request for poly-amourism to be recognized as a legitimate form of marriage' =>
				'page' => 'frontpage'
			]
		],

		'diskText_books by Rene AJM Veerman_medicine - how to fix the vast and gross humanrights abuses in early 21st-century psychiatry world-wide' => [
			'diskText_books by Rene AJM Veerman_medicine - how to fix the vast and gross humanrights abuses in early 21st-century psychiatry world-wide' =>
				'page' => 'frontpage'
			]
		],
		
		'diskText_books by Rene AJM Veerman_manual - telepathy' => [
			'diskText_books by Rene AJM Veerman_manual - telepathy' =>
				'page' => 'frontpage'
			]
		],
		
		'diskPhotoAlbum_books by Rene AJM Veerman_evidence-photoAlbum - weed marijuana - anti cancer' => [
			'diskPhotoAlbum_books by Rene AJM Veerman_evidence-photoAlbum - weed marijuana - anti cancer' => [
				'page' => 'frontpage'
			]
		],
		
		'forms_for financial support for victims of psychiatry'] => [
			'forms_for financial support for victims of psychiatry' =>
				'page' => 'frontpage'
			]
		]
    );
    $json = array();
    $urls = array();
    foreach ($views as $viewName => $viewSettings) {
        $json[$viewName] = json_encode($viewSettings);
        $urls[$viewName] = '/apps/'.base64_encode_url($json[$viewName]);
    };
?>
