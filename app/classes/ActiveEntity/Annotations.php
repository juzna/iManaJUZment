<?php
/**
 * Annotations specific for ActiveEntity
 */
 
namespace ActiveEntity\Annotations;
use \Doctrine\Common\Annotations\Annotation;

class Title extends Annotation {
  public $list;
  public $add;
  public $edit;
}
class Listable extends Annotation {}
class Editable extends Annotation {}
class NotFound extends Annotation {
  /** @var string What to do when entity is not found: exception */
  public $action;
  
  /** @var string Class name of thrown exception */
  public $exceptionClassName;
}

class Set extends Annotation {}
class Get extends Annotation {}
class Format extends Annotation {}
class Show extends Annotation {}

