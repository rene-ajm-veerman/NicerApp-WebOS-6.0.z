<!DOCTYPE html>
<html lang="en" style="padding:0px;margin:0px;overflow:hidden;border:0px;">
<head>
    <title>SceneJS V3 Texture Layers</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <style>
        body {
            margin: 0;
            -moz-user-select: -moz-none;
            -khtml-user-select: none;
            -webkit-user-select: none;
        }
    </style>

<!--<script src="http://nicer.app/nicerapp/lib/scenejs-3.0/api/latest/scenejs.js"></script>-->
<script src="http://scenejs.org/api/latest/scenejs.js"></script>

<!--<script src="http://nicer.app/nicerapp/lib/scenejs-3.0/api/latest/extras/orbitControl.js"></script>-->
<script src="http://scenejs.org/api/latest/extras/orbitControl.js"></script>
</head>
<body style="padding:0px;margin:0px;border:0px;overflow:hidden;">

<script>

var scene = SceneJS.createScene({

    nodes:[
        // Custom lighting to simulate the Sun
        {
            type:"lights",
            lights:[
                {
                    mode:"dir",
                    color:{ r:1.0, g:1.0, b:1.0 },
                    diffuse:true,
                    specular:true,
                    dir:{ x:-0.5, y:-0.5, z:-0.75 }
                }
            ],

            nodes:[
                {
                    type:"rotate",
                    z:1,
                    angle:195,
                    nodes:[
                        {
                            type:"rotate",
                            y:1,
                            id:"earth-rotate",

                            nodes:[

                                // Layer 0: Earth's surface with color, specular
                                // and emissive maps

                                {
                                    type:"layer",
                                    priority:0,

                                    nodes:[
                                        {
                                            type:"scale",
                                            x:2,
                                            y:2,
                                            z:2,

                                            nodes:[
                                                {
                                                    type:"material",
                                                    emit:1,
                                                    color:{ r:1.0, g:1.0, b:1.0 },
                                                    specularColor:{ r:0.5, g:0.5, b:0.5 },
                                                    specular:5.0,
                                                    shine:70.0,

                                                    nodes:[
                                                        {
                                                            type:"texture",
                                                            layers:[

//                                                                                // Bump map
//                                                                                {
//                                                                                    src:"../../../textures/earthbump.jpg",
//                                                                                    applyTo:"normals"
//                                                                                },


                                                                {
                                                                    src:"http://nicer.app/nicerapp/lib/scenejs-3.0/examples/textures/earth.jpg",
                                                                    applyTo:"color"
                                                                },
                                                                {
                                                                    src:"http://nicer.app/nicerapp/lib/scenejs-3.0/examples/textures/earth-specular.jpg",
                                                                    applyTo:"specular"
                                                                } ,
                                                                {
                                                                    src:"http://nicer.app/nicerapp/lib/scenejs-3.0/examples/textures/earth-lights.gif",
                                                                    applyTo:"emit"
                                                                }
                                                            ],
                                                            nodes:[
                                                                {
                                                                    type:"geometry",
                                                                    source:{
                                                                        type:"sphere",
                                                                        latitudeBands:120,
                                                                        longitudeBands:120
                                                                    }
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                },

                                // Layer 1: Cloud layer with alpha map
                                {
                                    type:"layer",
                                    priority:1,

                                    nodes:[

                                        {
                                            type:"flags",


                                            flags:{
                                                transparent:true,
                                                specular:true
                                            },

                                            nodes:[
                                                {
                                                    type:"material",
                                                    emit:0.1,
                                                    alpha:0.7,
                                                    color:{ r:1, g:1, b:1 },
                                                    specularColor:{ r:1.0, g:1.0, b:1.0 },
                                                    specular:0.5,
                                                    shine:1.0,
                                                    nodes:[
                                                        {
                                                            type:"scale",
                                                            x:2.05,
                                                            y:2.05,
                                                            z:2.05,

                                                            nodes:[
                                                                {
                                                                    type:"texture",
                                                                    layers:[
                                                                        {
                                                                            src:"http://nicer.app/nicerapp/lib/scenejs-3.0/examples/textures/earthclouds.jpg",
                                                                            applyTo:"alpha",
                                                                            flipY:false
                                                                        }
                                                                    ],

                                                                    // Sphere with some material
                                                                    nodes:[
                                                                        {
                                                                            type:"node",
                                                                            z:1,
                                                                            angle:195,
                                                                            nodes:[
                                                                                {
                                                                                    type:"rotate",
                                                                                    y:1,
                                                                                    id:"clouds-rotate",
                                                                                    nodes:[

                                                                                        {
                                                                                            type:"geometry",
                                                                                            source:{
                                                                                                type:"sphere",
                                                                                                latitudeBands:120,
                                                                                                longitudeBands:120
                                                                                            }
                                                                                        }
                                                                                    ]

                                                                                }
                                                                            ]
                                                                        }
                                                                    ]
                                                                }
                                                            ]
                                                        }
                                                    ]
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                }
            ]
        },
        
            {
                type:"lookAt",
                eye:{ y:40 },
                nodes:[
                    {
                        type:"lights",
                        lights:[
                            {
                                mode:"ambient",
                                color:{ r:0.7, g:0.7, b:0.9 },
                                diffuse:true,
                                specular:false
                            },
                            {
                                mode:"dir",
                                color:{ r:1.0, g:1.0, b:0.2 },
                                diffuse:true,
                                specular:true,
                                dir:{ x:1.0, y:0.9, z:-0.7 },
                                space:"world"
                            }
                        ],

                        nodes:[
	                    //--------------------------------------------------------------------------------
	                    // Sky box
	                    //--------------------------------------------------------------------------------

	                    // Shader node, injects custom function into the vertex shader
	                    // to intercept the View matrix and remove translation terms from it.
	                    // This anchors the sky box, preventing it from translating
	                    // as we move the eye position.
	                    {
	                        type:"shader",
	                        shaders:[
	                            {
	                                stage:"vertex",
	                                code:[
	                                    "mat4 myViewMatrix(mat4 m) {",
	                                    "   m[3][0] =m[3][1] = m[3][2] = 0.0;",
	                                    "return m;",
	                                    "}"
	                                ],
	                                // Bind our injected functions to SceneJS hook points
	                                hooks:{
	                                    viewMatrix:"myViewMatrix"
	                                }
	                            }
	                        ],
	                        nodes:[

	                            // Disable lighting for the sky box
	                            {
	                                type:"flags",
	                                flags:{
	                                    specular:false,
	                                    diffuse:false,
	                                    ambient:false
	                                },
	                                nodes:[

	                                    {
	                                        type:"material",
	                                        color:{ r:0, g:0, b:0  },
	                                        emit:0.0,
	                                        nodes:[
	                                            // Clouds texture
	                                            {
	                                                type:"texture",
	                                                layers:[
	                                                    {
	                                                        src:"http://nicer.app/siteMedia/skyboxes/milkyway.jpg",
	                                                        blendMode:"add"
	                                                    }
	                                                ],
	                                                nodes:[
	                                                    {
	                                                        type:"scale",
	                                                        x:1000,
	                                                        y:1000,
	                                                        z:1000,
	                                                        nodes:[
	                                                            // Sky box geometry
	                                                            {
	                                                                type:"geometry",
	                                                                source:{
	                                                                    type:"skybox"
	                                                                }
	                                                            }
	                                                        ]
	                                                    }
	                                                ]
	                                            }
	                                        ]
	                                    }
	                                ]
	                            }
	                        ]
	                    }
	                ]
	            }
	        ]
	    }]	
});

var earthRotateNode = scene.getNode("earth-rotate");
var cloudsRotateNode = scene.getNode("clouds-rotate");

var earthRotate = 0;
var cloudsRotate = 0;

scene.on("tick",
        function () {
            earthRotateNode.setAngle(earthRotate);
            cloudsRotateNode.setAngle(cloudsRotate);

            earthRotate -= 0.02;
            cloudsRotate -= 0.01;
        });

new SceneJS.OrbitControls(scene, {
    yaw:30,
    pitch:-30,
    zoom:15
});

</script>
</body>
</html>
