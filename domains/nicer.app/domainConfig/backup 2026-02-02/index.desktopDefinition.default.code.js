if (!na) var na = nicerapp = NicerApp_WebOS = {};
na.defaultDesktopDefinitionCode = na.d3c = {
    globals : {

    },
    settings : {
        conditionsCheck_queryStrings : {}
    },

    conditionsCheck : function (queryString) {
        let
        d3s = na.d3c.settings;

        // queryStrings can lead to complicated regular expressions,
        // which are CPU intensive to compile for specific strings.
        // so we cache *everything*, since this function is going to
        // get called many times during a manual resizing of the
        // browser window.
        if (!d3s.conditionsCheck_queryStrings[queryString]) {
            d3s.conditionsCheck_queryStrings[queryString] = {
                qs : queryString.split(':::')
            };

            let qs = d3s.conditionsCheck_queryStrings[queryString];

            qs.type = qs.qs[0];
            qs.fnc = qs.qs[1].match(/^(.*?)\(\'(.*?)\'\).*?$/);
            debugger;
        };
    },

    areDivsVisible : function (queryString) {
        let q = queryString;
    },

    dv_c : function () {
        var
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tc_2);
        else
            r = $.extend(r, r_tc_1);

        return r;
    },

    dv_tl_tte_tr : function () {
        var
        r_tl_1 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" }
                ]
            }
        },
        r_tl_2 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" }
                ]
            }
        },
        r_tte_1 = {
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarLeft","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" }
                ]
            }
        },
        r_tte_2 = {
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "#siteToolbarLeft","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" }
                ]
            }
        },
        r_tr_1 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" }
                ]
            }
        },
        r_tr_2 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" }
                ]
            }
        },
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" },
                    {"element" : "#siteToolbarRight","edge" : "left" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" },
                    {"element" : "#siteToolbarRight","edge" : "left" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tl_2, r_tte_2, r_tr_2, r_tc_2);
        else
            r = $.extend(r, r_tl_1, r_tte_1, r_tr_1, r_tc_1);

        return r;
    },

    dv_tl_tr : function () {
        var
        r_tl_1 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tl_2 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tr_1 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tr_2 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tl_2, r_tr_2, r_tc_2);
        else
            r = $.extend(r, r_tl_1, r_tr_1, r_tc_1);

        return r;
    },

    dv_tl : function () {
        var
        r_tl_1 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tl_2 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarLeft","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "#siteToolbarLeft","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tl_2, r_tc_2);
        else
            r = $.extend(r, r_tl_1, r_tc_1);

        return r;
    },

    dv_tr : function () {
        var
        r_tr_1 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tr_2 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" },
                    { "element" : "#siteToolbarRight", "edge" : "left" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" },
                    { "element" : "#siteToolbarRight", "edge" : "left" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tr_2, r_tc_2);
        else
            r = $.extend(r, r_tr_1, r_tc_1);

        return r;
    },

    dv_tte : function () {
        var
        r_tte_1 = {
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tte_2 = {
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tte_2, r_tc_2);
        else
            r = $.extend(r, r_tte_1, r_tc_1);

        return r;
    },

    dv_tte_tr : function () {
        var
        r_tl_1 = {
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tl_2 = {
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tr_1 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tr_2 = {
            "#siteToolbarRight" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteToolbarRight", "edge" : "left" },
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteToolbarRight", "edge" : "left" },
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tl_2, r_tr_2, r_tc_2);
        else
            r = $.extend(r, r_tl_1, r_tr_1, r_tc_1);

        return r;
    },

    dv_tl_tte : function () {
        var
        r_tte_1 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            },
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarLeft","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tte_2 = {
            "#siteToolbarLeft" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "body","edge" : "left" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            },
            "#siteToolbarThemeEditor" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "body","edge" : "right" }
                ],
                "growTo" : "maxY",
                "growToLimits" : [
                        { "element" : "#siteTaskbar", "edge" : "top" },
                ]
            }
        },
        r_tc_1 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "body","edge" : "top" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        },
        r_tc_2 = {
            "#siteContent" : {
                "mode" : 1,
                "snapTo" : [
                    {"element" : "#siteToolbarTop","edge" : "bottom" },
                    {"element" : "#siteToolbarThemeEditor","edge" : "right" }
                ],
                "growTo" : "max",
                "growToLimits" : [
                    { "element" : "#siteTaskbar", "edge" : "top" }
                ]

            }
        };
        var r = {};

        if (na.d.s.visibleDivs.includes('#siteToolbarTop'))
            r = $.extend(r, r_tte_2, r_tc_2);
        else
            r = $.extend(r, r_tte_1, r_tc_1);

        return r;
    }

};
