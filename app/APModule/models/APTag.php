<?php



/**
 * APTag
 * @Table @Entity
 */
class APTag extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer")
   * @Id @GeneratedValue
   */
  protected $ID;
  
  /**
   * @var AP $AP
   * @ManyToOne(targetEntity="AP", inversedBy="Tags")
   * @JoinColumns({
   *   @JoinColumn(name="AP", referencedColumnName="ID")
   * })
   */
  protected $AP;

  /**
   * @var string $name
   * @Column(name="name", type="string", length=100, nullable=false, unique=true)
   */
  protected $name;
}

