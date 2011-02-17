/**
 * Created by JetBrains PhpStorm.
 * User: juzna
 * Date: 2/17/11
 * Time: 2:14 PM
 * To change this template use File | Settings | File Templates.
 */

var LiveConnect = (function($) {
  var socketIOAdded = false;
  var reconnectTimeout = 300; // In ms
  var reconnectCounter = 0;
  var socket;
  var handlers = {}; // Map: msgType -> [ cb ]

  // Add socket IO library
  function addSocketIO() {
    if(socketIOAdded) return; // It's already there

    $('script:first').after($('<script src="http://' + location.host + ':9091/socket.io/socket.io.js" />'))
    socketIOAdded = true;
  }

  // Try to connect
  function connectionAttempt() {
    var no = reconnectCounter++; // Number of this attempt

    if(!connect()) {
      setTimeout(connectionAttempt(), reconnectTimeout);
      reconnectTimeout *= 2;
    }
  }

  // Do the connection
  function connect() {
    if(!window.io || !window.io.Socket) return false;

    socket = new io.Socket(undefined, {
        port: 9091,
        transports: [ 'websocket' ]
      })
      .connect()
      .on('message', onMessage)
      .on('disconnect', onDisconnect);

    return true;
  }

  // New message received
  function onMessage(msg) {
    var type = msg.messageType;
    if(!type) {
      console.log('Unknown message', msg);
      return;
    }
    else if(type === 'exception') console.log('Received exception', msg);

    // Just log it for now
    console.log('Received message', type, msg);
  }

  // Server has been disconnected
  function onDisconnect() {
    console.log('Disconnected');
    socket = null;
  }

  // Prepare message for subscription to an event
  function prepareSubscriptionMessage(table, operations, conds, cols) {

  }

  var Lib;
  return Lib = {
    isConnected: function() {
      return !!socket;
    },

    // Send a message
    sendMessage: function(type, msg) {
      msg.messageType = type;
      socket.send(msg);
    },

    // Subscribe for events
    subscribe: function(table, operations, conds, cols) {
      var msg = prepareSubscriptionMessage(table, operations, conds, cols);
      if(msg) {
        Lib.sendMessage('subscribe', msg);
        return true;
      }
      else return false;
    }
  }
})(jQuery);