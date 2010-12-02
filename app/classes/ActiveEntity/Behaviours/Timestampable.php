<?php

namespace ActiveEntity\Behaviours;

class Timestampable extends BaseBehaviour{
  public static function setDefinition() {
    self::hasColumn('created', 'timestamp');
    self::hasColumn('updated', 'timestamp');
  }
  
  // TODO: hooks
  
}

