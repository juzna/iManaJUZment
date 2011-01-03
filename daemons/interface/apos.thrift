/**
* AP types
*/
include "ap_types.thrift"
include "services.thrift"
namespace php APos

service APos extends services.Services {
	/**
	* Test connection with AP
	*/
	bool testConnection(),
	
	/**
	* Get system name
	*/
	string getSysName(),
	
	/**
	* Get uptime in seconds
	*/
	i32 getUptime(),
	
	/**
	* Get basic system info
	*/
	ap_types.OSInfo getSysInfo(),
	
	/**
	* Get list of MAC addresses
	* @param i16 vlan Filter mac addresses for this vlan
	* @param string ifName Filter mac addresses for this ifName
	*/
	ap_types.MacAddressList getMacList(1: i16 vlan, 2: string ifName),
	
	/**
	* Get list of ARP entries
	* @param i16 vlan Filter mac addresses for this vlan
	* @param string ifName Filter mac addresses for this ifName
	*/
	ap_types.ArpList getArpList(1: i16 vlan, 2: string ifName),
	
	/**
	* Get list of configured VLANs
	*/
	ap_types.VlanList getVlanList(),
	
	/**
	* Get mapping of VLAN's to physical ports
	*/
	ap_types.VlanPortEntry getVlanPortList(),
	
	/**
	* Get configured routes
	*/
	ap_types.RouteList getRouteList(1: bool allowDynamic = 1),
	
	/**
	* Get list of IP addresses
	*/
	ap_types.IpAddressList getIPList(),
	
	/**
	* Get list of network interfaces
	*/
	ap_types.NetInterfaceList getInterfaceList(),
	
	/**
	* Get list of physical ports
	*/
	ap_types.PortList getPortList(),
	
	/**
	* Get registration table statistics
	*/
	ap_types.RegistrationTableList getRegistrationTable()
	
	/**
	* Execute command
	*/
	string execute(1: string cmd),
	
	/**
	* Execute multiple commands in sequence
	*/
	list<string> executeList(1: list<string> cmdList),
}

/**
* Spawner of processes
*/
service Spawner {
	/**
	* Spawn new APos server
	*/
	bool spawn(1: i32 apid),
}