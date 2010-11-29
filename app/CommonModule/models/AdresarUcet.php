<?php



/**
 * AdresarUcet
 *
 * @Table()
 * @Entity
 */
class AdresarUcet extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

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

}