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


var Dialog = Class.create({
  defaultOptions: {
    handleForms: true,
    title: 'new window'
  },

  initialize: function(options) {
    this.options = Object.extend(Object.clone(this.defaultOptions), options || {});
  },

  clear: function() {
    this.elContainer = this.elTitle = this.elContent = this.elCloseBtn = null;
  },

  // Initialize DOM for this dialog
  initDOM: function() {
    if(this.elContainer) return;

    // Create overlay and container for all dialogs
    if(!Dialog.elOverlay) $('body').appendChild(Dialog.elOverlay = Builder.node('div', { className: 'dialog_overlay' } ) );

    Dialog.elOverlay.appendChild(this.elContainer = Builder.node('div', { className: 'dialog' }, [
      this.elCloseBtn = Builder.node('span', { className: 'close', title: 'Close window' }).update('X').observe('click', this.destroy.bind(this)),
      this.elTitle = Builder.node('h2').update(this.options.title || ''),
      this.elContent = Builder.node('div', { className: 'content' })
    ]));

    if(this.options.handleForms) this.elContainer.observe('submit', this.handleSubmit.bindAsEventListener(this));
  },

  update: function(data) {
    this.initDOM();
    this.elContent.update(data);
    return this;
  },

  setTitle: function(title) {
    this.initDOM();
    this.elTitle.update(title);
    return this;
  },

  load: function(url, options) {
    new Ajax.Updater(this.elContent, url, options);
    return this;
  },

  show: function() {
    if(!this.elContainer) this.initDOM();
    Dialog.elOverlay.show();
    this.elContainer.show();
    return this;
  },

  hide: function() {
    if(this.elContainer) this.elContainer.hide();
    if(Dialog.elOverlay) Dialog.elOverlay.hide();
    return this;
  },

  destroy: function() {
    this.elContainer.remove(); // Remove container from DOM
    if(Dialog.elOverlay) Dialog.elOverlay.hide();
    this.clear();
  },

  handleSubmit: function(ev) {
    if(ev.ctrlKey || ev._submittedByEvent && ev._submittedByEvent.ctrlKey) return;
    console.log('Handeled form submit', ev, ev.target);
    ev.stop();
    var frm = ev.target, orig = frm._submittedBy;

    // There is original button which submitted the form
    if(orig) {
      if(orig.name == 'cancel') {
        this.destroy();
        return;
      }
    }

    // Send ajax
    frm.request({
      requestHeaders: {
        wantJSON: 1
      },
      
      onComplete: function(transport) {
        if(transport.responseJSON && transport.responseJSON.state) {
          this.setTitle(transport.responseJSON.message);
          this.update('');
          window.setTimeout(this.destroy.bind(this), 500);
        }
        else {
          alert(transport.responseJSON.message || 'Unknown error occurred');
        }
      }.bind(this)
    })
  }


});

(function($) {
  $(function() {
    $('a.in_dialog').live('click', function(ev) {
      ev.preventDefault();
      var d = new Dialog({title: jQuery(this).text() }).show().update('Loading...').load(this.href + ' #content');
    });

    // For all forms, save button which was used to submit it
    $('body').bind('click', function(ev) {
      var trg = ev.target;
      if((trg instanceof HTMLButtonElement || trg instanceof HTMLInputElement) && (trg.type == 'submit' || trg.type == 'image') && trg.form) {
        trg.form._submittedByEvent = ev;
        trg.form._submittedBy = trg;
      }
    });
  });
})(jQuery);
