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

  },

  unsubscribe: function(clientKey, ev) {

  },

  unsubscribeClient: function(clientKey) {

  },

  getSubscriptions: function() {

  },

  getClients: function() {

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
   *  - registeredEvents: [ { table, operations[], columns[]?, conditions[{column, op, value}]  } ]
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
        ret.messageType = 'reply';
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
    for(var i in ClientDB.storage) {
     var client = ClientDB.storage[i];
     ClientDB.send(client, 'notify', { user: user, table: table });
    }
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
   * Client wants to subscribe for a new event
   * @param client
   * @param msg: { event: { tbl, op, [ col ], [ cond ] } }
   */
  subscribe: function(client, msg) {
    client.registeredEvents.push(msg.ev);
  },

  getId: function(client) {
    return client.sessionId;
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
    ClientDB.onMessage(client, msg);
  });

  client.on('disconnect', function() {
    ClientDB.onDisconnect(client);
  })
});
