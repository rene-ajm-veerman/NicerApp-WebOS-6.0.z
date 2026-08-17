<html>
<head>
    <title>Example 1</title>
    <link rel="stylesheet" href="index.css"/>

    <!-- this is how you make an HTML code comment -->
    
    <script   src="https://code.jquery.com/jquery-1.12.4.js"   integrity="sha256-Qw82+bXyGq6MydymqBxNPYTaUXXq7c8v3CwiYwLLNXU="   crossorigin="anonymous"></script> <!-- from https://code.jquery.com/ -->
    <script type="text/javascript" src="index.js"></script> <!-- index.js is your own javascript that uses the jQuery library included 1 line above here -->
</head>
<body onload="mySite.bodyOnload();">
    <div id="siteHeader" class="siteDiv siteHeaderFooter">
    #siteHeader
    </div>
    
    <div id="siteLeftbar" class="siteDiv siteSidebar">
    #siteLeftbar
    </div>

    <div id="siteRightbar" class="siteDiv siteSidebar">
    #siteRightbar
    </div>
    
    <div id="siteContent" class="siteDiv">
    #siteContent
    </div>
    
    <div id="siteFooter" class="siteDiv siteHeaderFooter">
    #siteFooter
    </div>
</body>
</html>