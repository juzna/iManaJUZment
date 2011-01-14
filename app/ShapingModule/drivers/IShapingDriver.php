<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */


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



