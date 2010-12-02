<?php

namespace ActiveEntity\Behaviours;

class InetSpeed extends Basebehaviour {
  public static function setDefinition() {
  	// Inet speeds
  	$fields = array('min', 'max', 'burst', 'tresh', 'time');
  	$types = array('tx', 'rx');
  	foreach($fields as $field) {
  	  foreach($types as $type) self::hasColumn($type . $field, 'string', 20, array());
  	}
  	
  	self::hasColumn('txpriority', 'integer', 2, array());
  	self::hasColumn('rxpriority', 'integer', 2, array());
  }
}

