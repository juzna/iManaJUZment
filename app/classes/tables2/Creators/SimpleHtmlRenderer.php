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
use Juz\Tables\ITableStructureDefinition,
  Juz\Tables\IDataSource;

/**
 * Renders table to HTML
 */
class SimpleHtmlRenderer extends BaseRenderer implements \Juz\Tables\ITableRenderer {
  /**
   * Renders table to standard output
   * @return void
   */
  function render() {
    echo "<table id=\"" . $this->definition->getId() . "\">\n";

    
    // Table header
    echo "<thead>\n";
    $this->renderHead();
    echo "</thead>\n";

    // Table body
    echo "<tbody>\n";
    $this->renderBody();
    echo "</tbody>\n";

    // Table footer
    echo "<tfoot>\n";
    $this->renderFooter();
    echo "</tfoot>\n";


    echo "</table>\n";
  }

  protected function renderHead() {
    echo "\t<tr>\n";
    foreach($this->definition->getFields() as $field) {
      echo "\t\t<th>$field->name</th>\n";
    }
    echo "\t<tr>\n";
  }

  protected function renderBody() {
    foreach($this->datasource as $item) {
      echo "\t<tr>\n";
      foreach($this->definition->getFields() as $field) {
        echo "\t\t<td>";
        $this->renderFieldContent($item, $field);
        echo "</td>\n";
      }
      echo "\t<tr>\n";
    }
  }

  protected function renderFieldContent($row, \Juz\Tables\Field $field) {
    if($var = $field->variable) {
      echo $this->getFieldValue($row, $var);
    }
    elseif($content = $field->content) {
      // Callback function for reg exp
      $renderer = $this;
      $cb = function($match) use ($row, $renderer) {
        return $renderer->getFieldValue($row, $match[1]);
      };

      echo preg_replace_callback('/\\{\\$([^}]+)\\}/', $cb, $content);
    }
  }

  protected function renderFooter() {
    $numCols = count($this->definition->getFields());
    $numRows = count($this->datasource);

    echo "\t<tr>\n\t\t<td colspan=\"" . $numCols . "\">Items: " . $numRows . "</td>\n\t</tr>\n";
  }
}
