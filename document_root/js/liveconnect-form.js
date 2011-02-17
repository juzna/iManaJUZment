/**
 * Created by JetBrains PhpStorm.
 * User: juzna
 * Date: 2/17/11
 * Time: 8:06 PM
 * To change this template use File | Settings | File Templates.
 */

// Add handlers
/*jQuery(function($) {
  // On new nodes inserted into DOM
  $(document).bind('DOMNodeInserted', function(ev) {
    console.log(ev, ev.target);
    $('form.liveconnect', ev.target).frmLiveConnect();
  })

  // When nodes are removed from the DOM
  .bind('DOMNodeRemoved', function(ev) {
    console.log(ev, ev.target);
    $('form.liveconnect', ev.target).frmLiveConnect('destroy');
  });
});
*/

(function($) {

$.widget("ui.frmLiveConnect", {
  options: {

  },

  _create: function() {
    console.log('Creating liveconnect form', this);
  },

  destroy: function() {
    console.log('Destroying liveconnect form', this);

    // Default destructor
    $.Widget.prototype.destroy.apply(this, arguments);
  }
});

})(jQuery);