if (!na || !na.ui || !na.ui.vividButton) debugger;
na.ui.vividButton.buttonTypes['btn_audioVideo_playPause'] = {
    buttonType : 'btn_audioVideo_playPause',
    startupState : 'paused',
    selectedState : 'playing',
    startupCircumstance : 'normal',    
    circumstances : {
        normal : {
            layers : {
                circleIcon_background : {
                    layerID : 'b.btnCode.circumstances.normal.layers.circleIcon_background',
                    animType : 'css : radial-gradient',
                    shape : 'circle',
                    position : 'center',
                    size : 'closest-side',
                    linearColorStops : [
                        { color : 'rgba(0,255,0,0)', length : [0, 0] },
                        { color : 'rgba(0,0,0,0.7)', length : [1, 100] }
                    ]
                }
            }
        },
        hover : {
            layers : {
                circleIcon_background : {
                    layerID : 'b.btnCode.circumstances.hover.layers.circleIcon_background',
                    animType : 'css : radial-gradient',
                    shape : 'circle',
                    position : 'center',
                    size : 'closest-side',
                    linearColorStops : [
                        { color : 'rgba(0,255,0,0.3)', length : [0, 92] },
                        { color : 'rgba(0,255,0,0.7)', length : [100, 100] }
                    ]
                }
            }
        }
    },
    layers : {
        circleIcon_svg : {
            layerID : 'b.btnCode.layers.circleIcon_svg',
            animType : 'svg',
            //src : '/NicerAppWebOS/logic.userInterface/naVividButton-4.0.0/btn.audioVideo.playPause.svg',
            src : '/NicerAppWebOS/logic.userInterface/vividUI-5.0.0/vividButton-4.1.0/btn_audioVideo_playPause.svg',
            startupCode : function () {
                //$('#from_pause_to_play')[0].beginElement();
                //$('#line2').addClass('atPlay');
            },
            onclick : function () {
                if (na.ui.vb.globals.debug) debugger;
                if ($('#btnPlayPause').is('.selected')) {
                    if (!$('#btnPlayPause').is('.paused')) {
                        if (!$('#line2').is('.atPlay')) {
                            $('#from_pause_to_play')[0].beginElement(); 
                            $('#line2').addClass('atPlay');
                        }
                    }
                } else {
                    /*
                    if (
                        $('#btnPlayPause').is('.paused')
                    ) {
                        if (!$('#line2').is('.atPlay')) {
                            $('#from_play_to_pause')[0].beginElement(); 
                            $('#line2').addClass('atPlay');
                        }
                    }*/
                }
            },
            onmouseover : function () {
                if (na.ui.vb.globals.debug) debugger;
                if ( 
                    $('#btnPlayPause').is('.selected')
                    || $('#btnPlayPause').is('.paused') 
                ) {
                    if ($('#line2').is('.atPlay')) {
                        $('#from_play_to_pause')[0].beginElement();
                        $('#line2').removeClass('atPlay');
                    } else {
                        $('#from_pause_to_play')[0].beginElement();
                        $('#line2').addClass('atPlay');
                    }
                        
                } 
            },
            onmouseout : function () {
                if (na.ui.vb.globals.debug) debugger;
                if ( 
                    $('#btnPlayPause').is('.selected')
                ) {
                    if (!$('#line2').is('.atPlay')) {
                        $('#from_pause_to_play')[0].beginElement();
                        $('#line2').addClass('atPlay');
                    }
                } else {
                    if (
                        !$('#btnPlayPause').is('.recentlyClicked')
                        && !$('#line2').is('.atPlay')
                    ) {
                        $('#from_pause_to_play')[0].beginElement();
                        $('#line2').addClass('atPlay');
                    }
                    if (
                        !$('#btnPlayPause').is('.recentlyClicked')
                        && $('#btnPlayPause').is('.paused')
                        //&& $('#line2').is('.atPlay')
                    ) {
                        $('#from_play_to_pause')[0].beginElement();
                        $('#line2').removeClass('atPlay');
                    }
                }
            }
        }
    },
    states : {
        playing : {
            circumstances : {
                normal : {
                    layers : {
                        circleIcon_background : {
                            layerID : 'b.btnCode.circumstances.normal.layers.circleIcon_background', 
                            animTo : {
                                hover : {
                                    animDuration : 500,
                                    animInterval : 10,
                                    steps : [ ]
                                }
                            }
                        },
                        circleIcon_svg : {
                            layerID : 'b.btnCode.layers.circleIcon_svg',
                            animTo : {
                                hover : {
                                    animDuration : 500
                                }
                            }
                        }
                    }
                },
                hover : {
                    layers : {
                        circleIcon_background : {
                            layerID : 'b.btnCode.circumstances.hover.layers.circleIcon_background',
                            animTo : {
                                normal : {
                                    animDuration : 500,
                                    animInterval : 10,
                                    steps : [ ]
                                }
                            }                            
                        },
                        circleIcon_svg : {
                            layerID : 'b.btnCode.layers.circleIcon_svg',
                            animTo : {
                                normal : {
                                    animDuration : 500
                                }
                            }
                        }
                    }
                }
            }
        },
        paused : {
            circumstances : {
                normal : {
                    layers : {
                        circleIcon_background : {
                            layerID : 'b.btnCode.circumstances.normal.layers.circleIcon_background', 
                            animTo : {
                                hover : {
                                    animDuration : 500,
                                    animInterval : 10,
                                    steps : [ ]
                                }
                            }
                        },
                        circleIcon_svg : {
                            layerID : 'b.btnCode.layers.circleIcon_svg',
                            animTo : {
                                hover : {
                                    animDuration : 500
                                }
                            }
                        }
                    }
                },
                hover : {
                    layers : {
                        circleIcon_background : {
                            layerID : 'b.btnCode.circumstances.hover.layers.circleIcon_background',
                            animTo : {
                                normal : {
                                    animDuration : 500,
                                    animInterval : 10,
                                    steps : [ ]
                                }
                            }                            
                        },
                        circleIcon_svg : {
                            layerID : 'b.btnCode.layers.circleIcon_svg',
                            animTo : {
                                normal : {
                                    animDuration : 500
                                }
                            }
                        }
                    }
                }
            }
        }
    }
};

