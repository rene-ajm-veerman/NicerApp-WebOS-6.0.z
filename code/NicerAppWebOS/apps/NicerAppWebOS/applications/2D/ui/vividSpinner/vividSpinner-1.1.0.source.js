/*
 *		vividSpinner-1.1.0 
 * 	(c) (r) [the owner of seductiveapps.com].
 * 	uses a slightly modified google's excanvas for IE support, copyright Google 2006.
 * 
 *  author			: <seductiveapps@gmail.com>
 *  version			: 1.1.0
 *	license			: http://seductiveapps.com/LICENSE.txt

	copyright : '(c) (r) 2008 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
	license : 'http://seductiveapps.com/LICENSE.txt',
	noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',

 *  created			: 23 oct 2008
 *  last modified 	: 29 oct 2008
 */
if (!sa) var sa = seductiveapps = {};
seductiveapps.acs = seductiveapps.vividSpinner = {
	options : {
		timePerFrame : 400,
		numStripes : 12	
	},
	
	initialize : function () {
		this.browserInfo = this.getBrowserInfo();
		this.openIcons = [];
		this.runningAnyAnims = false;
	},
	
	
	drawFrame : function (icon) {
		this.drawFrameLoaderIcon(icon);		
	},
	

	drawFrameLoaderIcon : function (icon) {
		ctx = this.getDrawingInterface (icon.canvas);
		if (!ctx) return false;
				
		ctx.save();
		ctx.clearRect (0,0,icon.width,icon.height);
		
		var ns = this.options.numStripes;
		
		var frameCount = icon.theme.stripes.length;
		var mx = Math.round (icon.width / 2);
		var my = Math.round (icon.height / 2);
		
		for (var i = 0; i < ns; i++) {
			var angle = ((Math.PI * 2) / ns) * i;

			var cos = Math.cos(angle);
			var sin = Math.sin(angle);

			var px = mx - (Math.cos(angle));
			var py = my - (Math.sin(angle));

			ctx.save();
			ctx.translate(px, py);
			
			ctx.rotate (angle);
			var aw = icon.images[0];
			
			for (var j = frameCount; j > 0; j--) {
				if (i == icon.frame - j) {
					aw = icon.images[frameCount - j];
				}
			}
			
			if (!aw || !aw.width) {
				ctx.restore();
				ctx.restore();
				return false; //for if the image hasn't loaded yet. we'll pass this point again in about 150 milliseconds though
			} else if (aw==icon.images[frameCount-1]) {
				icon.newlyCreated = false; //stop animating if user requested that
			}
			
			magicOffset =  /* determines in detail where the stripes end up on the circle.. */
				-1 * (aw.width/2.727272727272727);
				
			ctx.drawImage (aw, magicOffset, icon.theme.centerGapRadius);
			ctx.restore ();
			
		}
		ctx.restore();
		icon.frame++;
		if (icon.frame>this.options.numStripes) icon.frame = 1;

	},


// HTML and animation code

	addIcon : function (
						absolutePositioned /* absolute if TRUE, relative if FALSE */ ,
						pel, //parent html element 
						width, // in pixels
						height, 
						theme,
						startImmediately
	) {
		this.browserSize = this.getBrowserSize();
		var id = this.getNewID();
		
		var nel /*new html element*/ = document.createElement ('DIV');
		nel.style.display = 'none';
		nel.id = id;
		nel.className = 'sa.acs';

		if (absolutePositioned) {
			nel.style.position = 'absolute';
			document.body.appendChild(nel); //do this here already, else memleaks on IE
		}
		else {
			nel.style.position = 'relative';
			pel.appendChild(nel);
		};

		nel.style.width = width+'px';
		nel.style.height = height+'px';

		var canvas = document.createElement ('CANVAS');
		canvas.id = id;
		canvas.width = width;
		canvas.height = height;
        canvas.style.zIndex = 1000 * 1000;
		
		nel.appendChild (canvas);

		if ((pel === document.body)) {
			var px = this.browserSize.width;
			var py = this.browserSize.height; 
		} else {
			var px = (pel.offsetWidth>0) ? pel.offsetWidth : parseInt(pel.style.width);
			var py = (pel.offsetHeight>0) ? pel.offsetHeight : parseInt(pel.style.height);
		};
		var cx = (nel.offsetWidth>0) ? nel.offsetWidth : parseInt(nel.style.width);
		var cy = (nel.offsetHeight>0) ? nel.offsetHeight : parseInt(nel.style.height);

		var rx = 0;
		var ry = 0;

		if (absolutePositioned) {
			var e = pel;
			while (e && !(e === document.body)) {
				rx += e.offsetLeft;
				ry += e.offsetTop;
				e = e.offsetParent;
			};
		};
		nel.style.left = (rx + Math.round((px - cx) / 2)) + 'px';
		nel.style.top = (ry + (Math.round((py - cy) / 2))) + 'px';
		
		nel.style.zIndex = 1000 * 1000;
		nel.style.display = 'block';

		if (this.browserInfo.ie) {
			googleExCanvas();
		};
		

		var images = {};
		for (var i = 0; i < theme.stripes.length; i++) {
			images[i] = new Image();
			images[i].src = theme.stripes[i];
		}

		this.openIcons = this.openIcons.concat ({
			id : id,
			frame : 7, //start at 1 o'clock
			running : startImmediately,
			newlyCreated : true, //dirty hack to paint the first picture even when it aint supposed to animate
			theme : theme,
			images : images,
			container : nel,
			canvas : nel.childNodes[0],
			width : width,
			height : height
		});
		
		if (!sa.acs.runningAnyAnims) sa.acs.startAnimations();
		
		return nel;
	},
	
	findIconByContainer : function (containerElement) {
		var found = false;
		for (var i=0; i<this.openIcons.length; i++) {
			if (this.openIcons[i].container == containerElement) found = this.openIcons[i];
		};
		return found;
	},
	
	start : function (elOrParent) {
		var icon = this.findIconByContainer(jQuery('div.sa.acs', elOrParent)[0]);
		if (icon) {
			icon.running = true;
		};
	},
	
	stop : function (elOrParent) {
		var icon = this.findIconByContainer(jQuery('div.sa.acs', elOrParent)[0]);
		if (icon) {
			icon.running = false;
		};
	},
	
	startAnimations : function () {
		for (var i = 0; i < this.openIcons.length; i++) {
			var icon = this.openIcons[i];
		}
		this.runAnimations(); //first frame in all open icons
		this.runInterval = setInterval (function() {sa.acs.runAnimations()}, this.options.timePerFrame); // subsequent animations
		
		this.runningAnyAnims = true;
	},
	
	stopAnimations : function () {
		clearInterval (this.runInterval);
		this.runInterval = null;
		this.runningAnyAnims = false;
	},
	
	runAnimations : function () {
		for (var i=0; i < this.openIcons.length; i++) {
			var icon = this.openIcons[i];
			
			if (!icon.canvas || !icon.canvas.id) { //has it been deleted from outside the scope of this component?
				this.removeLoadingIcon(i);
			} else {
				if (icon.running || icon.newlyCreated) {
					this.drawFrame(this.openIcons[i]);
				}
			}
		}
	},
	
	removeLoadingIcon : function (idx) {
		if (this.openIcons[i]) {
			var icon = this.openIcons[i];
			
			if (icon.container && icon.container.id) icon.container.parentNode.removeChild (icon.container);
			
			this.openIcons.splice (idx, 1);
		}
		if (this.openIcons.length == 0) this.stopAnimations();
	},


//js helpers
	failsMiserably : function (msg, sourceLocation) {
		if (typeof console!=='undefined') {
			console.log(msg);
			
		};
		this.stopAnimations();
		alert (msg);
	},

	getBrowserInfo : function () {
		var agent = navigator.userAgent.toLowerCase();
		var r = {};
		r.major = parseInt(navigator.appVersion);
		r.minor = parseFloat(navigator.appVersion);
		r.windows = ((agent.indexOf('windows') != -1));
		r.ns = ((agent.indexOf('mozilla')   != -1) &&
			(agent.indexOf('spoofer')     == -1) &&
			(agent.indexOf('compatible')  == -1) &&
			(agent.indexOf('opera')       == -1) &&
			(agent.indexOf('webtv')       == -1));
		r.ie = (agent.indexOf("msie")       != -1);
		return r;
	},
	
	getBrowserSize : function () {
		return {
			width: sa.m.browserWidth(),
			height: sa.m.browserHeight()
		};
	},

	getDrawingInterface : function (canvas) {
		var fncn = 'sa.acs.getDrawingInterface(): ';

		if (typeof canvas !== 'object') {
			this.failsMiserably ('Invalid canvas : '+typeof canvas, fncn);
			return false;
		};
		
		if ( !(canvas.tagName.toLowerCase()=='canvas') || !canvas.id) {
			this.failsMiserably ('Canvas does not appear to be a valid HTML DOM element node. typeof='+typeof canvas, fncn);
			return false;
		}

		if (typeof canvas.getContext !== 'function') {
			this.failsMiserably ('Canvas with .id="'+canvas.id+'" hasnt got a .getContext() method.\nIf you\'re running IE, this means google\'s excanvas js-lib wasn\'t properly initialized.', fncn);
			return false;
		}
		
		var r = canvas.getContext('2d');
		return r;
	},
	
	getNewID : function () {
		var now = new Date();
		return now.getTime();
	}
};
sa.vividSpinner.initialize();
