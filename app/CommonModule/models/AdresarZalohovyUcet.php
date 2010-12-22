<?php



/**
 * AdresarZalohovyUcet
 *
 * @Table @Entity
 */
class AdresarZalohovyUcet extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @ManyToOne(targetEntity="Adresar", inversedBy="UctyZalohove")
   * @JoinColumn(name="adresarId", referencedColumnName="ID")
   * @ae:immutable @ae:required
   */
  protected $adresar;

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