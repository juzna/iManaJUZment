<?php
/**
* Zprostredkovani komunikace systemu s Access Pointy
*/


/**
* Access Point Operating System
*/
abstract class APos {
  /**
   * Wrapper for connecting to an APos
   * @param AP|int $ap
   * @return \Thrift\APos\APosIf
   */
  static function connect($ap, $connector = null) {
    if(is_numeric($ap)) {
      $id = $ap;
      if(!$ap = \AP::find($id)) throw new \NotFoundException("AP not found");
    }
    elseif($ap instanceof \AP) {
      // It's OK
    }
    else throw new \InvalidArgumentException("Parameter expected to be an AP");

    return \APos\Connector\Factory::connect($ap, $connector);
  }
}
