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


var inputFormats = {
  definitions: {},

  /**
   * Add new format handler
   * @param String name
   * @param Function initializer
   * @param Object options
   */
  add: function(name, initializer, options) {
    inputFormats.definitions[name] = Object.extend({
      initializer: initializer
    }, options || {});
  },

  /**
   * Make all formated inputs in container live
   * @param Element inp
   */
  initialize: function(inp, container) {
    if(!inp.hasAttribute('format')) return; // No format given

    var format = inp.getAttribute('format');
    if(!inputFormats.definitions[format]) return; // Given format not exists

    inputFormats.initializeAs(format, inp);
  },

  initializeAs: function(format, inp, options) {
    // Initialize it
    var def = inputFormats.definitions[format];
    def.initializer(inp, options ? Object.extend(Object.clone(def), options) : def, format);
  }
}

document.onLive('input-format', function(container){
	// Update all inputs in container
	container.select('input,textarea').each(function(item) { try { inputFormats.initialize(item, container); } catch(e) { console.error(e); } }  );
	//container.select('button').each(function(item) { if(!Element.hasAttribute(item, 'type')) item.setAttribute('type', 'button'); } );
});


