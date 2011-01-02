<?php



/**
 * DirectoryAccount
 *
 * @Table @Entity
 */
class DirectoryAccount extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @ManyToOne(targetEntity="DirectoryEntry", inversedBy="Accounts")
   * @JoinColumn(name="directoryId", referencedColumnName="ID")
   * @ae:immutable @ae:required
   */
  protected $directory;

  /**
   * @var string $predcisli
   * @Column(name="predcisli", type="string", length=10, nullable=false)
   */
  protected $predcisli;

  /**
   * @var string $cislo
   * @Column(name="cislo", type="string", length=10, nullable=false)
   */
  protected $cislo;

  /**
   * @var string $kodBanky
   * @Column(name="kodBanky", type="string", length=4, nullable=false)
   */
  protected $kodBanky;

  /**
   * @var string $poznamka
   * @Column(name="poznamka", type="string", length=255, nullable=true)
   */
  protected $poznamka;

  /**
   * @var bool $active
   * @Column(type="boolean", nullable=false)
   */
  protected $active;
}
