/**
 * Created by JetBrains PhpStorm.
 * User: juzna
 * Date: 2/16/11
 * Time: 2:14 PM
 * To change this template use File | Settings | File Templates.
 */

(function($) {

var Parent = $.ui.selectmenu;
$.widget("ui.selectmenu2", Parent, {
  widgetBaseClass: 'ui-selectmenu', // Use classic styling
  widgetEventPrefix: 'selectmenu',
  
  listContainerTag: 'div',
  listItemTag: 'tr',
  listItemSelector: 'tr',

  _create: function() {
    // Parent call
    Parent.prototype._create.apply(this, arguments);

    // Set up container selector
    this.listContainerSelector = 'div.' + this.widgetBaseClass + '-menu';
  },

  _createListContainer: function() {
    // Add inner table
    return Parent.prototype._createListContainer.apply(this, arguments)
      .append('<table><thead></thead><tbody></tbody></table>')
      .css('overflow-x', 'hidden');
  },

  _getOptionData: function(option) {
    // Add more options
    return $.extend(Parent.prototype._getOptionData.apply(this, arguments), {
      data: JSON.parse(option.dataset.data || null) || {}
    });
  },

  _createListItem: function(optionData) {
    return $.builder('<tr role="presentation" '+ (optionData.typeahead ? ' typeahead="' + optionData.typeahead + '"' : '' ) + ' />',
      $.builder('td', '<a href="#" tabindex="-1" role="option" aria-selected="false">' + optionData.text + '</a>'),
      $.builder('td', optionData.data.ahoj)
    );
  },

  _appendItemToList: function(item, optionData) {
    this.list.find('tbody').append(item);
  },

  _clearList: function() {
    this.list.find('tbody').html('');
  },

  _updateListWidth: function() {
    this.list.css('min-width', this.element.width());
  }

});

})(jQuery);
