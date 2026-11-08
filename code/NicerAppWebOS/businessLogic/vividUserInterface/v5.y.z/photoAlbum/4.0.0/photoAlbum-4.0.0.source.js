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

        // ------------------------------------------------------------------
        // inside na.photoAlbum.onload  – replace the two document.on* lines
        // ------------------------------------------------------------------
        na.m.hookScrollwheel ($('#viewMedia')[0], na.photoAlbum.mousewheel, false, true);

        // mouse
        document.addEventListener('mousedown',  na.photoAlbum.startDrag, {passive:false});
        document.addEventListener('mouseup',    na.photoAlbum.stopDrag);
        document.addEventListener('mousemove',  na.photoAlbum.dragDiv,   {passive:false});

        // touch
        document.addEventListener('touchstart', na.photoAlbum.startDrag, {passive:false});
        document.addEventListener('touchend',   na.photoAlbum.stopDrag);
        document.addEventListener('touchcancel',na.photoAlbum.stopDrag);
        document.addEventListener('touchmove',  na.photoAlbum.dragDiv,   {passive:false});

        if (!na.photoAlbum.settings.onresizeHooked) {
            window.addEventListener('resize', function () {
                setTimeout(na.photoAlbum.onload, 500);
            });
            na.photoAlbum.settings.onresizeHooked = true;
        }

    },

    onresize : function (evt) {
    },
    
    // ------------------------------------------------------------------
    // replace the three drag methods
    // ------------------------------------------------------------------
    startDrag : function (e) {
        // normalize event (mouse or first touch)
        if (!e) e = window.event;
        var touch = e.touches && e.touches[0];
        var clientX = touch ? touch.clientX : e.clientX;
        var clientY = touch ? touch.clientY : e.clientY;

        // only react when the photo itself is the target
        var targ = e.target || e.srcElement;
        if (targ.id !== 'viewMedia') return;

        if (e.preventDefault) e.preventDefault();   // stop page scroll / text selection

        var s = na.photoAlbum.settings;
        s.offsetX = clientX;
        s.offsetY = clientY;
        s.coordX  = $('#siteContent .vividDialogContent')[0].scrollLeft;
        s.coordY  = $('#siteContent .vividDialogContent')[0].scrollTop;
        s.drag    = true;
    },

    dragDiv : function (e) {
        var s = na.photoAlbum.settings;
        if (!s.drag) return;

        if (!e) e = window.event;
        var touch = e.touches && e.touches[0];
        var clientX = touch ? touch.clientX : e.clientX;
        var clientY = touch ? touch.clientY : e.clientY;

        if (e.preventDefault) e.preventDefault();

        var deltaX = clientX - s.offsetX;
        var deltaY = clientY - s.offsetY;

        // invert so the photo follows the finger / mouse
        var el = $('#siteContent .vividDialogContent')[0];
        el.scrollLeft = s.coordX - deltaX;
        el.scrollTop  = s.coordY - deltaY;

        // keep reference point up-to-date (smooth continuous drag)
        s.offsetX = clientX;
        s.offsetY = clientY;
        s.coordX  = el.scrollLeft;
        s.coordY  = el.scrollTop;
    },

    stopDrag : function () {
        na.photoAlbum.settings.drag = false;
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
