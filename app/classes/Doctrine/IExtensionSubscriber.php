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

namespace Juz;

/**
 * Extension can add methods dynamically
 * For more info see ClassMetadata
 * @see ClassMetadata
 *
 * @author Jan Dolecek - juzna.cz
 *
 */
interface IExtensionSubscriber {
  /**
   * Gets a callback for named method
   * @param string $method
   * @return callback
   */
  public function loader($method);
}
