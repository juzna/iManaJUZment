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

/**
* TODO: write comments
*/
var Observer = {
  /**
  * Add observer to scope
  * @param Object scope Scope to store this observer, eg. your SelectBox, UserFormat etc. instance
  * @param Element element Element for observing events
  * @param string eventName Name of event (of array of event names)
  * @param function callback Callback for this event
  * @param mixed bind Bind callback to: when true, bind to scope; when {scope: .., args: [] } then bind to scope with args; when object, bind to it; otherwise no binding
  * @param string tag Tag for later easier removing
  * @retun bool True if success
  */
  observe: function(scope, element, eventName, callback, bind, tag, capture) {
    if(typeof scope != 'object') return false;
    if(typeof scope._registeredEvents == 'undefined') scope._registeredEvents = [];
    
    // Create callback
    var cb;
    if(bind === true) cb = callback.bind(scope);
    else if(Object.isArray(bind)) cb = Function.bindAsEventListener.apply(callback, [ scope ].concat(bind));
    else if(typeof bind == 'object' && bind.scope && bind.args) cb = Function.bindAsEventListener.apply(callback, [ bind.scope ].concat($A(bind.args)));
    else if(typeof bind == 'object') cb = callback.bind(bind);
    else cb = callback; // No bind
    
    // Get event name list
    var eventList;
    if(typeof eventName == 'string') eventList = [ eventName ];
    else if(Object.isArray(eventName)) eventList = eventName;
    else throw new Exception("wrong event name");
    
    
    // Register all events
    var len = eventList.length;
    for(var i = 0; i < len; ++i) {
      eventName = eventList[i];
      
      scope._registeredEvents.push( { element: element, eventName: eventName, callback: cb, bind: bind, tag: tag } ); // Register
      Event.observe(element, eventName, cb, capture); // Add
    }
    
    return true;
  },
  
  /**
  * Remove matched event handlers
  * @param Object scope Scope to store this observer, eg. your SelectBox, UserFormat etc. instance
  * @param Element element Element for observing events (optional)
  * @param string eventName Name of event (or array of event names) (optional)
  * @param string tag Tag for later easier removing (optional)
  * @return int Number of removed observers
  */
  stop: function(scope, element, eventName, tag) {
    if(typeof scope != 'object') return false;
    if(!Object.isArray(scope._registeredEvents)) return true; // No events
    
    // Go thru registered events and remove requested
    var cnt = scope._registeredEvents.length, removed = 0;
    for(var i = 0; i < cnt; ++i) {
      var item = scope._registeredEvents[i];
      
      var match = true; // Implicit action is remove
      
      if(typeof element != 'undefined' && element != item.element) match = false; // It's not our element
      else if(typeof tag != 'undefined' && tag != item.tag) match = false; // Not our tag
      else if(typeof eventName == 'string' && eventName != item.eventName) match = false; // Not our event name
      else if(Object.isArray(eventName) && eventName.indexOf(item.eventName) == -1) match = false; // Event name not in our event name list
      
      // Remove is matched
      if(match) {
        console.log('Removing observer', item.eventName, 'with tag', item.tag, item);
        Event.stopObserving(item.element, item.eventName, item.callback); // Remove observer
        delete scope._registeredEvents[i]; // Unregister
        removed++;
      }
    }
    
    // Compact list of registered events (remove deleted positions)
    if(removed > 0) scope._registeredEvents = scope._registeredEvents.compact();
    
    return removed;
  },
  
  /**
  * Remove all event handlers
  * @param Object scope Scope to store this observer, eg. your SelectBox, UserFormat etc. instance
  */
  clear: function(scope) {
    if(typeof scope != 'object') return false;
    if(!Object.isArray(scope._registeredEvents)) return true; // No events
    
    // Go thru registered events and remove requested
    var cnt = scope._registeredEvents.length;
    for(var i = 0; i < cnt; ++i) {
      var item = scope._registeredEvents[i];
      Event.stopObserving(item.element, item.eventName, item.callback); // Remove observer
    }
    
    // Clear list
    scope._registeredEvents = [];
  }
};
