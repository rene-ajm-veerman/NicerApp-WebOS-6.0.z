na.vc = {
};

/*global jQuery */
/*!
* FitText.js 1.2
*
* Copyright 2011, Dave Rupert http://daverupert.com
* Released under the WTFPL license
* http://sam.zoy.org/wtfpl/
*
* Date: Thu May 05 14:23:00 2011 -0600
*/

(function( $ ){

  $.fn.fitText = function( kompressor, options ) {

    // Setup options
    var compressor = kompressor || 1,
        settings = $.extend({
          'minFontSize' : Number.NEGATIVE_INFINITY,
          'maxFontSize' : Number.POSITIVE_INFINITY
        }, options);

    return this.each(function(){

      // Store the object
      var $this = $(this);

      // Resizer() resizes items based on the object width divided by the compressor * 10
      var resizer = function () {
        $this.css('font-size', Math.max(Math.min($this.width() / (compressor*10), parseFloat(settings.maxFontSize)), parseFloat(settings.minFontSize)));
      };

      // Call once to set.
      resizer();

      // Call on resize. Opera debounces their resize by default.
      $(window).on('resize.fittext orientationchange.fittext', resizer);

    });

  };

})( jQuery );


class vividComponent {
  types = [ 'inputSearchBox', 'btn', 'btnDropdownMenu' ];
  constructor() {
  }
}

class vividComponentUIpart {
  types = [ 'gif', 'jpg', 'png', 'apng', 'video', 'audio', 'svg', 'webgl' ];
  // what's this? a just-in-time rendering engine much like na.desktop.resize() calls na.desktop.gotoPreProcessor.parse() and through that, na.m.walkArray()
  constructor () {
  }
}
