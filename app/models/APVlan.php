<?php



/**
 * APVlan
 *
 * @Table()
 * @Entity
 */
class APVlan extends \ActiveEntity\Entity
{
    /**
     * @var integer $AP
     * @Column(name="AP", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $AP;

    /**
     * @var integer $vlan
     * @Column(name="vlan", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $vlan;

    /**
     * @var string $description
     * @Column(name="description", type="string", length=255, nullable=true)
     */
    protected $description;

}