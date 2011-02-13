/**
 * Created by JetBrains PhpStorm.
 * User: juzna
 * Date: 2/13/11
 * Time: 10:01 PM
 * To change this template use File | Settings | File Templates.
 */


var Dialog = Class.create({
  defaultOptions: {
    title: 'new window'
  },

  initialize: function(options) {
    this.options = Object.extend(Object.clone(Dialog.defaultOptions), options || {});
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
    this.elContainer.show();
    return this;
  },

  hide: function() {
    if(this.elContainer) this.elContainer.hide();
    return this;
  },

  destroy: function() {
    this.elContainer.remove(); // Remove container from DOM
    this.clear();
  }

});
