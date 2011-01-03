include "common.thrift"
namespace php APos

/**
* State of MAC address entry in switch
*/
enum MacAddressEntryState {
  unknown = 0,
  learned = 1,
  configured = 2,
  other = 255
}

/**
* MAC address entry in 'display mac-address'
*/
struct MacAddressEntry {
  1: common.APID ap,
  2: string mac,
  3: string ifName,
  4: i16 vlan,
  5: MacAddressEntryState state
}

/**
* List of MAC address entries
*/
typedef list<MacAddressEntry> MacAddressList


/**
* ARP entry
*/
struct ArpEntry {
  1: common.APID ap,
  2: string ip,
  3: string mac,
  4: i16 vlan,
  5: string ifName
  6: bool isStatic,
  7: bool isActive
}

/**
* List of ARP entries
*/
typedef list<ArpEntry> ArpList


/**
* VLAN entry
*/
struct VlanEntry {
  1: common.APID ap,
  2: i16 vlan,
  3: string description
}
typedef list<VlanEntry> VlanList

/**
* Port link-type
*/
enum LinkType {
  access = 1,
  trunk = 2,
  hybrid = 3,
}

/**
* Mapping VLAN to L2 ifName
*/
struct VlanPortEntry {
  1: common.APID ap,
  2: i16 vlan,
  3: string port,
  4: LinkType linkType,
  5: bool isTagged,
  6: bool isPvid
}

/**
* Route entry
*/
struct RouteEntry {
  1: common.APID ap,
  2: common.ipAddress destination,
  3: byte netmask,
  4: common.ipAddress gateway,
  5: i16 preference,
  6: i16 cost,
  7: string mac, // Mac address of gateway
  8: string ifName, // ifName, thru which is gateway accessible
  9: bool isStatic, // Is it static configured or dynamically adedd?
  10: bool isActive
}
typedef list<RouteEntry> RouteList


/**
* IP address entry
*/
struct IpAddressEntry {
  1: common.APID ap,
  2: common.ipAddress ip,
  3: byte netmask,
  4: string ifName,
  5: bool isEnabled,	// Admin enabled?
}
typedef list<IpAddressEntry> IpAddressList


/**
* Network ifName types
*/
enum NetIfType {
  other = 0, // Unknown
  ether = 1, // Ethernet
  wlan = 2, // Wireless
  vlan = 3, // Virtual LAN
  eoip = 4, // Ethernet over IP
  ppp = 5,  // VPN
}

/**
* Band for wireless info
*/
enum WirelessBand {
  other = 0,	// Unknown
  b24 = 1,	// 2.4GHz
  b5 = 2,	// 5GHz
  b3 = 3,	// 3GHz (licenced)
}

/**
* Wireless encryption types
*/
enum WifiEncryptionType {
  none = 0,
  wep = 1,
  wpa = 2,
  unknown = 255,	// unknown
}

/**
* Wireless encryption parameters
*/
struct WifiEncryptionInfo {
  1: required WifiEncryptionType type,
  2: optional string passphrase,
  3: optional list<string> keys
}

/**
* Wireless ifName info
*/
struct WirelessIfInfo {
  1: string bssid, // Base SSID (mac address)
  2: string essid, // Extended SSID (user defined string)
  3: WirelessBand band,
  4: i32 frequency, // Frequency in MHz
  5: WifiEncryptionInfo encryption
}

struct VlanIfInfo {
  1: i16 vlan,
  2: bool isTagged,
}

/**
* Network interface
*/
struct NetInterfaceEntry {
  1: common.APID ap,
  2: string ifName,
  3: NetIfType type,
  4: i16 mtu, // Maximal transfer unit
  5: WirelessIfInfo wireless,
  6: VlanIfInfo vlan,
  7: bool isEnabled,	// Admin enabled?
  8: bool isActive,	// Active, i.e. it blinks
}
typedef list<NetInterfaceEntry> NetInterfaceList


/**
* Physical port entry
*/
struct PortEntry {
  1: common.APID ap,
  2: string port,	// Port name
  3: LinkType linkType,
  4: bool isEnabled,	// Admin enabled?
  5: bool isActive,	// Active, i.e. it blinks
  6: string description,
}
typedef list<PortEntry> PortList


/**
* Wireless registration table entry
*/
struct RegistrationTableEntry {
  1: common.APID ap,
  2: string ifName,
  3: string mac,		// Client mac address
  4: string radioName,		// Client's name
  5: i32 uptime,		// Uptime in seconds
  6: i16 snr,			// Signal to noise ratio, in dB
  7: common.ipAddress lastIP,		// Last visible IP address
}
typedef list<RegistrationTableEntry> RegistrationTableList


/**
* Operating system info
*/
struct OSInfo {
  1: common.APID ap,
  2: string name,
  3: byte vMajor,	// Major version
  4: byte vMinor,	// Minor version
  5: string version,
  6: i32 uptime,	// Uptime in seconds
  7: common.ipAddress ip,	// Default IP address
  255: map<string, string> moreInfo	// More parameters
}
