sa.canvasLogo = sa.logo = {
	about : {
		whatsThis : 'sa.canvasLogo === sa.logo; drawing of the logo and any interactivity/animations specific for the logo',
		copyright : '(c) (r) 2017 by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		license : 'May only be used by [the owner of seductiveapps.com] <seductiveapps@gmail.com>',
		noWarranty : 'NO WARRANTY EXPRESSED OR IMPLIED. USE ONLY AT YOUR OWN RISK.',
		version : 'x.y.z',
		dateOfBirth : '2017-07',
		lastUpdated : '2017-07-30 (Sunday) 00:00CEST'
	},
	globals : {
		
	},
	settings : {
        margin : 0, //pixels
		shadowX : 1,
		shadowY : 1,
		shadowDepth : 1,
		shadowDeeper : true,
        logoHTMLids : {}
	},
	
	init_do_createLogo : function (logoHTMLid) {
		var eventData = {
			eventType : 'resize',
			element : logoHTMLid
		};
		jQuery(window).resize (eventData, sa.logo.resize);
        
        sa.logo.settings.logoHTMLids[logoHTMLid] = {};
		
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid);
		
		var context = sa.logo.settings.context = jQuery('#'+logoHTMLid)[0].getContext('2d');
		
		var stage = sa.logo.settings.stage = new createjs.Stage(logoHTMLid);
		stage.shadow = new createjs.Shadow("#000000", -5, 0, 20);
		//setInterval (sa.logo.shadowLoop, 50);
		//stage.setBounds (0,0,w,h);
		
		var circle_html = new createjs.Shape();
        circle_html.graphics.beginStroke('navy').beginFill('white').drawCircle(0, 0, 40);
		sa.logo.settings.circle_html = circle_html;
        stage.addChild(circle_html);
		
		
		var circle_css = new createjs.Shape();
        circle_css.graphics.beginStroke('navy').beginFill('white').drawCircle(0, 0, 30);
		sa.logo.settings.circle_css = circle_css;
        stage.addChild(circle_css);
		
		var circle_javascript = new createjs.Shape();
        circle_javascript.graphics.beginStroke('navy').beginFill('white').drawCircle(0, 0, 20);
		sa.logo.settings.circle_javascript = circle_javascript;
        stage.addChild(circle_javascript);
		
		sa.logo.resize (eventData, true);		
	},
	
	shadowLoop : function (logoHTMLid) {
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid),
		stage = sa.logo.settings.stage,
		shadow = stage.shadow;
		
		// todo : perhaps animate shadow by creating shadow that moves around the logo.. tomorrow maybe.
		
	},

	resize : function (resizeEventData, actuallyDo) {
		
		if (actuallyDo) {
			
			if (resizeEventData.data) resizeEventData = resizeEventData.data;
			var 
			logoHTMLid = resizeEventData.element,
			w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
			h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid),
			stage = sa.logo.settings.stage,
			circle_html = sa.logo.settings.circle_html,
			circle_css = sa.logo.settings.circle_css,
			circle_javascript = sa.logo.settings.circle_javascript;

			sa.logo.settings.context.clearRect(0, 0, sa.logo.settings.context.width, sa.logo.settings.context.height);		
			// overrides
			//w = 200;
			//h = 200;

			//jQuery('#'+logoHTMLid)[0].top = 5;
			//jQuery('#'+logoHTMLid)[0].left = 5;
			/*
            jQuery('#'+logoHTMLid)[0].parentNode.style.width = w-10 + 'px';
			jQuery('#'+logoHTMLid)[0].parentNode.style.height = h-10 + 'px';
            */
			jQuery('#'+logoHTMLid)[0].width = w-10;
			jQuery('#'+logoHTMLid)[0].height = h-10;
			
			var x = parseInt((w / 2) - 10);
			var y = parseInt((h / 2) - 10);
			circle_html.x = x;
			circle_html.y = y;

			circle_css.x = x;
			circle_css.y = y;

			circle_javascript.x = x;
			circle_javascript.y = y;

			
			
			sa.logo.drawEggs (logoHTMLid);
			sa.logo.drawNESW_squares (logoHTMLid);
			//sa.logo.draw_NorthEast_SouthEast_SouthWest_NorthWest_squares (logoHTMLid);
			sa.logo.drawTriangles (logoHTMLid);
			
			stage.update();
		}
	},
	
	draw_NorthEast_SouthEast_SouthWest_NorthWest_squares : function (logoHTMLid) {
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid),
		stage = sa.logo.settings.stage;
		
		var square_nw = new createjs.Shape();
		square_nw.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) - 40 - 10,
		y1 = (h/2) - 30,
		x2 = (w/2) - 30,
		y2 = (h/2) - 50,
		x3 = Math.round( (w/2) - (x2 - x1) - 40 ),
		y3 = Math.round( (h/2) - (y1-y2)- 40 );
		square_nw.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (square_nw);
		
		var square_se = new createjs.Shape();
		square_se.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) + 20,
		y1 = (h/2) + 40,
		x2 = (w/2) + 40,
		y2 = (h/2) + 20,
		x3 = Math.round( (w/2) + (x2 - x1) + 30 ),
		y3 = Math.round( (h/2) + (y1-y2) + 30 );
		square_se.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (square_se);
		
		var square_ne = new createjs.Shape();
		square_ne.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) + 20,
		y1 = (h/2) - 50,
		x2 = (w/2) + 40,
		y2 = (h/2) -  30,
		x3 = Math.round( (w/2) + (x2 - x1) + 30 ),
		y3 = Math.round( (h/2) - (y2-y1) - 40 );
		square_ne.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (square_ne);

		/* smaller, nicer, square, compared to the more pointy / aggressive arrows in the rest of this code 
		var square_sw = new createjs.Shape();
		square_sw.graphics.beginStroke("black");
		var 
		x1 = (w/2) - 40 - 10,
		y1 = (h/2) + 20,
		x2 = (w/2) - 30,
		y2 = (h/2) + 40,
		x3 = Math.round( (w/2) - (x2 - x1) - 30 ),
		y3 = Math.round( (h/2) + (y2-y1) + 20 );
		*/
		var square_sw = new createjs.Shape();
		square_sw.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) - 40 - 10,
		y1 = (h/2) + 20,
		x2 = (w/2) - 30,
		y2 = (h/2) + 40,
		x3 = Math.round( (w/2) - (x2 - x1) - 40 ),
		y3 = Math.round( (h/2) + (y2-y1) + 30 );
		square_sw.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (square_sw);

		
		stage.update();
		
	},
	
	drawTriangles : function (logoHTMLid) {
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid),
		stage = sa.logo.settings.stage;
		
		var triangle_nw = new createjs.Shape();
		triangle_nw.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) - 40 - 10,
		y1 = (h/2) - 30,
		x2 = (w/2) - 30,
		y2 = (h/2) - 50,
		x3 = Math.round( (w/2) - (x2 - x1) - 40 ),
		y3 = Math.round( (h/2) - (y1-y2)- 40 );
		triangle_nw.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (triangle_nw);
		
		var triangle_se = new createjs.Shape();
		triangle_se.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) + 20,
		y1 = (h/2) + 40,
		x2 = (w/2) + 40,
		y2 = (h/2) + 20,
		x3 = Math.round( (w/2) + (x2 - x1) + 30 ),
		y3 = Math.round( (h/2) + (y1-y2) + 30 );
		triangle_se.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (triangle_se);
		
		var triangle_ne = new createjs.Shape();
		triangle_ne.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) + 20,
		y1 = (h/2) - 50,
		x2 = (w/2) + 40,
		y2 = (h/2) -  30,
		x3 = Math.round( (w/2) + (x2 - x1) + 30 ),
		y3 = Math.round( (h/2) - (y2-y1) - 40 );
		triangle_ne.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (triangle_ne);

		/* smaller, nicer, triangle, compared to the more pointy / aggressive arrows in the rest of this code 
		var triangle_sw = new createjs.Shape();
		triangle_sw.graphics.beginStroke("black");
		var 
		x1 = (w/2) - 40 - 10,
		y1 = (h/2) + 20,
		x2 = (w/2) - 30,
		y2 = (h/2) + 40,
		x3 = Math.round( (w/2) - (x2 - x1) - 30 ),
		y3 = Math.round( (h/2) + (y2-y1) + 20 );
		*/
		var triangle_sw = new createjs.Shape();
		triangle_sw.graphics.beginStroke('navy').beginFill('white');
		var 
		x1 = (w/2) - 40 - 10,
		y1 = (h/2) + 20,
		x2 = (w/2) - 30,
		y2 = (h/2) + 40,
		x3 = Math.round( (w/2) - (x2 - x1) - 40 ),
		y3 = Math.round( (h/2) + (y2-y1) + 30 );
		triangle_sw.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (triangle_sw);

		
		stage.update();
	},
	
	drawTriangles__arrowPointingToNorthWest : function (logoHTMLid) {
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid),
		stage = sa.logo.settings.stage;
		
		var triangle_nw = new createjs.Shape();
		triangle_nw.graphics.beginStroke("black");
		var 
		x1 = (w/2) - 40 - 10,
		y1 = (h/2) - 30,
		x2 = (w/2) - 30,
		y2 = (h/2) - 50,
		x3 = Math.round( (w/2) - (x2 - x1) - 40 ),
		y3 = Math.round( (h/2) - (y1-y2)- 40 );
		triangle_nw.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (triangle_nw);
		
		var triangle_se = new createjs.Shape();
		triangle_se.graphics.beginStroke("black");
		var 
		x1 = (w/2) + 20,
		y1 = (h/2) + 40,
		x2 = (w/2) + 40,
		y2 = (h/2) + 20,
		x3 = Math.round( (w/2) - (x2 - x1) - 40 ),
		y3 = Math.round( (h/2) - (y1-y2)- 40 );
		triangle_se.graphics
			.moveTo( x1, y1 )
			.lineTo( x2, y2 )
			.lineTo( x3, y3 )
			.lineTo( x1, y1 );
		stage.addChild (triangle_se);
		
		stage.update();
	},
	
	drawNESW_squares : function (logoHTMLid) {
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid),
		stage = sa.logo.settings.stage;

		var square_north = new createjs.Shape();
        square_north.graphics.beginStroke('navy').beginFill('white').drawRect( (w/2) - 15, (h/2) - 40 - 10 - 20, 20, 20); 
		sa.logo.settings.square_north = square_north;
        stage.addChild(square_north);
		stage.update();
		
		var square_south = new createjs.Shape();
        square_south.graphics.beginStroke('navy').beginFill('white').drawRect( (w/2) - 15, (h/2) + 40, 20, 20); 
		sa.logo.settings.square_south = square_south;
        stage.addChild(square_south);
		stage.update();
		
		var square_west = new createjs.Shape();
        square_west.graphics.beginStroke('navy').beginFill('white').drawRect( (w/2) - 40 - 10 - 20, (h/2) - 15, 20, 20); 
		sa.logo.settings.square_west = square_west;
        stage.addChild(square_west);
		stage.update();
		
		var square_east = new createjs.Shape();
        square_east.graphics.beginStroke('navy').beginFill('white').drawRect( (w/2) + 40, (h/2) - 15, 20, 20); 
		sa.logo.settings.square_east = square_east;
        stage.addChild(square_east);
		stage.update();
		
	},

	drawEggs : function (logoHTMLid) {
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(logoHTMLid),
		stage = sa.logo.settings.stage;
		
		var elipse_left = new createjs.Shape();
		elipse_left.graphics.beginStroke('navy').drawEllipse ( (w/2)-20, (h/2)-15, 10, 20);
		sa.logo.settings.elipse_left = elipse_left;
        stage.addChild(elipse_left);
		stage.update();

		var elipse_left = new createjs.Shape();
		elipse_left.graphics.beginStroke('navy').drawEllipse ( (w/2), (h/2)-15, 10, 20);
		sa.logo.settings.elipse_left = elipse_left;
        stage.addChild(elipse_left);
		stage.update();
		
	},

	resize_entireWindow : function (resizeEventData) {
		if (resizeEventData.data) resizeEventData = resizeEventData.data;
		var 
		w = sa.logo.settings.width = sa.logo.logoWidth(resizeEventData.logoHTMLid),
		h = sa.logo.settings.height = sa.logo.logoHeight(resizeEventData.logoHTMLid),
		logoHTMLid = resizeEventData.element,
		stage = sa.logo.settings.stage,
		circle = sa.logo.settings.circle;

		//jQuery('#'+logoHTMLid)[0].top = 5;
		//jQuery('#'+logoHTMLid)[0].left = 5;
		jQuery('#'+logoHTMLid)[0].parentNode.style.width = w-10 + 'px';
		jQuery('#'+logoHTMLid)[0].parentNode.style.height = h-10 + 'px';
		jQuery('#'+logoHTMLid)[0].width = w-10;
		jQuery('#'+logoHTMLid)[0].height = h-10;
		
		var x = parseInt((w / 2) - 20);
        var y = parseInt((h / 2) - 20);
		circle.x = x;
		circle.y = y;
		
		stage.update();
	},
	
	
	logoWidth : function (logoHTMLid) {
		return jQuery('#'+logoHTMLid)[0].width - sa.logo.settings.margin;
	},
	logoHeight : function (logoHTMLid) {
		return jQuery('#'+logoHTMLid)[0].height - sa.logo.settings.margin;
	}
}; // sa.logo

function startLogo (logoHTMLid) {
	sa.logo.init_do_createLogo (logoHTMLid);
}

