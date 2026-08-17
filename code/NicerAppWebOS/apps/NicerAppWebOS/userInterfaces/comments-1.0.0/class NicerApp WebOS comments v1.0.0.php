<?php

class class_NicerApp_WebOS_siteComments {


    public $version = '1.0.0';
    public $about = array(
        'whatsThis' => 'NicerApp #siteComments PHP class',
        'version' => '1.0.0',
        'history' => array (
            '1.y.z' => 'Initial version'
        ),
        'created' => 'Saturday, 21 May 2022 11:02 CEST',
        'copyright' => 'Copyright (c) 2023 by Rene A.J.M. Veerman <rene.veerman.netherlands@gmail.com>'
    );
    
    public $jsMe = 'na.apps.loaded[\'app.2D.comments.v1.0.0\']';
    public $cssTheme = 'darkmode';
    public $baseIndentLevel = 3;

    public function __construct() {
        $this->forumsIndexFilepath = dirname(__FILE__).'/settings/forumsIndex.json';
        //$this->forumsIndex = safeJSONload ($this->forumsIndexFilepath);
    }
    
    
    function filepath ($filename) {
        return realpath(dirname(__FILE__).'/../../../..').'/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments-1.0.0/'.$filename;
    }
    
    
    
    public function html_displayHEAD() {
        global $naWebOS;
        $il = 0;
        $theme = $this->cssTheme;
        $r = $naWebOS->html ($il, '<link type="text/css" rel="stylesheet" media="screen" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">');
        $r.= $naWebOS->html ($il, '<link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments-1.0.0/comments-globals.css?changed='.date('Ymd-His', filemtime(dirname(__FILE__).'/comments-globals.css')).'">');
        $r.= $naWebOS->html ($il, '<link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments-1.0.0/comments-'.$theme.'.css?changed='.date('Ymd-His', filemtime(dirname(__FILE__).'/comments-'.$theme.'.css')).'">');
        $r.= $naWebOS->html ($il, '<script type="text/javascript"  src="/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce/tinymce.min.js" onload="na.comments.onload_editorLoaded();"></script>');
        $r.= $naWebOS->html ($il, '<link rel="stylesheet" href="/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/skin.min.css">');
        $r.= $naWebOS->html ($il, '<script src="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-1.12.1/jquery-ui.js"></script>');
        $r.= $naWebOS->html ($il, '<script type="text/javascript"  src="/NicerAppWebOS/apps/NicerAppWebOS/userInterfaces/siteComments-1.0.0/naComments-1.0.0.source.js?changed='.date('Ymd-His', filemtime(dirname(__FILE__).'/naComments-1.0.0.source.js')).'"></script>');
        return $r;
    }
    
    
    public function html_displayEditor() {
        global $naWebOS;
        $il = $this->baseIndentLevel;
        $r  = $naWebOS->html($il, '<div class="naComments_HEAD"></div>');
        $r  .= $naWebOS->html($il, '<div class="naForums_header">');
            $r .= $naWebOS->html($il+1,    '<span>Comments</span>');
        $r .= $naWebOS->html($il, '</div>');
        
        //$r .= $this->displayForumList($il);
        
        $r .= $naWebOS->html ($il, '<div class="naForums_forumCategory_bottomSpacer">');
        $r .= $naWebOS->html ($il, '</div>');
        return $r;
    }
};

?>
