<?php

namespace ActiveEntity;

/**
 * Extended metadata for ActiveEntity's
 */
class ClassMetadata extends \Doctrine\ORM\Mapping\ClassMetadata {
  public $title;
  public $titles;
  public $listable = false;
  public $editable = false;
  public $notFoundAction;
  public $notFoundParams;
}

