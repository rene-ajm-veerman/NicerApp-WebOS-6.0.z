if (!na || !na.ui || !na.ui.vividButton) debugger;
na.ui.vividButton.buttonTypes['btn_lock'] = {
    buttonType : 'btn_lock',
    startupState : 'open',
    selectedState : 'closed',
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
            src : '/NicerAppWebOS/logic.userInterface/vividButton-4.1.0/btn_lock.svg',
            onclick : function (evt) {
                    $('.lock-top, .lock-body, .lock-spinner').toggleClass('loading');
                    // make your ajax call here instead of the timeout
                    setTimeout(function(){
                        $('.lock-top, .lock-body, .lock-spinner').toggleClass('loading');
                        $('.lock-top').toggleClass('loaded');
                    }, 750)
            }
        }
    },
    states : {
        open : {
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
        closed : {
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

