<?php



/**
 * ZakanzikTarifUhrada
 *
 * @Table()
 * @Entity
 */
class ZakanzikTarifUhrada extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var integer $mesicu
     * @Column(name="mesicu", type="integer", length=3, nullable=false)
     */
    protected $mesicu;

    /**
     * @var date $datumOd
     * @Column(name="datumOd", type="date", nullable=false)
     */
    protected $datumOd;

    /**
     * @var date $datumDo
     * @Column(name="datumDo", type="date", nullable=false)
     */
    protected $datumDo;

}