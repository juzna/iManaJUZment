/**
 * LiveConnect - allow to send events and changes directly to subscribed clients (browsers)
 */
namespace php LiveConnect


/**
 * User info
 */
typedef i32 User;


/**
 * What operation happened?
 */
enum LiveConnectOp {
 add = 1,
 edit = 2,
 delete = 3,
 clone = 4,
 all = 255,
}

/**
 * What changes happened?
 */
typedef map<string,string> LiveConnectData;


/**
 * Operation for a condition
 */
enum LiveConnectConditionOp {
  present = 1,
  eq = 2,
  lt = 3,
  lte = 4,
  gt = 5,
  gte = 6,
  in = 7,
}

/**
 * Conditions for subscriptions
 */
struct Condition {
  1: required string column,
  2: required LiveConnectConditionOp operation,
  3: optional string value
}

/**
 * Definition of events we want to subscribe for
 */
struct EventDefinition {
  1: required string table,
  2: required set<LiveConnectOp> operations,
  3: optional set<string> columns,
  4: required set<Condition> conditions,
}

struct Subscription {
  1: required string clientKey,
  2: required EventDefinition ev,
  3: optional i32 expiry,
  4: optional i32 renewals,
  5: optional i32 eventReceived,
}


Service LiveConnect {
  /**
   * Notification from system that a change happened
   */
  oneway void notify(1: User user, 2: string table, 3: LiveConnectOp op, 4: LiveConnectData oldData, 5: LiveConnectData nwData)

  /**
   * Subscribe client for particular events
   */
  bool subscribe(1: string clientKey, 2: EventDefinition ev, 3: i32 timeout)

  /**
   * Unsubscribe existing event
   */
  bool unsubscribe(1: string clientKey, 2: EventDefinition ev)

  /**
   * Unsubscribe all events of single client
   */
  bool unsubscribeClient(1: string clientKey)

  /**
   * Get list of all subscriptions
   */
  list<Subscription> getSubscriptions()
}
