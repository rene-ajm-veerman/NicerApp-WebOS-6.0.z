import * as THREE from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/3rd-party/3D/libs/three.js/build/three.module.js';
import { vividUserInterface_3D_component } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/3D/_component.js';
import { vividUserInterface_3D_button } from '/NicerAppWebOS/ajax_getModule.php?f=/NicerAppWebOS/logic.vividUserInterface/v6.y.z/3D/button.js'

export class vividUserInterface_3D_button_startMenu_planet extends vividUserInterface_3D_button {
  constructor (settings) {
    super(settings);
    var t = this;

    /*
    t.lights = [];
    t.scene = new THREE.Scene();
    t.camera = new THREE.PerspectiveCamera(
      50, $(settings.el).width() / $(settings.el).height(), 0.01, 10 * 1000
    );

    t.renderer = new THREE.WebGLRenderer({alpha:true, antialias : true});
    t.renderer.physicallyCorrectLights = true;
    t.renderer.outputEncoding = THREE.sRGBEncoding;
    t.renderer.setPixelRatio (window.devicePixelRatio);
    t.renderer.setSize( $(settings.el).width()-20, $(settings.el).height()-20 );
    t.renderer.toneMappingExposure = 1.0;

    $(settings.el).append( t.renderer.domElement );

    const light1  = new THREE.AmbientLight(0xFFFFFF, 0.3);
    light1.name = 'ambient_light';
    light1.intensity = 0.3;
    light1.color = 0xFFFFFF;
    t.camera.add( light1 );

    const light2  = new THREE.DirectionalLight(0xFFFFFF, 0.8 * Math.PI);
    light2.position.set(0.5, 0, 0.866); // ~60º
    light2.name = 'main_light';
    light2.intensity = 0.8 * Math.PI;
    light2.color = 0xFFFFFF;
    t.camera.add( light2 );

    t.lights.push(light1, light2);

    t.pmremGenerator = new THREE.PMREMGenerator( t.renderer );
    t.pmremGenerator.compileEquirectangularShader();

    t.globe = new THREE.SphereGeometry (.8, 64, 32);
    t.textureURL = '/siteMedia/textures';
    t.group = new THREE.Group();
    t.scene.add( t.group );

    new HDRCubeTextureLoader()
      .setPath( t.textureURL+'/cube/pisaHDR/' )
      .load( [ 'px.hdr', 'nx.hdr', 'py.hdr', 'ny.hdr', 'pz.hdr', 'nz.hdr' ],
          function ( hdrTexture ) {

          const geometry = new THREE.SphereGeometry( .8, 64, 32 );

          const textureLoader = new THREE.TextureLoader();

          const diffuse = textureLoader.load( t.textureURL+'/carbon/Carbon.png' );
          diffuse.colorSpace = THREE.SRGBColorSpace;
          diffuse.wrapS = THREE.RepeatWrapping;
          diffuse.wrapT = THREE.RepeatWrapping;

          const normalMap1 = textureLoader.load( t.textureURL+'/carbon/Carbon_Normal.png' );
          normalMap1.wrapS = THREE.RepeatWrapping;
          normalMap1.wrapT = THREE.RepeatWrapping;

          const normalMap2 = textureLoader.load( t.textureURL+'/water/Water_1_M_Normal.jpg' );

          const normalMap3 = new THREE.CanvasTexture( new FlakesTexture() );
          normalMap3.wrapS = THREE.RepeatWrapping;
          normalMap3.wrapT = THREE.RepeatWrapping;
          normalMap3.anisotropy = 16;

          const normalMap4 = textureLoader.load( t.textureURL+'/golfball.jpg' );

          const clearcoatNormalMap = textureLoader.load( t.textureURL+'/pbr/Scratched_gold/Scratched_gold_01_1K_Normal.png' );

          // car paint

          const carPaintUV = uv().mul( vec2( 10, 6 ) );
          const carPaintNormalScale = vec2( 0.15 );

          let material = new MeshPhysicalNodeMaterial();
          material.clearcoatNode = float( 1 );
          material.clearcoatRoughnessNode = float( 0.1 );
          material.metalnessNode = float( 0.9 );
          material.roughnessNode = float( 0.5 );
          material.colorNode = color( 0x0000ff );
          material.normalNode = normalMap( texture( normalMap3, carPaintUV ), carPaintNormalScale );

          let mesh = new THREE.Mesh( geometry, material );
          mesh.position.x = - 1;
          mesh.position.y = 1;
          t.group.add( mesh );

          // fibers

          const fibersUV = uv().mul( 10 );

          material = new THREE.MeshPhysicalNodeMaterial();
          material.roughnessNode = float( 0.5 );
          material.clearcoatNode = float( 1 );
          material.clearcoatRoughnessNode = float( 0.1 );
          material.colorNode = texture( diffuse, fibersUV );
          material.normalNode = normalMap( texture( normalMap1, fibersUV ) );

          mesh = new THREE.Mesh( geometry, material );
          mesh.position.x = 1;
          mesh.position.y = 1;
          t.group.add( mesh );

          // golf

          material = new THREE.MeshPhysicalNodeMaterial();
          material.clearcoatNode = float( 1 );
          material.roughnessNode = float( 0.1 );
          material.metalnessNode = float( 0 );
          material.colorNode = color( 0xffffff );
          material.normalNode = normalMap( texture( normalMap4 ) );
          // y scale is negated to compensate for normal map handedness.
          material.clearcoatNormalNode = normalMap( texture( clearcoatNormalMap ), vec2( 2.0, - 2.0 ) );

          mesh = new THREE.Mesh( geometry, material );
          mesh.position.x = - 1;
          mesh.position.y = - 1;
          t.group.add( mesh );

          // clearcoat + normalmap

          material = new THREE.MeshPhysicalNodeMaterial();
          material.clearcoatNode = float( 1 );
          material.roughnessNode = float( 1 );
          material.metalnessNode = float( 1 );
          material.colorNode = color( 0xff0000 );
          material.normalNode = normalMap( texture( normalMap2 ), vec2( 0.15, 0.15 ) );
          // y scale is negated to compensate for normal map handedness.
          material.clearcoatNormalNode = normalMap( texture( clearcoatNormalMap ), vec2( 2.0, - 2.0 ) );

          mesh = new THREE.Mesh( geometry, material );
          mesh.position.x = 1;
          mesh.position.y = - 1;
          t.group.add( mesh );

          //

          t.scene.background = hdrTexture;
          t.scene.environment = hdrTexture;

        }

          );


    //t.updateEnvironment(this);

    t.raycaster = new THREE.Raycaster();
    t.mouse = new THREE.Vector2();
    t.mouse.x = 0;
    t.mouse.y = 0;
    t.mouse.z = 0;

    t.camera.position.z = 1500;
    t.camera.position.y = 200;

    /*
    // Setup labels
    t.labelRenderer = new CSS2DRenderer();
    t.labelRenderer.setSize(innerWidth, innerHeight);
    t.labelRenderer.domElement.style.position = 'absolute';
    t.labelRenderer.domElement.style.top = '0px';
    t.labelRenderer.domElement.style.backgroundColor = 'rgba(0,0,50,0.5)';
    t.labelRenderer.domElement.style.boxShadow = 'inset 3px 3px 2px 2px rgba(255,255,255,0.55), 4px 4px 3px 2px rgba(0,0,0,0.7)';
    t.labelRenderer.domElement.style.pointerEvents = 'none';
    document.body.appendChild(t.labelRenderer.domElement);

    t.labelDiv = document.createElement('div');
    t.labelDiv.className = 'label';
    t.labelDiv.style.backgroundColor = 'rgba(0,0,50,0.5)';
    t.labelDiv.style.boxShadow = 'inset 3px 3px 2px 2px rgba(255,255,255,0.55), 4px 4px 3px 2px rgba(0,0,0,0.7)';
    t.labelDiv.style.marginTop = '-1em';

    t.label = new CSS2DObject(t.labelDiv);
    t.label.visible = false;
    t.scene.add(t.label);
    * /

    // Track mouse movement to pick objects
    //t.raycaster = new Raycaster();
    //t.mouse = new Vector2();

    window.addEventListener('mousemove', ({ clientX, clientY }) => {
        //const { innerWidth, innerHeight } = window;
        var innerWidth = $('#siteContent .vividDialogContent').width();
        var innerHeight = $('#siteContent .vividDialogContent').height();

        //t.mouse.x = ((clientX-$('#siteContent .vividDialogContent').offset().left) / innerWidth) * 2 - 1;
        //t.mouse.y = (-1 * ((clientY-$('#siteContent .vividDialogContent').offset().top) / innerHeight) * 2) + 1;
        //t.animate(t);
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        //const { innerWidth, innerHeight } = window;
        var innerWidth = $('#siteContent .vividDialogContent').width();
        var innerHeight = $('#siteContent .vividDialogContent').height();

        t.renderer.setSize(innerWidth, innerHeight);
        t.camera.aspect = innerWidth / innerHeight;
        t.camera.updateProjectionMatrix();
    });

    t.renderer.setAnimationLoop(() => {
        //controls.update();

        // Pick objects from view using normalized mouse coordinates
        t.raycaster.setFromCamera(t.mouse, t.camera);

    });

    t.animate(t);
        */

  }

  animate (t,evt) {
    requestAnimationFrame( function(evt) { t.animate (t,evt) });
    t.renderer.render( t.scene, t.camera );
  }
}
