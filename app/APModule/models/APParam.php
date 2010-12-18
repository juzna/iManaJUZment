<?php


use Doctrine\Common\Collections\ArrayCollection;

/**
 * APParam
 *
 * @Table @Entity
 */
class APParams extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var AP
   * @ManyToOne(targetEntity="AP", inversedBy="Params")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $name
   * @Column(name="name", type="string", length=50, nullable=true)
   */
  protected $name;

  /**
   * @var string $value
   * @Column(name="value", type="string", length=50, nullable=true)
   */
  protected $value;

  /**
   * @var string $comment
   * @Column(name="comment", type="string", length=255, nullable=true)
   */
  protected $comment;

  /**
   * 
   */
  public function __construct()
  {
    parent::__construct();
  
  }
}