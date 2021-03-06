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
class Show extends Annotation {
  public $helper;
}
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
  public $class;
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
class Group extends Annotation {}
class Name extends Annotation {}
class DefaultValue extends Annotation {}
class EnumValues extends Annotation {}
