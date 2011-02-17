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
  var subscriptions = {}; // Map: id -> { [eventRequests], cb, timeout }
  var subscriptionCounter = 0;


  /******       Connection      *******/


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

  // Server has been disconnected
  function onDisconnect() {
    console.log('Disconnected');
    socket = null;
    connected = false;
    state = 'disconnected';
  }





  /*******     Common messaging     ***********/

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

    // LiveConnect change notification
    else if(type == 'notify') onNotification(msg);

    // Misc message -> fire event
    else fireEvent(type, msg);
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






  /******      Method calls     *******/

  // Call has expired
  function callExpiry(seqId) {
    var call = callbacks[seqId];
    if(call && call.error) call.error('timeout');

    delete callbacks[seqId];
  }




  /*******    Subscription   *******/

  /**
   * Notification received
   * @package subscription
   * @param Object msg Notification details, contains:
   *   - id - subscription ID
   *   - user
   *   - table
   *   - operation
   *   - rowIndex
   *   - oldData
   *   - newData
   */
  function onNotification(msg) {
    var sId = msg.id;
    if(subscriptions[sId]) subscriptions[sId].cb('notify', msg);
  }

  // Prepare message for subscription to an event
  function sanitizeEventRequest(ev) {
    var x;

    // Check table
    if(typeof ev.table !== 'string') throw new Error('Table is expected to be a string');


    // Check operations
    if(!ev.operations) ev.operations = 'all';
    if(typeof ev.operations === 'string' && ev.operations !== 'all') ev.operations = ev.operations.split(',');

    // Check conditions
    var conds = ev.conditions;
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
    ev.conditions = conds;


    // Check columns
    if(typeof ev.columns === 'string') ev.columns = ev.columns.split(',');

    return ev;
  }  

  /**
   * Soft timeout of a subscription
   * @package subscription
   * @param sId Subscription ID
   */
  function subscriptionTimeout(sId) {
    var sub;
    if(!(sub = subscriptions[sId])) return; // Subscription not found

    // Fire soft timeout callback
    var ret = sub.cb('timeout-soft', sId);
    if(ret === true) { // Extend this subscription
      // Send subscription request
      Lib.sendMessage('extendSubscription', {
        id: sId,
        events: sub.eventRequests,
        timeout: sub.hardTimeout
      });

      // Renew timeout
      sub.resTimeout = setTimeout(subscriptionTimeout, sub.softTimeout * 1000, sId);
    }

    // Should I do something when timeouted, or just wait for the server to drop?

  }




  // Exported methods
  var Lib;
  return Lib = {


    /*******    State methods    *******/
    isConnected: function() {
      return connected;
    },

    getState: function() {
      return state;
    },





    /*******    Connection     *******/

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




    /*******    Misc messages    *******/

    // Send a message
    sendMessage: function(type, msg) {
      msg.messageType = type;

      if(connected) socket.send(msg);
      else messageBuffer.push(msg);

      return Lib; // provides fluent interface
    },

    on: function(eventName, cb) {
      if(!handlers[eventName]) handlers[eventName] = [];
      handlers[eventName].push(cb);

      return Lib; // Provides fluent interface
    },




    /*******    Subscription   *******/

    // Subscribe for events
    subscribe: function(id, eventRequests, cb, timeout, hardTimeout) {
      // Process smart arguments
      if($.isPlainObject(id) || id instanceof Array) {
        hardTimeout = timeout;
        timeout = cb;
        cb = eventRequests;
        eventRequests = id;
        id = '__' + ++subscriptionCounter;
      }
      if(!timeout) timeout = 90;
      if(!hardTimeout || hardTimeout < timeout) hardTimeout = Math.ceil(timeout * 1.2);

      // Sanitize event requests
      eventRequests = ( $.isPlainObject(eventRequests) ? [ eventRequests ] : eventRequests ).map(sanitizeEventRequest);

      // Send subscription request
      Lib.sendMessage('subscribe', {
        id: id,
        events: eventRequests,
        timeout: hardTimeout
      });

      // Store this request details
      subscriptions[id] = {
        eventRequests: eventRequests,
        cb: cb || $.noop,
        softTimeout: timeout,
        hardTimeout: hardTimeout,
        resTimeout: setTimeout(subscriptionTimeout, timeout * 1000, id)
      };

      return id;
    },

    /**
     * Drop existing subscription
     */
    unsubscribe: function(sId) {
      // Send unsubscribe to the server
      Lib.sendMessage('unsubscribe', { id: sId } );

      // Clean stored subscription
      if(subscriptions[sId]) {
        var sub = subscriptions[sId];

        // Clear timeout
        if(sub.resTimeout) clearTimeout(sub.resTimeout);
        sub.resTimeout = null;

        // Callback
        sub.cb('unsubscribe-user');

        delete subscriptions[sId];
      }

      return Lib; // Provides fluent interface
    },





    /*******    Method calls    *******/

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





    /*********   Debug      ********/

    getInternals: function() {
      return ({
        handlers: handlers,
        state: state,
        messageBuffer: messageBuffer,
        sequenceId: sequenceId,
        callbacks: callbacks,
        subscriptions: subscriptions,
        subscriptionCounter: subscriptionCounter
      });
    }
  }
})(jQuery);
