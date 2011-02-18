/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
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
    operations: ['edit', 2]
  },

  _create: function() {
    var dataset = this.element[0].dataset;
    this.entityName = dataset.entityname;
    this.entityId = dataset.entityid;

    if(!this.entityName || !this.entityId) return;
    // console.log('Creating liveconnect form', this.entityName, this.entityId);

    this.sId = LiveConnect.connect()
      .subscribe( {
        table: this.entityName,
        operations: this.options.operations,
        conditions: 'ID=' + this.entityId
      }, $.proxy(this._onMessage, this) );
  },

  // General callback
  _onMessage: function(what, msg) {
    switch(what) {
      case 'timeout-soft':
      case 'timeout':
        console.log('Liveconnect form timeout, refreshing');
        return true;

      case 'notify':
        this._onNofitication(msg);
        break;

      default:
        console.log('Liveconnect unknown event:', what, msg);
    }
  },

  // Notification received from server
  _onNofitication: function(msg) {
    var self = this, data = msg.newData, numChanged = 0;
    $(this.element[0].elements).each(function() {
      var name = this.name, el = $(this);
      if(name && name in data && el.val() != data[name]) {
        console.log('Change detected', name, el.val(), data[name]);
        numChanged++;

        if(!el.data('live-changed')) {
          el.after('<span style="color: red;">(changed)</span>');
          el.data('live-changed', true);
        }
      }
    });
    console.log('Changed', numChanged);
  },

  destroy: function() {
    console.log('Destroying liveconnect form', this.element);

    // Unsubscribe
    if(this.sId) LiveConnect.unsubscribe(this.sId);

    // Default destructor
    $.Widget.prototype.destroy.apply(this, arguments);
  }
});

})(jQuery);