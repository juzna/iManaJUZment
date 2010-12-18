<?php
/**
 * Annotations specific for ActiveEntity
 */
 
namespace ActiveEntity\Annotations;
use \Doctrine\Common\Annotations\Annotation;

class Module extends Annotation {}
class Title extends Annotation {
  public $list;
  public $single;
  public $add;
  public $edit;
}
class Description extends Annotation {}
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
class Behaviour extends Annotation {
  public $name;
  public $params;
}
class Link extends Annotation {
  public $title;
  public $module;
  public $presenter;
  public $view;
  public $action;
  public $params;
}
class HeaderLink extends Link {}

class Links extends Annotation {
  public $module;
  public $presenter;
  public $view;
  public $action;
  public $params;
  public $alias;
  public $common;
}

class Immutable extends Annotation {}
class Required extends Annotation {}