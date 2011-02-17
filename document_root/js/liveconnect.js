/**
 * Created by JetBrains PhpStorm.
 * User: juzna
 * Date: 2/17/11
 * Time: 2:14 PM
 * To change this template use File | Settings | File Templates.
 */

/**
 * Example:
 * LiveConnect.connect()
 *   .subscribe('APIP')
 *   .subscribe('AP')
 *   .on('test', function(msg) {
 *     console.log('Got test event', msg);
 *   })
 *   .call('getId', {}, function(msg) {
 *     console.log('Got ID:', msg);
 *   });
 */

var LiveConnect = (function($) {
  var socketIOAdded = false;
  var reconnectTimeout = 300; // In ms
  var reconnectCounter = 0;
  var socket;
  var handlers = {}; // Map: msgType -> [ cb ]
  var connected = false;
  var state = 'initial';
  var messageBuffer = [];
  var sequenceId = 0; // For calls
  var callbacks = {}; // For calls

  // Add socket IO library
  function addSocketIO() {
    if(socketIOAdded) return; // It's already there

    $('script:first').after($('<script src="http://' + window.location.host + ':9091/socket.io/socket.io.js" />'))
    socketIOAdded = true;
  }

  // Try to connect
  function connectionAttempt() {
    addSocketIO();
    var no = reconnectCounter++; // Number of this attempt

    if(!connect()) {
      state = 'failed';
      console.log('Connect unsuccessfull, will retry in ', reconnectTimeout / 1000, 'seconds');
      setTimeout(connectionAttempt, reconnectTimeout);
      reconnectTimeout *= 2;
    }
  }

  // Do the connection
  function connect() {
    console.log('Trying to connect');
    if(!window.io || !window.io.Socket) return false;

    socket = new io.Socket(undefined, {
        port: 9091,
        transports: [ 'websocket' ]
      })
      .connect()
      .on('connect', onConnect)
      .on('message', onMessage)
      .on('disconnect', onDisconnect);

    state = 'connecting';
    return true;
  }

  // Connection is estabilished
  function onConnect() {
    connected = true;
    state = 'connected';

    if(messageBuffer.length) sendMessagesFromBuffer();
  }

  // Send all messages from buffer
  function sendMessagesFromBuffer() {
    var msg;
    console.log('Sending', messageBuffer.length, 'messages from buffer');
    while(msg = messageBuffer.shift()) socket.send(msg);
  }

  // New message received
  function onMessage(msg) {
    var type = msg.messageType, seqId = msg.sequenceId;

    // Just log it for now
    console.log('Received message', type, msg);

    // Unknown type
    if(!type) {
      console.log('Unknown message', msg);
      return;
    }

    // Call-reply
    else if(type === 'call-reply') {
      if(callbacks[seqId]) {
        clearTimeout(callbacks[seqId].timeout); // Remove timeout

        try {
          (callbacks[seqId].success || function() {})(msg, callbacks[seqId]);
        } catch(e) {
          console.log('Error in LiveConnect callback', e);
        }

        // Remove from memory
        delete callbacks[seqId];
      }
      else {
        console.log('LiveConnect callback response, but no call with that - probably expired');
      }
    }

    // Exception
    else if(type === 'exception') {
      // Execute error callback
      if(callbacks[seqId]) {
        try {
          (callbacks[seqId].error || function() {})(msg, callbacks[seqId]);
        } catch(e) {
          console.log('Error in LiveConnect exception callback', e);
        }
      }

      console.log('Received exception', msg);
    }

    // Misc message -> fire event
    else fireEvent(type, msg);
  }

  // Server has been disconnected
  function onDisconnect() {
    console.log('Disconnected');
    socket = null;
    connected = false;
    state = 'disconnected';
  }

  // Prepare message for subscription to an event
  function prepareEventDefinition(table, operations, conds, cols) {
    var x;

    // Check conditions
    if(typeof conds === 'string') conds = conds.split(',');
    if(conds instanceof Array) conds = conds.map(function(item) {
      if($.isPlainObject(item)) return item;
      else if(item instanceof Array) return { column: item[0], operation: item[1], value: item[2] };
      else if(typeof item === 'string') {
        if(item.match(/^[a-z0-9]+$/i)) return { column: item, operation: 'present' };
        else if(x = item.match(/^([a-z0-9]+)=(.+)$/i)) return { column: x[1], operation: 'eq', value: x[2] };
      }
    });
    else conds = null;

    // Create request-event object
    return {
      table: String(table),
      operations: operations instanceof Array ? operations : 'all',
      conditions: conds,
      columns: cols instanceof Array ? cols : null
    }

  }

  // Call has expired
  function callExpiry(seqId) {
    var call = callbacks[seqId];
    if(call && call.error) call.error('timeout');

    delete callbacks[seqId];
  }

  // Fire event
  function fireEvent(name, msg) {
    if(!(name in handlers)) return;
    if(!handlers[name].length) return;

    // Execute all callbacks
    handlers[name].each(function(cb) {
      cb(msg);
    })
  }

  // Exported methods
  var Lib;
  return Lib = {
    isConnected: function() {
      return connected;
    },

    getState: function() {
      return state;
    },

    // Try to connect
    connect: function() {
      if(!socket) connectionAttempt();
      return Lib; // provides fluent interface
    },

    // Disconnect
    disconnect: function() {
      // Do it
      if(socket) {
        socket.disconnect();
        connected = false;
        state = 'disconnected';
        socket = null;
      }

      return Lib; // provides fluent interface
    },

    // Send a message
    sendMessage: function(type, msg) {
      msg.messageType = type;

      if(connected) socket.send(msg);
      else messageBuffer.push(msg);

      return Lib; // provides fluent interface
    },

    // Subscribe for events
    subscribe: function(table, operations, conds, cols) {
      var eventDefinition = prepareEventDefinition(table, operations, conds, cols);
      if(eventDefinition) Lib.sendMessage('subscribe', { ev: eventDefinition } );

      return Lib; // provides fluent interface
    },

    /**
     *
     * @param method
     * @param msg
     * @param cb
     * @param errCb
     */
    call: function(method, msg, cb, errCb, timeout) {
      var seqId = ++sequenceId;

      // Store callbacks
      callbacks[seqId] = {
        method: method,
        success: cb,
        error: errCb,
        added: (new Date).valueOf()
      }

      // Add expiry callback
      callbacks[seqId].timeout = setTimeout(callExpiry, (timeout || 10) * 1000, seqId);

      // Send it
      msg.sequenceId = seqId;
      Lib.sendMessage(method, msg);

      return Lib; // Provides fluent interface
    },

    on: function(eventName, cb) {
      if(!handlers[eventName]) handlers[eventName] = [];
      handlers[eventName].push(cb);

      return Lib; // Provides fluent interface
    }
  }
})(jQuery);
