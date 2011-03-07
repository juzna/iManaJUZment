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

namespace Juz\Tables\DataSource;

class DoctrineRepositorySource extends ExtendableArraySource {
  /** @var \Doctrine\ORM\EntityRepository */
  protected $repository;

  /** @var array Filter parameters */
  protected $filter = array();

  /**
   * Creates new datasource from Doctrine's repository, allows filtering
   *
   * @param \Doctrine\ORM\EntityRepository $repo Repository
   * @param array $filter Filter parameters
   */
  public function __construct(\Doctrine\ORM\EntityRepository $repo, $filter = null) {
    $this->repository = $repo;
    if(is_array($filter)) $this->filter += $filter;
  }

  /**
   * Create datasource from entitymanager and entity's name
   * @param \Doctrine\ORM\EntityManager $em
   * @param string $entityName
   * @param array $filter
   * @return DoctrineRepositorySource
   */
  public static function create($em, $entityName, $filter = null) {
    if(!isset($em)) $em = \Nette\Environment::getService('Doctrine\\ORM\\EntityManager'); // Find default EM if not given
    return new self($em->getRepository($entityName), $filter);
  }

  public function addContidion($key, $val) {
    $this->filter[$key] = $val;
  }

  /**
   * Executes query and gets array of results
   * (needed by Extendable array data source)
   * @return array Final array
   */
  protected function _initialize() {
    return $this->filter ? $this->repository->findBy($this->filter) : $this->repository->findAll();
  }
}
