<?php



/**
 * DirectoryDepositAccount
 *
 * @Table @Entity
 */
class DirectoryDepositAccount extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @ManyToOne(targetEntity="DirectoryEntry", inversedBy="DepositAccounts")
   * @JoinColumn(name="directoryId", referencedColumnName="ID")
   * @ae:immutable @ae:required
   */
  protected $directory;

  /**
   * @var string $nazev
   * @Column(name="nazev", type="string", length=100, nullable=false)
   */
  protected $nazev;

  /**
   * @var integer $kod
   * @Column(name="kod", type="integer", length=1, nullable=false)
   */
  protected $kod;
}