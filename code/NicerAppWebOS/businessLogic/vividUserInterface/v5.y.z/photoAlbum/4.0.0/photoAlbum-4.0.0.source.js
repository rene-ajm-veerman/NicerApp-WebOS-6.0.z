if (!na.photoAlbum) na.photoAlbum = {
    settings : {},
    
    onload : function () {
        var
        jQueryImg = $('#viewMedia'),
        s = na.photoAlbum.settings,
        w = jQueryImg[0].naturalWidth,
        h = jQueryImg[0].naturalHeight,
        iframeWidth = $('#siteContent .vividDialogContent').width(),
        iframeHeight = $('#siteContent .vividDialogContent').height(),
        imgpw = Math.floor((w*100)/iframeWidth),
        imgph = Math.floor((h*100)/iframeHeight);
        s.naturalWidth = w;
        s.naturalHeight = h;
        //na.m.log (1, 't12', iframeWidth, w, iframeHeight, h, imgpw, imgph);
        if (imgph>imgpw) {
            if (h>iframeHeight) {
                tw = jQueryImg.css({width:'',left:0,height:iframeHeight}).delay(50).width();
                th = jQueryImg.height();
                s.zoomPercentage = (tw*100)/w;
            } else if (w>iframeWidth) {
                th = jQueryImg.css({top:0,width:iframeWidth,height:''}).delay(50).height();
                tw = jQueryImg.width();
                s.zoomPercentage = (th * 100) / h;
            } else if (w>h) {
                tw = jQueryImg.css({width:'',left:0,height:h}).delay(50).width();
                th = jQueryImg.height();
                s.zoomPercentage = (tw*100)/w;
            } else {
                th = jQueryImg.css({top:0,width:w,height:''}).delay(250).height();
                tw = jQueryImg.width();
                s.zoomPercentage = (th*100) / h;
            }

        } else {
            if (w>iframeWidth) {
                th = jQueryImg.css({top:0,width:iframeWidth,height:''}).delay(50).height();
                tw = jQueryImg.width();
                s.zoomPercentage = (th*100) / h;
            } else if (h>iframeHeight) {
                tw = jQueryImg.css({width:'',left:0,height:iframeHeight}).delay(50).width();
                th = jQueryImg.height();
                s.zoomPercentage = (tw*100)/w;
            } else if (w>h) {
                tw = jQueryImg.css({width:'',left:0,height:h}).delay(50).width();
                th = jQueryImg.height();
                s.zoomPercentage = (tw*100)/w;
            } else {
                th = jQueryImg.css({top:0,width:w,height:''}).delay(50).height();
                tw = jQueryImg.width();
                s.zoomPercentage = (th*100) / h;
            }
                
        };
        jQueryImg.css ({position:'absolute'});
        s.minZoom = s.zoomPercentage;
        
        setTimeout(na.photoAlbum.updateZoom,200);
        na.m.hookScrollwheel ($('#viewMedia')[0], na.photoAlbum.mousewheel, false, true);
        document.onmousedown = na.photoAlbum.startDrag;
        document.onmouseup = na.photoAlbum.stopDrag;
        if (!na.photoAlbum.settings.onresizeHooked) {
            window.addEventListener ('resize', function () {
                debugger;
                setTimeout(na.photoAlbum.onload,500) });
            na.photoAlbum.settings.onresizeHooked = true;
        }

    },

    onresize : function (evt) {
    },
    
	startDrag : function (e) {
        // determine event object
        debugger;
        if (!e) {
            var e = window.event;
        }
        if(e.preventDefault) e.preventDefault();

        // IE uses srcElement, others use target
        targ = e.target ? e.target : e.srcElement;

        if (targ.id != 'viewMedia') {return};
        // calculate event X, Y coordinates
            na.photoAlbum.settings.offsetX = e.clientX;
            na.photoAlbum.settings.offsetY = e.clientY;

        // calculate integer values for top and left 
        // properties
        na.photoAlbum.settings.coordX = $('#siteContent .vividDialogContent')[0].scrollLeft;
        na.photoAlbum.settings.coordY = $('#siteContent .vividDialogContent')[0].scrollTop;
        na.photoAlbum.settings.drag = true;
        // move div element
            document.onmousemove=na.photoAlbum.dragDiv;
        return false;
        
    },
    dragDiv : function (e) {
        if (!na.photoAlbum.settings.drag) {return};
        if (!e) { var e= window.event};
        // var targ=e.target?e.target:e.srcElement;
        // move div element
        var deltaX = e.clientX - na.photoAlbum.settings.offsetX;
        var deltaY = e.clientY - na.photoAlbum.settings.offsetY;

        // invert so the photo follows the drag direction
        $('#siteContent .vividDialogContent')[0].scrollLeft = na.photoAlbum.settings.coordX - deltaX;
        $('#siteContent .vividDialogContent')[0].scrollTop = na.photoAlbum.settings.coordY - deltaY;
        return false;
    },
	stopDrag : function () {
        na.photoAlbum.settings.drag=false;
    },
    
    mousewheel : function (evt) {
        var
        s = na.photoAlbum.settings,
        rolled = 0,
        direction = 1,
        container = $('#siteContent .vividDialogContent')[0],
        img = $('#viewMedia')[0];

        if ('wheelDelta' in evt) {
            rolled = evt.wheelDelta;
        } else {
            rolled = -1 * evt.detail;
        }
        rolled = (na.m.userDevice.isInvertedMousewheel ? 1 : -1) * rolled;
        if (rolled < 0) direction = -1;
        direction = -1 * direction;

        // --- mouse position relative to the scrollable container ---
        var rect = container.getBoundingClientRect();
        var mouseX = evt.clientX - rect.left;   // viewport → container
        var mouseY = evt.clientY - rect.top;

        // content-space coordinates under the mouse *before* the zoom
        var contentX = container.scrollLeft + mouseX;
        var contentY = container.scrollTop  + mouseY;

        // remember old size so we can compute the scale factor
        var oldW = img.offsetWidth  || s.tw;
        var oldH = img.offsetHeight || s.th;

        // apply the percentage change (same formula you already had)
        if (s.zoomPercentage) {
            s.zoomPercentage = s.zoomPercentage + (direction * ((s.zoomPercentage / 100) * 3));
        }
        if (s.zoomPercentage < s.minZoom) s.zoomPercentage = s.minZoom;

        // update the image size / position
        na.photoAlbum.updateZoom();

        // new size after updateZoom
        var newW = img.offsetWidth  || s.tw;
        var newH = img.offsetHeight || s.th;

        // how much the content grew (or shrank)
        var scaleX = newW / oldW;
        var scaleY = newH / oldH;

        // the same content point should stay under the mouse
        var newContentX = contentX * scaleX;
        var newContentY = contentY * scaleY;

        container.scrollLeft = newContentX - mouseX;
        container.scrollTop  = newContentY - mouseY;
    },

    onresize : function (settings) {
        na.photoAlbum.updateZoom();
    },

    updateZoom : function () {
        var
        jQueryImg = $('#viewMedia'),
        s = na.photoAlbum.settings,
        w = jQueryImg[0].naturalWidth,
        h = jQueryImg[0].naturalHeight,
        iframeWidth  = $('#siteContent .vividDialogContent').width(),
        iframeHeight = $('#siteContent .vividDialogContent').height(),
        imgpw = Math.floor((iframeWidth  * 100) / w),
        imgph = Math.floor((iframeHeight * 100) / h),
        th = false,
        tw = false;

        if (imgph > imgpw) {
            if (h > iframeHeight) {
                th = (h / 100) * s.zoomPercentage;
                tw = jQueryImg.css({width:'', left:0, height:th}).delay(50).width();
            } else if (w > iframeWidth) {
                tw = (w / 100) * s.zoomPercentage;
                th = jQueryImg.css({top:0, width:tw, height:''}).delay(50).height();
            } else if (w > h) {
                tw = (w / 100) * s.zoomPercentage;
                th = jQueryImg.css({top:0, width:tw, height:''}).delay(50).height();
            } else {
                th = (h / 100) * s.zoomPercentage;
                tw = jQueryImg.css({width:'', left:0, height:th}).delay(50).width();
            }
        } else {
            if (w > iframeWidth) {
                tw = (w / 100) * s.zoomPercentage;
                th = jQueryImg.css({top:0, width:tw, height:''}).delay(50).height();
            } else if (h > iframeHeight) {
                th = (h / 100) * s.zoomPercentage;
                tw = jQueryImg.css({width:'', left:0, height:th}).delay(50).width();
            } else if (w > h) {
                tw = (w / 100) * s.zoomPercentage;
                th = jQueryImg.css({top:0, width:tw, height:''}).delay(50).height();
            } else {
                th = (h / 100) * s.zoomPercentage;
                tw = jQueryImg.css({width:'', left:0, height:th}).delay(50).width();
            }
        }

        s.th = Math.round(th * 1000) / 1000;
        s.tw = Math.round(tw * 1000) / 1000;

        // When the image is larger than the viewport we keep left/top at 0
        // and let the container scroll.  When it is smaller we still centre it.
        var pos = {
            left   : (tw > iframeWidth)  ? 0 : ((iframeWidth  - tw) / 2),
            top    : (th > iframeHeight) ? 0 : ((iframeHeight - th) / 2),
            width  : tw,
            height : th
        };

        jQueryImg.css(pos);

        $('#naPhotoAlbum__control__naturalWidth').html  ('Original\'s width : '  + s.naturalWidth);
        $('#naPhotoAlbum__control__naturalHeight').html ('Original\'s height : ' + s.naturalHeight);
        $('#naPhotoAlbum__control__width').html         ('Width : '  + s.tw);
        $('#naPhotoAlbum__control__height').html        ('Height : ' + s.th);
        $('#naPhotoAlbum__control__zoomPercentage').html('Zoom : '   + Math.round(s.zoomPercentage * 1000) / 1000 + '%');
    }
    
};
