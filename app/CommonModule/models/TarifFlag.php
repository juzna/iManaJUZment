<?php



/**
 * TarifFlag
 *
 * @Table @Entity
 */
class TarifFlag extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer") @Id @GeneratedValue
     */
    protected $ID;

    /**
     * @var string $name
     * @Column(name="name", type="string", length=50, nullable=false, unique=true)
     */
    protected $nazev;

}