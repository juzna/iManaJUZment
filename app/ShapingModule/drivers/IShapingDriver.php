<?php

namespace ShapingModule\Drivers;

interface IShapingDriver {
  /**
  * Get interface of APos, which is needed to implements this queues
  * @return string
  */
  function getRequiredAPosInterfaceName();

  /**
  * Store connected APos driver
  */
  function setAPOsHandler(\Thrift\APos\APosIf $apos);

  /**
   * Add list of queues
   */
  function addQueues(array $list);

  /**
   * Preview commands to be sent
   */
  function preview($sync = false);

  /**
   * Synchronize it
   */
  function commit();
}



