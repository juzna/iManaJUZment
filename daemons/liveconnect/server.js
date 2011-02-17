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
  },

  subscribe: function(clientKey, ev, timeout) {

  },

  unsubscribe: function(clientKey, ev) {

  },

  unsubscribeClient: function(clientKey) {

  },

  getSubscriptions: function() {

  }
});
thriftServer.listen(9090);




// Create HTTP server for Socket.IO
server = http.createServer(function(req, res){
  // your normal server code
  res.writeHead(200, {'Content-Type': 'text/html'});
  res.end('<h1>Hello world</h1>');
});
server.listen(9091);

// socket.io, I choose you
var socket = io.listen(server, {
  transports: [ 'websocket' ]
});
socket.on('connection', function(client){
  console.log('new client', client.sessionId);

  // new client is here!
  client.on('message', function(msg) {
    console.log('new msg', sys.inspect(msg));
  });

  client.on('disconnect', function() {
    console.log('client disconnected');
  })
});
