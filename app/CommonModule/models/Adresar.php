<?php



/**
 * Adresar
 *
 * @Table()
 * @Entity
 */
class Adresar extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $nazev
     * @Column(name="nazev", type="string", length=100, nullable=true)
     */
    protected $nazev;

    /**
     * @var boolean $jePlatceDph
     * @Column(name="jePlatceDph", type="boolean", nullable=false)
     */
    protected $jePlatceDph;

    /**
     * @var boolean $zobrazit
     * @Column(name="zobrazit", type="boolean", nullable=false)
     */
    protected $zobrazit;

}