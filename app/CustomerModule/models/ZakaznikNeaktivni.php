<?php



/**
 * ZakaznikNeaktivni
 *
 * @Table()
 * @Entity
 */
class ZakaznikNeaktivni extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var date $datumOd
     * @Column(name="datumOd", type="date", nullable=false)
     */
    protected $datumOd;

    /**
     * @var date $datumDo
     * @Column(name="datumDo", type="date", nullable=true)
     */
    protected $datumDo;

    /**
     * @var string $duvod
     * @Column(name="duvod", type="string", length=255, nullable=false)
     */
    protected $duvod;

}