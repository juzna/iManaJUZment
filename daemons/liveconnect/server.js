/**
 * LiveConnect server
 *
 * Will provide Thrift interface for communication with PHP
 *  and WebSockets (Socket.IO) interface for web clients
 */

var sys = require('sys'),
  thrift = require('thrift'),
  LiveConnectStub = require('../interface/gen-nodejs/LiveConnect.js'),
  ttypes = require('../interface/gen-nodejs/liveconnect_types'),
  http = require('http'),
  io = require('socket.io'),
  _;


// Create thrift server
var thriftServer = thrift.createServer(LiveConnectStub, {
  notify: function(user, table, op, oldData, newData) {
    console.log('Notify: ' + table);
    ClientDB.onNotify.apply(null, arguments);
  },

  subscribe: function(clientKey, ev, timeout) {
    // TODO: implement this method
  },

  unsubscribe: function(clientKey, ev) {
    // TODO: implement this method
  },

  unsubscribeClient: function(clientKey) {
    // TODO: implement this method
  },

  getSubscriptions: function() {
    // TODO: implement this method
  },

  getClients: function() {
    // TODO: implement this method
  }
});
thriftServer.listen(9090);



var ClientDB = {
  // Counter of clients
  counter: 0,

  // Online counter
  online: 0,

  /**
   * Storage for clients
   * Each client has these properties:
   *  - sessionId - unique ID
   *  - registeredEvents: [ { table, operations[], columns[]?, conditions[{column, op, value}], expiry  } ]
   *    - operations: either string 'all', or array with: add, edit, remove
   *
   */
  storage: {},

  /**
   * Add new client and store it to storage
   * @param Client Socket.IO client
   */
  add: function(client) {
    // Increase counters
    ClientDB.counter++;
    ClientDB.online++;

    // Store client
    ClientDB.storage[client.sessionId] = client;

    // Set-up basic properties
    client.registeredEvents = [];
  },

  /**
   * Dispatch incoming message
   * @param client
   * @param msg
   */
  onMessage: function(client, msg) {
    var type = msg.messageType;
    if(type in MessageHandlers) {
      // Execute handler
      var ret = MessageHandlers[type](client, msg);

      // Send back response
      if(typeof ret !== 'undefined') {
        if(ret.__proto__ !== Object.prototype) ret = { data: ret }; // It's not plain object -> convert
        ret.messageType = 'call-reply';
        ret.sequenceId = msg.sequenceId;
        if('memo' in msg) ret.memo = msg.memo;
        client.send(ret);
      }
    }
  },

  onDisconnect: function(client) {
    // Decrease counter
    ClientDB.online--;

    // Remove client from storage
    delete ClientDB.storage[client.sessionId];
  },

  /**
   * New notification received
   */
  onNotify: function(user, table, op, oldData, newData) {
    // Prepare notification object to be sent to clients
    var notification = {
      user: user,
      table: table,
      operation: op,
      oldData: oldData,
      newData: newData
    };

    // Go thru all clients
    for(var i in ClientDB.storage) {
      var client = ClientDB.storage[i];

      // Check their events
      for(var j = 0; j < client.registeredEvents.length; j++) {
        var ev = client.registeredEvents[j];

        if(ClientDB.matchesEvent(ev, table, op, oldData, newData)) {
          notification.id = ev.subscriptionId;
          console.log('Sending notification to', client.sessionId, 'for subscription', ev.subscriptionId);
          ClientDB.send(client, 'notify', notification);
        }
      }
    }
  },

  /**
   * Check if registered event (ev) matches the notification
   * @param Object ev
   * @param String table
   * @param String op
   * @param Object oldData
   * @param Object newData
   * @return bool
   */
  matchesEvent: function(ev, table, op, oldData, newData) {
    // Is it our table?
    if(typeof ev.table === 'string' && ev.table !== table) return false; // subscribed for string
    if(ev.table instanceof Array && 'indexOf' in ev.table && ev.table.indexOf(table) == -1) return false; // subscribed for array

    // Is it our operation?
    if(ev.operations !== 'all' && ev.operations instanceof Array && ev.operations.indexOf(op) == -1) return false; // Not our op

    // Try conditions
    if(ev.conditions instanceof Array) {
      for(var i = 0; i < ev.conditions.length; i++) {
        if(!ClientDB.matchesCondition(ev.conditions[i], newData, oldData)) return false; // Doesn't match this cond
      }
    }

    // Changes in columns we are interested in?
    if(ev.columns && !ClientDB.columnsOfInterest(ev.columns, oldData, newData)) return false;

    // All conditions passed
    return true;
  },

  /**
   * Test if notification matches to a condition
   */
  matchesCondition: function(cond, newData, oldData) {
    return (newData || oldData) &&
           (!newData || ClientDB._matchesCondition(cond, newData)) &&
           (!oldData || ClientDB._matchesCondition(cond, oldData));
  },

  // Check if one condition matches
  _matchesCondition: function(cond, object) {
    var col = cond.column; // What column are we talking about?
    var val = cond.value;

    switch(cond.operation) {
      case 'present':
      case 1:
        return (col in object) && (object[col] !== null);

      case 'eq':
      case 2:
        return (col in object) && (object[col] == val);

      // TODO: implement other operations
      // lt = 3,
      // lte = 4,
      // gt = 5,
      // gte = 6,

      default:
        return true;
    }
  },

  // If change is in columns we care about
  columnsOfInterest: function(cols, oldData, newData) {
    if(!cols) return true;
    for(var i = 0; i < cols.length; i++) {
      var col = cols[i];
      if((col in oldData) || (col in newData)) return true;
    }

    return false;
  },

  /**
   * Send message to client
   * @param client
   * @param msgType
   * @param msg
   */
  send: function(client, msgType, msg) {
    msg.messageType = msgType;
    client.send(msg);
  }
};

