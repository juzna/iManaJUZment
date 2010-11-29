<?php



/**
 * APPokrytiSubnet
 *
 * @Table()
 * @Entity
 */
class APPokrytiSubnet extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $ip
     * @Column(name="ip", type="string", length=15, nullable=false)
     */
    protected $ip;

    /**
     * @var integer $netmask
     * @Column(name="netmask", type="integer", length=2, nullable=false)
     */
    protected $netmask;

}