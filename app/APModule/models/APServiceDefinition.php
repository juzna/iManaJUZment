<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APServiceList
 *
 * @Table @Entity
 */
class APServiceDefinition extends \ActiveEntity\Entity
{
  /**
   * @var string $code
   * @Column(name="code", type="string") @Id @GeneratedValue
   */
  protected $code;

  /**
   * @var string $nazev
   * @Column(name="nazev", type="string", length=50, nullable=false)
   */
  protected $nazev;

  /**
   * @var string $popis
   * @Column(name="popis", type="string", length=255, nullable=true)
   */
  protected $popis;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}