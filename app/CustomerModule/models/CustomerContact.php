<?php



/**
 * Contact defails for a customer
 *
 * @Table @Entity
 */
class CustomerContact extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @var Customer $customer
   * @ManyToOne(targetEntity="Customer", inversedBy="Contacts")
   * @JoinColumn(name="custId", referencedColumnName="custId")
   */
  protected $customer;

  /**
   * @var string $type
   * @Column(name="type", type="string", length=10, nullable=false)
   */
  protected $type;

  /**
   * @var string $value
   * @Column(name="value", type="string", length=100, nullable=false)
   */
  protected $value;

  /**
   * @var string $comment
   * @Column(name="comment", type="string", length=255, nullable=true)
   */
  protected $comment;
}
