<?php



/**
 * TariffFlag
 *
 * @Table @Entity
 */
class TariffFlag extends \ActiveEntity\Entity
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