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
