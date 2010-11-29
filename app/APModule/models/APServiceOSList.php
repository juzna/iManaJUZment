<?php



/**
 * APServiceOSList
 *
 * @Table()
 * @Entity
 */
class APServiceOSList extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $os
     * @Column(name="os", type="string", length=20, nullable=false)
     */
    protected $os;

    /**
     * @var string $version
     * @Column(name="version", type="string", length=20, nullable=false)
     */
    protected $version;

}