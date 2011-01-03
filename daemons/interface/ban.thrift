include "common.thrift"

/**
* User to be blocked
*/
struct BlockUser {
  1: common.ipAddress ip,
  2: string mac
}

/**
* Type of bans
*/
enum BlockType {
  permament = 1,	// Cannot be removed by customer
  soft = 2,		// CAN be remove by customer
}

/**
* Ban entry
*/
struct BanEntry {
  1: common.APID ap,
  2: BlockUser user,
  3: BlockType type
}
typedef list<BanEntry> BanList


/**
* RPC for banning users
*/
service Blocking {
	/**
	* Block user
	*/
	oneway void block(1: BlockUser user, 2: BlockType type),
	
	/**
	* Unblock user
	*/
	oneway void unblock(1: BlockUser user),
	
	/**
	* Ensure that given user is banned
	*/
	oneway void ensureBanned(1: BlockUser user, 2: BlockType type),
	
	/**
	* Check whether user is banned
	* @return 0 if not banned
	*/
	BlockType isBanned(1: BlockUser user),
	
	/**
	* Get existing bans
	*/
	BanList getBanList(),
	
	/**
	* Add multiple bans at one
	*/
	oneway void blockMultiple(1: BanList banList),
}