<?php



/**
 * TarifFlag
 *
 * @Table()
 * @Entity
 */
class TarifFlag extends \ActiveEntity\Entity
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
     * @Column(name="nazev", type="string", length=50, nullable=false, unique=true)
     */
    protected $nazev;

}