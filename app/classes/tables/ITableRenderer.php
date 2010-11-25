<?php

namespace Tables;


interface ITableRenderer {
  public function getDefinition();
  public function setDefinition(ITableDefinition $def);
  
  public function getDataSource();
  public function setDataSource(\Traversable $source);
  
  public function getVariablesNeeded();
  
  public function getVariables();
  public function setVariables(array $arr);
  
  public function render($offset = 0, $limit = 0);
}
