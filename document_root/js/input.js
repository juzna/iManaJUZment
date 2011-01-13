
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

    // Initialize it
    var def = inputFormats.definitions[format];
    def.initializer(inp, def, format);
  }
}

document.onLive('input-format', function(container){
	// Update all inputs in container
	container.select('input,textarea').each(function(item) { try { inputFormats.initialize(item, container); } catch(e) { console.error(e); } }  );
	//container.select('button').each(function(item) { if(!Element.hasAttribute(item, 'type')) item.setAttribute('type', 'button'); } );
});


