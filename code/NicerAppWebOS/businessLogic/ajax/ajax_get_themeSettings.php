<?php
require_once (realpath(dirname(__FILE__).'/../').'/boot.php');
global $naWebOS;
/*
 * v5.3 : nicer_app___data_themes document :
{
  "_id": "LunqcbUYkyatk1tNT1sM",
  "_rev": "1-6fc13132187c67a693233d8ce6bbc2f7",
  "theme": "default",
  "textBackgroundOpacity": 0.5,
  "dialogs": {
    "#siteDateTime": {
      "border": "0px none rgb(255, 255, 255) ",
      "borderRadius": "10px 10px 10px 10px ",
      "boxShadow": "rgba(0, 0, 0, 0.55) 2px 2px 3px 3px",
      "color": "rgb(255, 255, 255)",
      "fontSize": "16px",
      "fontWeight": "700",
      "fontFamily": "Open Sans, sans-serif",
      "textShadow": "rgba(0, 0, 0, 0.5) 3px 3px 2px"
    },
    "#siteDateTime > .vdBackground": {
      "opacity": "1",
      "background": "rgba(0, 0, 0, 0.35)",
      "borderRadius": "10px 10px 10px 10px "
    },
    "#siteDateTime td": [],
    "#siteContent": {
      "border": "0px none rgb(255, 255, 255) ",
      "borderRadius": "20px 20px 20px 20px ",
      "boxShadow": "rgba(0, 0, 0, 0.55) 2px 2px 3px 3px",
      "color": "rgb(255, 255, 255)",
      "fontSize": "15.593px",
      "fontWeight": "400",
      "fontFamily": "Open Sans, sans-serif",
      "textShadow": "rgba(0, 0, 0, 0.5) 3px 3px 2px"
    },
    "#siteContent > .vdBackground": {
      "opacity": "1",
      "background": "rgba(0, 0, 0, 0.39)",
      "borderRadius": "20px 20px 20px 20px "
    },
    "#siteContent td": [],
    "#siteStatusbar": {
      "border": "0px none rgb(255, 255, 255) ",
      "borderRadius": "20px 20px 20px 20px ",
      "boxShadow": "rgba(0, 0, 0, 0.55) 2px 2px 3px 3px",
      "color": "rgb(255, 255, 255)",
      "fontSize": "15.593px",
      "fontWeight": "700",
      "fontFamily": "Open Sans, sans-serif",
      "textShadow": "rgba(0, 0, 0, 0.7) 3px 3px 2px"
    },
    "#siteStatusbar > .vdBackground": {
      "opacity": "0.5",
      "background": "rgba(0, 0, 0, 0) url(\"https://nicer.app/siteMedia/backgrounds/tiled/blue/363806009_0a1edd40dd.jpg\") repeat scroll 0% 0% / auto padding-box border-box",
      "borderRadius": "20px 20px 20px 20px "
    },
    "#siteStatusbar td": {
      "fontSize": "15.593px",
      "fontWeight": "700",
      "fontFamily": "Open Sans, sans-serif",
      "textShadow": "rgba(200, 255, 200, 0.7) 0px 0px 5px, rgba(0, 0, 0, 0.95) 0px 0px 3px, rgba(0, 0, 0, 0.95) 3px 3px 4px"
    },
    "#siteToolbarLeft": {
      "border": "0px none rgb(255, 255, 255) ",
      "borderRadius": "20px 20px 20px 20px ",
      "boxShadow": "rgba(0, 0, 0, 0.55) 2px 2px 3px 3px",
      "color": "rgb(255, 255, 255)",
      "fontSize": "15.593px",
      "fontWeight": "400",
      "fontFamily": "Open Sans, sans-serif",
      "textShadow": "rgba(0, 0, 0, 0.5) 3px 3px 2px"
    },
    "#siteToolbarLeft > .vdBackground": {
      "opacity": "1",
      "background": "rgba(0, 0, 0, 0.35)",
      "borderRadius": "20px 20px 20px 20px "
    },
    "#siteToolbarLeft td": [],
    "#siteToolbarThemeEditor": {
      "border": "0px none rgb(255, 255, 255) ",
      "borderRadius": "20px 20px 20px 20px ",
      "boxShadow": "rgba(0, 0, 0, 0.55) 2px 2px 3px 3px",
      "color": "rgb(255, 255, 255)",
      "fontSize": "15.593px",
      "fontWeight": "400",
      "fontFamily": "Open Sans, sans-serif",
      "textShadow": "rgba(0, 0, 0, 0.5) 3px 3px 2px"
    },
    "#siteToolbarThemeEditor > .vdBackground": {
      "opacity": "1",
      "background": "rgba(0, 0, 0, 0.35)",
      "borderRadius": "20px 20px 20px 20px "
    },
    "#siteToolbarThemeEditor td": []
  },
  "apps": [],
  "backgroundSearchKey": "landscape",
  "background": "/siteMedia/backgrounds/landscape/scenery/shoreline lakes/artem-sapegin-XGDBdSQ70O0-unsplash.jpg",
  "backgroundChange_hours": "0",
  "backgroundChange_minutes": "5",
  "changeBackgroundsAutomatically": "false",
  "menusFadingSpeed": "400",
  "menusUseRainbowPanels": "true",
  "lastUsed": "1683525231",
  "specificityName": "current page for user Guest at the client",
  "orientation": "landscape",
  "app": "/",
  "url": "/view/eyJcLyI6eyJwYWdlIjoiaW5kZXgifX0",
  "user": "Guest"
}
*/



