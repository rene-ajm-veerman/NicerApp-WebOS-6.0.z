<?php 
    require_once (realpath(dirname(__FILE__).'/../../../../..').'/NicerAppWebOS/boot.php');
    require_once (dirname(__FILE__).'/../boot.php');
    global $naForums;
    
    $theme = $naForums->cssTheme;
    if (array_key_exists('cdb_loginName', $_SESSION) && ($_SESSION['cdb_loginName']!=='' || $_SESSION['cdb_loginName']!=='Guest')) {
        //$theme = $_SESSION['cdb_loginName'];
    };
    //if (array_key_exists('theme',$_POST)) $theme = $_POST['theme'];
    //if (array_key_exists('theme',$_GET)) $theme = $_GET['theme'];
    
?>
<!--<html>
<head>-->
    <link type="text/css" rel="stylesheet" media="screen" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!--<link type="text/css" rel="stylesheet" media="screen" href="/NicerAppWebOS/index.css">-->
    <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/nicer.app/app.2D.forums/forums-default.css?changed=<?php echo $naForums->currentDateTimeStamp();?>">
    <link type="text/css" rel="StyleSheet" media="screen" href="/NicerAppWebOS/apps/nicer.app/app.2D.forums/forums-<?php echo $theme?>.css?changed=<?php echo $naForums->currentDateTimeStamp();?>">
    <!-- see fonts.google.com -->
    <link href="https://fonts.googleapis.com/css?family=ABeeZee|Aclonica|Acme|Actor|Advent+Pro|Akronim|Alex+Brush|Architects+Daughter|Archivo+Black|Baloo|Bebas+Neue|Caveat|Chewy|Cookie|Cormorant|Courgette|Covered+By+Your+Grace|Dancing+Script|El+Messiri|Exo|Exo+2|Galada|Gloria+Hallelujah|Great+Vibes|Handlee|Indie+Flower|Kalam|Kaushan+Script|Khula|Knewave|Krona+One|Lacquer|Lemonada|Lusitana|M+PLUS+1p|Marck+Script|Merienda+One|Modak|Montserrat|Montserrat+Alternates|Mr+Dafoe|Nanum+Pen+Script|Noto+Serif+JP|Odibee+Sans|Oleo+Script|Orbitron|PT+Sans|Parisienne|Pathway+Gothic+One|Permanent+Marker|Playball|Pridi|Quattrocento+Sans|Rock+Salt|Sacramento|Saira+Condensed|Saira+Extra+Condensed|Saira+Semi+Condensed|Satisfy|Shadows+Into+Light|Shadows+Into+Light+Two|Sigmar+One|Signika+Negative|Slabo+27px|Source+Code+Pro|Special+Elite|Spectral|Spinnaker|Sriracha|Unica+One|Acme|Lato:300,300i,400,400i|Montserrat|Mukta+Malar|Ubuntu|Indie+Flower|Raleway|Pacifico|Fjalla+One|Work+Sans|Gloria+Hallelujah&display=swap" rel="stylesheet"> <!-- see index.css and na.site.code.source.js (tinyMCE section) for where it's (primarily) used -->
    <script type="text/javascript"  src="/NicerAppWebOS/3rd-party/tinymce-4.9.11/js/tinymce/tinymce.min.js"></script>
    <link rel="stylesheet" href="/NicerAppWebOS/3rd-party/tinymce-4/themes/charcoal/skin.min.css">
    <script src="/NicerAppWebOS/3rd-party/jQuery/jquery-ui-1.12.1/jquery-ui.js"></script>
    <script type="text/javascript"  src="/NicerAppWebOS/apps/nicer.app/app.2D.forums/naForums-1.0.0.source.js?changed=<?php echo $naForums->currentDateTimeStamp();?>"></script>
    <!--<script src="https://smtpjs.com/v3/smtp.js"></script>-->
<!--</head>
<body onload="$(document).ready(function() { debugger; nawm.onload()});" onresize="naWebMail.onresize(event);">-->

<!--</body>
</html>-->
