<?php 
    require_once ('functions.php');
    if (!array_key_exists('content', $_GET)) {
        $_GET['content'] = 'frontpage';
    };
    $content = dirname(__FILE__).'/content/'.$_GET['content'].'.php';
    
    $cdbConfigFile = dirname(__FILE__).'/couchdb.json';
    $cdbConfig = json_decode(file_get_contents($cdbConfigFile), true);
    unset ($cdbConfig['adminUsername']);
    unset ($cdbConfig['adminPassword']);
    
    $ip = array_key_exists('X-Forwarded-For',apache_request_headers())
        ? apache_request_headers()['X-Forwarded-For'] 
        : $_SERVER['REMOTE_ADDR'];
    
    ob_start();
    if (array_key_exists('siteTheme', $_COOKIE)) {
        $siteTheme = $_COOKIE['siteTheme'];
    } else if (array_key_exists('siteTheme',$_POST)) {
        $siteTheme = $_POST['siteTheme'];
    } else {
        $siteTheme = 'light';
    }
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <!--
        LICENSE :
        
        Copyright (c) and All Rights Reserved (r) 2021 EXCLUSIVELY TO RENE A.J.M. VEERMAN.
        
        THE FINE FOR USING THIS CODE ANYWHERE BUT HTTPS://NICER.APP OR HTTPS://ZONED.AT IS SET AT
        ONE MILLION US DOLLAR PER MONTH.
    -->
    <title>zoned.at url link shortener</title>
    
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Karla:wght@300&family=PT+Sans&display=swap" rel="stylesheet">
    
    <script type="text/javascript" src="3rd-party/jquery-3.5.1.min.js"></script>
    
    <link rel="stylesheet" type="text/css" href="3rd-party/tooltipster-master/dist/css/tooltipster.bundle.min.css" />
    <script type="text/javascript" src="3rd-party/tooltipster-master/dist/js/tooltipster.bundle.min.js"></script>
    
    <link href="content.css?c=<?php echo date('Ymd_His', filemtime('content.css'));?>" rel="stylesheet">
    <script type="text/javascript" src="zat.source.js?c=<?php echo date('Ymd_His', filemtime('zat.source.js'));?>"></script>
    
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
    <link rel="manifest" href="favicon/site.webmanifest">
    <link rel="mask-icon" href="favicon/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">    
</head>
    <script type="text/javascript">
        zat.globals.clientIP = '<?php echo $ip;?>';
    </script>
<body theme="<?php echo $siteTheme;?>" onload="zat.onload();">
    <!--
    <button class="login" id="btnLogin" onclick="zat.displayLoginDialog();">
        <span>Login</span>
    </button>
    <button class="login" id="btnRegister" onclick="zat.registerNewAccount();">
        <span>Register</span>
    </button>
    -->

    <div class="flex-container">
    <div class="row">
    <div class="flex-item">
        <?php echo content($content);?>
    </div> <!-- class="flex-item" -->
    </div> <!-- class="row" -->
    </div> <!-- class="flex-container" -->
</body>
</html>
