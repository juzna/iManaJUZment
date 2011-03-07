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

namespace Juz\Tables\Creator;

/**
 * Base class for simple definition of renderers
 */
abstract class BaseRenderer extends BaseCreator implements \Juz\Tables\ITableRenderer {
  /**
   * Renders table and returns it as string
   * @return void
   */
  function toString() {
    ob_start();

    try {
      $this->render();
      return ob_get_clean();
    }
    catch(\Exception $e) {
      ob_end_clean();
      throw $e;
    }
  }
}