var MessageHandlers = {
  /**
   * Get list of all available methods
   */
  availableMethods: function() {
    return Object.keys(MessageHandlers);
  },

  /**
   * Client wants to subscribe for a new event
   * @param client
   * @param msg: { event: { tbl, op, [ col ], [ cond ], timeout } }
   */
  subscribe: function(client, msg) {
    var id = msg.id, eventRequests = msg.events, timeout = msg.timeout || 90;

    for(var i = 0; i < eventRequests.length; i++) {
      var ev = eventRequests[i];

      // TODO: check if received valid event request

      ev.subscriptionId = id;
      ev.expire = (new Date).valueOf() + timeout * 1000; // Timeout in ms

      // add to client
      client.registeredEvents.push(ev);
    }
  },

  extendSubscription: function(client, msg) {
    // TODO:
  },

  unsubscribe: function(client, msg) {
    // TODO:
  },

  /**
   * Get client's ID
   */
  getId: function(client) {
    return client.sessionId;
  },

  // Get number of online clients
  getOnlineClientNum: function() {
    return ClientDB.online;
  },

  // Get total number of clients ever connected
  getTotalClientNum: function() {
    return ClientDB.counter;
  }

};



// Create HTTP server for Socket.IO
server = http.createServer(function(req, res){
  // your normal server code
  res.writeHead(200, {'Content-Type': 'text/html'});
  res.end('<h1>Hello world</h1>');
});
server.listen(9091);

// socket.io, I choose you
var socketIO = io.listen(server, {
  transports: [ 'websocket' ]
});
socketIO.on('connection', function(client){
  ClientDB.add(client);

  // new client is here!
  client.on('message', function(msg) {
    try {
      ClientDB.onMessage(client, msg);
    }
    catch(e) {
      console.log('Exception on client', client.sessionId, ':', e);
      client.send({ messageType: 'exception', error: e.toString() });
      client.disconnect();
      ClientDB.onDisconnect(client);
    }
  });

  client.on('disconnect', function() {
    ClientDB.onDisconnect(client);
  })
});
