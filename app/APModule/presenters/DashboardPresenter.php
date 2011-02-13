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


namespace APModule;

use Nette\Application\AppForm,
	Nette\Forms\Form,
  Nette\Forms,
  DoctrineForm,
  ActiveEntity\Entity;


class DashboardPresenter extends \DashboardPresenter {
  // List of entity aliases (for listing, adding, editing, removing)
  protected $entityAliases = array(
    'ap'    => 'AP',
    'ip'    => 'APIP',
    'swif'  => 'APSwIf',
    'port'  => 'APPort',
    'antenna' => 'APAntenna',
    'coverage' => 'APCoverage',
    'coverageSubnet' => 'APCoverageSubnet',
    'route' => 'APRoute',
  );

  /**
   * Renders Access point's detail page
   * @param int $id ID of access point
   */
  public function renderDetail($id) {
    $this->template->AP = \AP::find($id);
    $this->template->Tags =\APTag::getRepository()->findAll();
  }

  /**
   * Detail of coverage
   * @param int $id ID of coverage
   */
  public function renderCoverageDetail($id) {
    $this->template->Coverage = $cov = \APCoverage::find($id);
    $this->template->AP = $cov->AP;
  }

  /**
   * Adds or removes tag from an AP
   * @param int $apId AP id
   * @param int $tagId Tag id
   * @param string $what What action to do: add, delete, (remove)
   * @return void
   */
  public function handleSetTag($apId, $tagId, $what) {
    /** @var AP */
    $ap = \AP::find($apId);
    $tag = \APTag::find($tagId);

    // Do it
    if($what == 'add') $ap->Tags->add($tag);
    elseif($what == 'delete' || $what == 'remove') $ap->Tags->removeElement($tag);
    else throw new \InvalidArgumentException("Wrong action - what parameter");
    
    $ap->flush();
    $this->invalidateControl('tags');
  }
}  