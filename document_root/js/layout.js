

(function($) {
  function resizeWindow() {
    var h = $(window).height() - $('#header').height() - $('#footer').height();
    var h = Math.max(100, h);
    $('#content').css('min-height', h);
  }

  // Resize window on load
  $(resizeWindow);

  // On resize, change the window size
  $(window).bind('resize', resizeWindow);
})(jQuery);