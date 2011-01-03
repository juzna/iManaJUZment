/**
* Diagnostic funtions
*/

include "common.thrift"

/**
* Modes of ping
*/
enum PingMode {
  icmp = 1,
  arp = 2,
  udp = 3,
}

/**
* Ping response
*/
struct PingResponse {
  1: common.APID ap,
  2: i32 count,		// Count of pings
  3: list<i64> times,	// Ping times in microseconds
  4: byte packetLoss,	// In percents
  5: i64 timeMin,
  6: i64 timeAvg,
  7: i64 timeMax,
  8: PingMode mode,
}

/**
* What kind of Traceroute to do
*/
enum TracerouteMode {
  icmp = 1,
  tcp = 2,
  udp = 3,
}


/**
* Entry in traceroute list
*/
struct TracerouteEntry {
  1: i32 position,
  2: common.ipAddress ip,
  3: i64 time,		// in microseconds
  4: TracerouteMode mode,
}

/**
* Response of traceroute command
*/
struct TracerouteResponse {
  1: common.APID ap,
  2: i32 length,		// Length of path
  3: list<TracerouteEntry> responses,
}


service Diagnostics {
  /**
  * Do ping - ICMP echo request/reply test
  * @param IP ipAddress IP address to be pinged
  * @param i32 timeout in miliseconds
  * @param i32 count Number of pings
  * @param i16 size Size of payload
  */
  PingResponse ping(1: common.ipAddress ip, 2: i32 timeout, 3: i32 count = 5, 4: i16 size = 20, 5: PingMode mode = icmp),
  
  /**
  * Multiple simultaneous pings
  */
  map<common.ipAddress,PingResponse> multiPing(1: list<common.ipAddress> ipList, 2: i32 timeout, 3: i32 count = 5, 4: i16 size = 20, 5: PingMode mode = icmp),
  
  /**
  * Do traceroute
  */
  TracerouteResponse traceroute(1: common.ipAddress ip, 2: i32 timeout, 3: i32 count = 3, 4: TracerouteMode type = icmp),
}