/* v5.4 : nicer_app___data_themes document :
{
  "_id": "LunqcbUYkyatk1tNT1sM",
  "_rev": "1-6fc13132187c67a693233d8ce6bbc2f7",
  "theme": "default",
  "textBackgroundOpacity": 0.5,
  "themeSettings": {
    "Date Time" : {
        "HTML-SELECTOR:#siteDateTime": {
            "css" : {
                "border": "0px none rgb(255, 255, 255) ",
                "borderRadius": "10px 10px 10px 10px ",
                "boxShadow": "rgba(0, 0, 0, 0.55) 2px 2px 3px 3px",
                "color": "rgb(255, 255, 255)",
                "fontSize": "16px",
                "fontWeight": "700",
                "fontFamily": "Open Sans, sans-serif",
                "textShadow": "rgba(0, 0, 0, 0.5) 3px 3px 2px"
            }
        },
        "HTML-SELECTOR:#siteDateTime > .vdBackground": {
            "css" : {
                "opacity": "1",
                "background": "rgba(0, 0, 0, 0.35)",
                "borderRadius": "10px 10px 10px 10px "
            }
        },
        "HTML-SELECTOR:#siteDateTime td": []
      },
      "Content" : {
        "HTML-SELECTOR:#siteContent": {
            "css" : {
                "border": "0px none rgb(255, 255, 255) ",
                "borderRadius": "20px 20px 20px 20px ",
                "boxShadow": "rgba(0, 0, 0, 0.55) 2px 2px 3px 3px",
                "color": "rgb(255, 255, 255)",
                "fontSize": "15.593px",
                "fontWeight": "400",
                "fontFamily": "Open Sans, sans-serif",
                "textShadow": "rgba(0, 0, 0, 0.5) 3px 3px 2px"
            }
        },
        "HTML-SELECTOR:#siteContent > .vdBackground": {
            "css" : {
                "opacity": "1",
                "background": "rgba(0, 0, 0, 0.39)",
                "borderRadius": "20px 20px 20px 20px "
            }
        },
        "HTML-SELECTOR:#siteContent td": []
      },
      // ETC
  },
  "apps": [],
  "backgroundSearchKey": "landscape",
  "background": "/siteMedia/backgrounds/landscape/scenery/shoreline lakes/artem-sapegin-XGDBdSQ70O0-unsplash.jpg",
  "backgroundChange_hours": "0",
  "backgroundChange_minutes": "5",
  "changeBackgroundsAutomatically": "false",
  "menusFadingSpeed": "400",
  "menusUseRainbowPanels": "true",
  "lastUsed": "1683525231",
  "specificityName": "current page for user Guest at the client",
  "orientation": "landscape",
  "app": "/",
  "url": "/view/eyJcLyI6eyJwYWdlIjoiaW5kZXgifX0",
  "user": "Guest"
}
*/
?>
{
    "Theme Settings" : {
        "dialogs" : {
            "Date Time" : {
                "type" : "themeSettingsItem",
                "css" : {
                    "selector" : "#datetime",
                    "background" : "rgba(0,0,0,0.5)"
                }
            },
            "Icon Buttons" :  {
            }
        <?php echo jsonAppSettings()?>

    }
}
<?php
function jsonAppSettings() {
    global $naWebOS;
    $v = $naWebOS->view;
    foreach ($v as $viewFolder => $viewSettings) break; // 1 app at a time
    $fnAppSettings = $viewFolder.'/app.themeSettings.php';
    if (file_exists($fnAppSettings) && is_readable($fnAppSettings)) {
        return require_return ($fnAppSettings).',';
    };
    return '';
}
?>
