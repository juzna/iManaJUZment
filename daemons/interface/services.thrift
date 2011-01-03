include "common.thrift"
namespace php APos

/**
* State of service on AP
*/
struct ServiceState {
  1: common.APID ap,
  2: string serviceName,
  3: bool isRunning,
  4: string state,
  5: optional map<string,string> moreInfo,
}

/**
* Description of service
*/
struct ServiceDescriptor {
  1: common.APID ap,
  2: string serviceName,
  3: string description,
}


/**
* Services for Access Points
*/
service Services {
	/**
	* Check service state
	*/
	ServiceState checkService(1: string serviceName),
	
	/**
	* Activate service
	*/
	oneway void activateService(1: string serviceName),
	
	/**
	* Deactivate service
	*/
	oneway void deactivateService(1: string serviceName),
	
	/**
	* Get list of available services
	*/
	list<ServiceDescriptor> getAvailableServices(),
	
	/**
	* Check, if service is supported
	*/
	bool isSupported(1: string serviceName),
	
	/**
	* Check all services
	*/
	map<string,ServiceState> checkAllServices(),
}