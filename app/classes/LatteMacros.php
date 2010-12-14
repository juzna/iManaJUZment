<?php

class LatteMacros extends \Nette\Templates\LatteMacros {
  public function __construct() {
    parent::__construct();

    $this->macros['table'] = "<?php %:macroTable% ?>";
  }

  public function macroTable($content, $modifiers) {
    $tableName = $this->fetchToken($content);
    $ds = $this->fetchToken($content);
    
    return '$presenter->drawTable(' . $this->formatString($tableName) . ', ' . $ds . $this->formatArray($content, ', ') . ')';
  }



}
