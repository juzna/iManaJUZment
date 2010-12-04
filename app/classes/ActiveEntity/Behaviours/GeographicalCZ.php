<?php

namespace ActiveEntity\Behaviours;

class GeographicalCZ extends BaseBehaviour{
  public static function setDefinition($className, $args) {
    self::hasColumn('posX', 'integer', 11, array());
    self::hasColumn('posY', 'integer', 11, array());

    // Postal address
    self::hasColumn('ulice', 'string', 100, array());
    self::hasColumn('cisloPopisne', 'string', 100, array());
    self::hasColumn('mesto', 'string', 100, array());
    self::hasColumn('PSC', 'string', 10, array());
    self::hasColumn('stat', 'string', 10, array());

    self::hasColumn('uir_obec', 'integer', 11, array());
    self::hasColumn('uir_cobce', 'integer', 11, array());
    self::hasColumn('uir_ulice', 'integer', 11, array());
    self::hasColumn('uir_objekt', 'integer', 11, array());
    self::hasColumn('uir_special', 'boolean', null, array()); // Specialni adresa (neni z UIR)
  }
}

