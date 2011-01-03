/**
* Communication with Nagios
*/
namespace php Nagios
include "common.thrift"

/**
* Services to be checked
*/
enum CheckService {
	ping = 1,
	ssh = 2,
	http = 3,
	telnet = 4,
}

/**
* Host entry for config
*/
struct HostEntry {
  1: required string hostName,
  2: optional string hostAlias,
  3: required common.ipAddress ip,
  4: required string contactGroup,
  5: string template = "generic-host",
  6: optional string image,		// Path to image
  7: common.coordinates coords,	// Coordinates for map
  8: string url,		// Action URL
  9: list<string> groups = [ 'default' ],	// List of groups
  10: list<CheckService> services = [ ping ],	// List of services to be checked
  11: required list<string> parents,		// List of parents
}

/**
* Whole configuration
*/
struct Configuration {
  1: required list<HostEntry> hosts,		// List of hosts
  2: map<string,string> groupAliases,	// Group aliases
}


service Nagios {
	/**
	* Set new configuration
	*/
	oneway void updateConfiguration(1: Configuration conf),
	
	/**
	* Reload config
	*/
	oneway void reloadConfig(),
	
	oneway void start(),
	oneway void stop(),
	oneway void restart(),
	
	/**
	* Get statistics
	*/
	string getStatistics(),
}



/**
* Nagios event sent from Nagios to IS
*/
struct Event {
  1: string hostName,
  2: common.ipAddress hostIp,
  3: string newState
}

service Listener {
	/**
	* Process event by nagios
	*/
	oneway void processEvent(1: Event ev),
}