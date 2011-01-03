/**
* Communication with Mikrotik
*/

include "apos.thrift"
namespace php APos

/**
* Result of command thru API
*/
struct APIResult {
	1: list<map<string,string>> lst,
	2: string ret,
}

/**
* Command to be executed via API
*/
struct APICommand {
	1: required string path,
	2: required string command,
	3: required map<string,string> params,
}
typedef list<APICommand> APICommandList
typedef map<string,APICommand> APICommandHash


service Mk extends apos.APos {
	/**
	* Export section
	*/
	list<map<string,string>> export(1: string path),
	
	/**
	* Get all command
	*/
	list<map<string,string>> getAll(1: string path),
	
	/**
	* Execute API command
	*/
	APIResult executeAPI(1: string path, 2: string command, 3: map<string,string> params),
	
	/**
	* Execute multiple commands
	*/
	map<string,APIResult> executeAPIMulti(1: APICommandHash cmdList),
}