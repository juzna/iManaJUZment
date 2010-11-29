<?php



/**
 * APIP
 *
 * @Table()
 * @Entity @ae:title("Seznam IP adres") @ae:listable @ae:editable @ae:addable
 * @juznovo @ahoj @coje @pepa("pepa vozi krtky", homo=1)
 */
class APIP extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $interface
     * @Column(name="interface", type="string", length=50, nullable=false)
     * @ae:title("Interface name") @ae:format("identifier") @ae:get @ae:set
     */
    protected $interface;

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

    /**
     * @var string $description
     * @Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

}
