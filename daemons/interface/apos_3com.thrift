/**
* Communication with 3com switches
*/

include "apos.thrift"
namespace php APos

service APos_3com extends apos.APos {
	/**
	* Display section
	*/
	string display(1: string path),
}