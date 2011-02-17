//
// Autogenerated by Thrift
//
// DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
//
var Thrift = require('thrift').Thrift;

var ttypes = require('./liveconnect_types');
//HELPER FUNCTIONS AND STRUCTURES

var LiveConnect_notify_args = function(args){
  this.user = null
  this.table = null
  this.op = null
  this.oldData = null
  this.nwData = null
if( args != null ){  if (null != args.user)
  this.user = args.user
  if (null != args.table)
  this.table = args.table
  if (null != args.op)
  this.op = args.op
  if (null != args.oldData)
  this.oldData = args.oldData
  if (null != args.nwData)
  this.nwData = args.nwData
}}
LiveConnect_notify_args.prototype = {}
LiveConnect_notify_args.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 1:      if (ftype == Thrift.Type.I32) {
        this.user = input.readI32()
      } else {
        input.skip(ftype)
      }
      break
      case 2:      if (ftype == Thrift.Type.STRING) {
        this.table = input.readString()
      } else {
        input.skip(ftype)
      }
      break
      case 3:      if (ftype == Thrift.Type.I32) {
        this.op = input.readI32()
      } else {
        input.skip(ftype)
      }
      break
      case 4:      if (ftype == Thrift.Type.MAP) {
        {
          var _size21 = 0
          var rtmp3
          this.oldData = {}
          var _ktype22 = 0
          var _vtype23 = 0
          rtmp3 = input.readMapBegin()
          _ktype22= rtmp3.ktype
          _vtype23= rtmp3.vtype
          _size21= rtmp3.size
          for (var _i25 = 0; _i25 < _size21; ++_i25)
          {
            key26 = null
            val27 = null
            key26 = input.readString()
            val27 = input.readString()
            this.oldData[key26] = val27
          }
          input.readMapEnd()
        }
      } else {
        input.skip(ftype)
      }
      break
      case 5:      if (ftype == Thrift.Type.MAP) {
        {
          var _size28 = 0
          var rtmp3
          this.nwData = {}
          var _ktype29 = 0
          var _vtype30 = 0
          rtmp3 = input.readMapBegin()
          _ktype29= rtmp3.ktype
          _vtype30= rtmp3.vtype
          _size28= rtmp3.size
          for (var _i32 = 0; _i32 < _size28; ++_i32)
          {
            key33 = null
            val34 = null
            key33 = input.readString()
            val34 = input.readString()
            this.nwData[key33] = val34
          }
          input.readMapEnd()
        }
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_notify_args.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_notify_args')
  if (null != this.user) {
    output.writeFieldBegin('user', Thrift.Type.I32, 1)
    output.writeI32(this.user)
    output.writeFieldEnd()
  }
  if (null != this.table) {
    output.writeFieldBegin('table', Thrift.Type.STRING, 2)
    output.writeString(this.table)
    output.writeFieldEnd()
  }
  if (null != this.op) {
    output.writeFieldBegin('op', Thrift.Type.I32, 3)
    output.writeI32(this.op)
    output.writeFieldEnd()
  }
  if (null != this.oldData) {
    output.writeFieldBegin('oldData', Thrift.Type.MAP, 4)
    {
      output.writeMapBegin(Thrift.Type.STRING, Thrift.Type.STRING, Thrift.objectLength(this.oldData))
      {
        for(var kiter35 in this.oldData)        {
          if (this.oldData.hasOwnProperty(kiter35))
          {
            var viter36 = this.oldData[kiter35]
            output.writeString(kiter35)
            output.writeString(viter36)
          }
        }
      }
      output.writeMapEnd()
    }
    output.writeFieldEnd()
  }
  if (null != this.nwData) {
    output.writeFieldBegin('nwData', Thrift.Type.MAP, 5)
    {
      output.writeMapBegin(Thrift.Type.STRING, Thrift.Type.STRING, Thrift.objectLength(this.nwData))
      {
        for(var kiter37 in this.nwData)        {
          if (this.nwData.hasOwnProperty(kiter37))
          {
            var viter38 = this.nwData[kiter37]
            output.writeString(kiter37)
            output.writeString(viter38)
          }
        }
      }
      output.writeMapEnd()
    }
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_notify_result = function(args){
}
LiveConnect_notify_result.prototype = {}
LiveConnect_notify_result.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_notify_result.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_notify_result')
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_subscribe_args = function(args){
  this.clientKey = null
  this.ev = null
  this.timeout = null
if( args != null ){  if (null != args.clientKey)
  this.clientKey = args.clientKey
  if (null != args.ev)
  this.ev = args.ev
  if (null != args.timeout)
  this.timeout = args.timeout
}}
LiveConnect_subscribe_args.prototype = {}
LiveConnect_subscribe_args.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 1:      if (ftype == Thrift.Type.STRING) {
        this.clientKey = input.readString()
      } else {
        input.skip(ftype)
      }
      break
      case 2:      if (ftype == Thrift.Type.STRUCT) {
        this.ev = new ttypes.EventDefinition()
        this.ev.read(input)
      } else {
        input.skip(ftype)
      }
      break
      case 3:      if (ftype == Thrift.Type.I32) {
        this.timeout = input.readI32()
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_subscribe_args.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_subscribe_args')
  if (null != this.clientKey) {
    output.writeFieldBegin('clientKey', Thrift.Type.STRING, 1)
    output.writeString(this.clientKey)
    output.writeFieldEnd()
  }
  if (null != this.ev) {
    output.writeFieldBegin('ev', Thrift.Type.STRUCT, 2)
    this.ev.write(output)
    output.writeFieldEnd()
  }
  if (null != this.timeout) {
    output.writeFieldBegin('timeout', Thrift.Type.I32, 3)
    output.writeI32(this.timeout)
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_subscribe_result = function(args){
  this.success = null
if( args != null ){  if (null != args.success)
  this.success = args.success
}}
LiveConnect_subscribe_result.prototype = {}
LiveConnect_subscribe_result.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 0:      if (ftype == Thrift.Type.BOOL) {
        this.success = input.readBool()
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_subscribe_result.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_subscribe_result')
  if (null != this.success) {
    output.writeFieldBegin('success', Thrift.Type.BOOL, 0)
    output.writeBool(this.success)
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_unsubscribe_args = function(args){
  this.clientKey = null
  this.ev = null
if( args != null ){  if (null != args.clientKey)
  this.clientKey = args.clientKey
  if (null != args.ev)
  this.ev = args.ev
}}
LiveConnect_unsubscribe_args.prototype = {}
LiveConnect_unsubscribe_args.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 1:      if (ftype == Thrift.Type.STRING) {
        this.clientKey = input.readString()
      } else {
        input.skip(ftype)
      }
      break
      case 2:      if (ftype == Thrift.Type.STRUCT) {
        this.ev = new ttypes.EventDefinition()
        this.ev.read(input)
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_unsubscribe_args.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_unsubscribe_args')
  if (null != this.clientKey) {
    output.writeFieldBegin('clientKey', Thrift.Type.STRING, 1)
    output.writeString(this.clientKey)
    output.writeFieldEnd()
  }
  if (null != this.ev) {
    output.writeFieldBegin('ev', Thrift.Type.STRUCT, 2)
    this.ev.write(output)
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_unsubscribe_result = function(args){
  this.success = null
if( args != null ){  if (null != args.success)
  this.success = args.success
}}
LiveConnect_unsubscribe_result.prototype = {}
LiveConnect_unsubscribe_result.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 0:      if (ftype == Thrift.Type.BOOL) {
        this.success = input.readBool()
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_unsubscribe_result.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_unsubscribe_result')
  if (null != this.success) {
    output.writeFieldBegin('success', Thrift.Type.BOOL, 0)
    output.writeBool(this.success)
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_unsubscribeClient_args = function(args){
  this.clientKey = null
if( args != null ){  if (null != args.clientKey)
  this.clientKey = args.clientKey
}}
LiveConnect_unsubscribeClient_args.prototype = {}
LiveConnect_unsubscribeClient_args.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 1:      if (ftype == Thrift.Type.STRING) {
        this.clientKey = input.readString()
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_unsubscribeClient_args.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_unsubscribeClient_args')
  if (null != this.clientKey) {
    output.writeFieldBegin('clientKey', Thrift.Type.STRING, 1)
    output.writeString(this.clientKey)
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_unsubscribeClient_result = function(args){
  this.success = null
if( args != null ){  if (null != args.success)
  this.success = args.success
}}
LiveConnect_unsubscribeClient_result.prototype = {}
LiveConnect_unsubscribeClient_result.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 0:      if (ftype == Thrift.Type.BOOL) {
        this.success = input.readBool()
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_unsubscribeClient_result.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_unsubscribeClient_result')
  if (null != this.success) {
    output.writeFieldBegin('success', Thrift.Type.BOOL, 0)
    output.writeBool(this.success)
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_getSubscriptions_args = function(args){
}
LiveConnect_getSubscriptions_args.prototype = {}
LiveConnect_getSubscriptions_args.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_getSubscriptions_args.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_getSubscriptions_args')
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_getSubscriptions_result = function(args){
  this.success = null
if( args != null ){  if (null != args.success)
  this.success = args.success
}}
LiveConnect_getSubscriptions_result.prototype = {}
LiveConnect_getSubscriptions_result.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 0:      if (ftype == Thrift.Type.LIST) {
        {
          var _size39 = 0
          var rtmp3
          this.success = []
          var _etype42 = 0
          rtmp3 = input.readListBegin()
          _etype42 = rtmp3.etype
          _size39 = rtmp3.size
          for (var _i43 = 0; _i43 < _size39; ++_i43)
          {
            var elem44 = null
            elem44 = new ttypes.Subscription()
            elem44.read(input)
            this.success.push(elem44)
          }
          input.readListEnd()
        }
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_getSubscriptions_result.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_getSubscriptions_result')
  if (null != this.success) {
    output.writeFieldBegin('success', Thrift.Type.LIST, 0)
    {
      output.writeListBegin(Thrift.Type.STRUCT, this.success.length)
      {
        for(var iter45 in this.success)
        {
          if (this.success.hasOwnProperty(iter45))
          {
            iter45=this.success[iter45]
            iter45.write(output)
          }
        }
      }
      output.writeListEnd()
    }
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_getClients_args = function(args){
}
LiveConnect_getClients_args.prototype = {}
LiveConnect_getClients_args.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_getClients_args.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_getClients_args')
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnect_getClients_result = function(args){
  this.success = null
if( args != null ){  if (null != args.success)
  this.success = args.success
}}
LiveConnect_getClients_result.prototype = {}
LiveConnect_getClients_result.prototype.read = function(input){ 
  var ret = input.readStructBegin()
  while (1) 
  {
    var ret = input.readFieldBegin()
    var fname = ret.fname
    var ftype = ret.ftype
    var fid   = ret.fid
    if (ftype == Thrift.Type.STOP) 
      break
    switch(fid)
    {
      case 0:      if (ftype == Thrift.Type.LIST) {
        {
          var _size46 = 0
          var rtmp3
          this.success = []
          var _etype49 = 0
          rtmp3 = input.readListBegin()
          _etype49 = rtmp3.etype
          _size46 = rtmp3.size
          for (var _i50 = 0; _i50 < _size46; ++_i50)
          {
            var elem51 = null
            elem51 = new ttypes.ClientInfo()
            elem51.read(input)
            this.success.push(elem51)
          }
          input.readListEnd()
        }
      } else {
        input.skip(ftype)
      }
      break
      default:
        input.skip(ftype)
    }
    input.readFieldEnd()
  }
  input.readStructEnd()
  return
}

LiveConnect_getClients_result.prototype.write = function(output){ 
  output.writeStructBegin('LiveConnect_getClients_result')
  if (null != this.success) {
    output.writeFieldBegin('success', Thrift.Type.LIST, 0)
    {
      output.writeListBegin(Thrift.Type.STRUCT, this.success.length)
      {
        for(var iter52 in this.success)
        {
          if (this.success.hasOwnProperty(iter52))
          {
            iter52=this.success[iter52]
            iter52.write(output)
          }
        }
      }
      output.writeListEnd()
    }
    output.writeFieldEnd()
  }
  output.writeFieldStop()
  output.writeStructEnd()
  return
}

var LiveConnectClient = exports.Client = function(output, pClass) {
    this.output = output;
    this.pClass = pClass;
    this.seqid = 0;
    this._reqs = {}
}
LiveConnectClient.prototype = {}
LiveConnectClient.prototype.notify = function(user,table,op,oldData,nwData,callback){
  this.seqid += 1;
  this._reqs[this.seqid] = callback;
    this.send_notify(user, table, op, oldData, nwData)
}

LiveConnectClient.prototype.send_notify = function(user,table,op,oldData,nwData){
  var output = new this.pClass(this.output);
  output.writeMessageBegin('notify', Thrift.MessageType.CALL, this.seqid)
  var args = new LiveConnect_notify_args()
  args.user = user
  args.table = table
  args.op = op
  args.oldData = oldData
  args.nwData = nwData
  args.write(output)
  output.writeMessageEnd()
  return this.output.flush()
}
LiveConnectClient.prototype.subscribe = function(clientKey,ev,timeout,callback){
  this.seqid += 1;
  this._reqs[this.seqid] = callback;
    this.send_subscribe(clientKey, ev, timeout)
}

LiveConnectClient.prototype.send_subscribe = function(clientKey,ev,timeout){
  var output = new this.pClass(this.output);
  output.writeMessageBegin('subscribe', Thrift.MessageType.CALL, this.seqid)
  var args = new LiveConnect_subscribe_args()
  args.clientKey = clientKey
  args.ev = ev
  args.timeout = timeout
  args.write(output)
  output.writeMessageEnd()
  return this.output.flush()
}

LiveConnectClient.prototype.recv_subscribe = function(input,mtype,rseqid){
  var callback = this._reqs[rseqid] || function() {};
  delete this._reqs[rseqid];
  if (mtype == Thrift.MessageType.EXCEPTION) {
    var x = new Thrift.TApplicationException()
    x.read(input)
    input.readMessageEnd()
    return callback(x);
  }
  var result = new LiveConnect_subscribe_result()
  result.read(input)
  input.readMessageEnd()

  if (null != result.success ) {
    return callback(null, result.success);
  }
  return callback("subscribe failed: unknown result");
}
LiveConnectClient.prototype.unsubscribe = function(clientKey,ev,callback){
  this.seqid += 1;
  this._reqs[this.seqid] = callback;
    this.send_unsubscribe(clientKey, ev)
}

LiveConnectClient.prototype.send_unsubscribe = function(clientKey,ev){
  var output = new this.pClass(this.output);
  output.writeMessageBegin('unsubscribe', Thrift.MessageType.CALL, this.seqid)
  var args = new LiveConnect_unsubscribe_args()
  args.clientKey = clientKey
  args.ev = ev
  args.write(output)
  output.writeMessageEnd()
  return this.output.flush()
}

LiveConnectClient.prototype.recv_unsubscribe = function(input,mtype,rseqid){
  var callback = this._reqs[rseqid] || function() {};
  delete this._reqs[rseqid];
  if (mtype == Thrift.MessageType.EXCEPTION) {
    var x = new Thrift.TApplicationException()
    x.read(input)
    input.readMessageEnd()
    return callback(x);
  }
  var result = new LiveConnect_unsubscribe_result()
  result.read(input)
  input.readMessageEnd()

  if (null != result.success ) {
    return callback(null, result.success);
  }
  return callback("unsubscribe failed: unknown result");
}
LiveConnectClient.prototype.unsubscribeClient = function(clientKey,callback){
  this.seqid += 1;
  this._reqs[this.seqid] = callback;
    this.send_unsubscribeClient(clientKey)
}

LiveConnectClient.prototype.send_unsubscribeClient = function(clientKey){
  var output = new this.pClass(this.output);
  output.writeMessageBegin('unsubscribeClient', Thrift.MessageType.CALL, this.seqid)
  var args = new LiveConnect_unsubscribeClient_args()
  args.clientKey = clientKey
  args.write(output)
  output.writeMessageEnd()
  return this.output.flush()
}

LiveConnectClient.prototype.recv_unsubscribeClient = function(input,mtype,rseqid){
  var callback = this._reqs[rseqid] || function() {};
  delete this._reqs[rseqid];
  if (mtype == Thrift.MessageType.EXCEPTION) {
    var x = new Thrift.TApplicationException()
    x.read(input)
    input.readMessageEnd()
    return callback(x);
  }
  var result = new LiveConnect_unsubscribeClient_result()
  result.read(input)
  input.readMessageEnd()

  if (null != result.success ) {
    return callback(null, result.success);
  }
  return callback("unsubscribeClient failed: unknown result");
}
LiveConnectClient.prototype.getSubscriptions = function(callback){
  this.seqid += 1;
  this._reqs[this.seqid] = callback;
    this.send_getSubscriptions()
}

LiveConnectClient.prototype.send_getSubscriptions = function(){
  var output = new this.pClass(this.output);
  output.writeMessageBegin('getSubscriptions', Thrift.MessageType.CALL, this.seqid)
  var args = new LiveConnect_getSubscriptions_args()
  args.write(output)
  output.writeMessageEnd()
  return this.output.flush()
}

LiveConnectClient.prototype.recv_getSubscriptions = function(input,mtype,rseqid){
  var callback = this._reqs[rseqid] || function() {};
  delete this._reqs[rseqid];
  if (mtype == Thrift.MessageType.EXCEPTION) {
    var x = new Thrift.TApplicationException()
    x.read(input)
    input.readMessageEnd()
    return callback(x);
  }
  var result = new LiveConnect_getSubscriptions_result()
  result.read(input)
  input.readMessageEnd()

  if (null != result.success ) {
    return callback(null, result.success);
  }
  return callback("getSubscriptions failed: unknown result");
}
LiveConnectClient.prototype.getClients = function(callback){
  this.seqid += 1;
  this._reqs[this.seqid] = callback;
    this.send_getClients()
}

LiveConnectClient.prototype.send_getClients = function(){
  var output = new this.pClass(this.output);
  output.writeMessageBegin('getClients', Thrift.MessageType.CALL, this.seqid)
  var args = new LiveConnect_getClients_args()
  args.write(output)
  output.writeMessageEnd()
  return this.output.flush()
}

LiveConnectClient.prototype.recv_getClients = function(input,mtype,rseqid){
  var callback = this._reqs[rseqid] || function() {};
  delete this._reqs[rseqid];
  if (mtype == Thrift.MessageType.EXCEPTION) {
    var x = new Thrift.TApplicationException()
    x.read(input)
    input.readMessageEnd()
    return callback(x);
  }
  var result = new LiveConnect_getClients_result()
  result.read(input)
  input.readMessageEnd()

  if (null != result.success ) {
    return callback(null, result.success);
  }
  return callback("getClients failed: unknown result");
}
var LiveConnectProcessor = exports.Processor = function(handler) {
  this._handler = handler
}
LiveConnectProcessor.prototype.process = function(input, output) {
  var r = input.readMessageBegin()
  if (this['process_' + r.fname]) {
    return this['process_' + r.fname].call(this, r.rseqid, input, output)
  } else {
    input.skip(Thrift.Type.STRUCT)
    input.readMessageEnd()
    var x = new Thrift.TApplicationException(Thrift.TApplicationExceptionType.UNKNOWN_METHOD, 'Unknown function ' + r.fname)
    output.writeMessageBegin(r.fname, Thrift.MessageType.Exception, r.rseqid)
    x.write(output)
    output.writeMessageEnd()
    output.flush()
  }
}

LiveConnectProcessor.prototype.process_notify = function(seqid, input, output) {
  var args = new LiveConnect_notify_args()
  args.read(input)
  input.readMessageEnd()
  this._handler.notify(args.user, args.table, args.op, args.oldData, args.nwData)
}

LiveConnectProcessor.prototype.process_subscribe = function(seqid, input, output) {
  var args = new LiveConnect_subscribe_args()
  args.read(input)
  input.readMessageEnd()
  var result = new LiveConnect_subscribe_result()
  this._handler.subscribe(args.clientKey, args.ev, args.timeout, function(success) {
    result.success = success
    output.writeMessageBegin("subscribe", Thrift.MessageType.REPLY, seqid)
    result.write(output)
    output.writeMessageEnd()
    output.flush()
  })
}

LiveConnectProcessor.prototype.process_unsubscribe = function(seqid, input, output) {
  var args = new LiveConnect_unsubscribe_args()
  args.read(input)
  input.readMessageEnd()
  var result = new LiveConnect_unsubscribe_result()
  this._handler.unsubscribe(args.clientKey, args.ev, function(success) {
    result.success = success
    output.writeMessageBegin("unsubscribe", Thrift.MessageType.REPLY, seqid)
    result.write(output)
    output.writeMessageEnd()
    output.flush()
  })
}

LiveConnectProcessor.prototype.process_unsubscribeClient = function(seqid, input, output) {
  var args = new LiveConnect_unsubscribeClient_args()
  args.read(input)
  input.readMessageEnd()
  var result = new LiveConnect_unsubscribeClient_result()
  this._handler.unsubscribeClient(args.clientKey, function(success) {
    result.success = success
    output.writeMessageBegin("unsubscribeClient", Thrift.MessageType.REPLY, seqid)
    result.write(output)
    output.writeMessageEnd()
    output.flush()
  })
}

LiveConnectProcessor.prototype.process_getSubscriptions = function(seqid, input, output) {
  var args = new LiveConnect_getSubscriptions_args()
  args.read(input)
  input.readMessageEnd()
  var result = new LiveConnect_getSubscriptions_result()
  this._handler.getSubscriptions(function(success) {
    result.success = success
    output.writeMessageBegin("getSubscriptions", Thrift.MessageType.REPLY, seqid)
    result.write(output)
    output.writeMessageEnd()
    output.flush()
  })
}

LiveConnectProcessor.prototype.process_getClients = function(seqid, input, output) {
  var args = new LiveConnect_getClients_args()
  args.read(input)
  input.readMessageEnd()
  var result = new LiveConnect_getClients_result()
  this._handler.getClients(function(success) {
    result.success = success
    output.writeMessageBegin("getClients", Thrift.MessageType.REPLY, seqid)
    result.write(output)
    output.writeMessageEnd()
    output.flush()
  })
}
