<?php



/**
 * ZakaznikTarif
 *
 * @Table()
 * @Entity
 */
class ZakaznikTarif extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $popis
     * @Column(name="popis", type="string", length=255, nullable=true)
     */
    protected $popis;

    /**
     * @var boolean $zakladni
     * @Column(name="zakladni", type="boolean", nullable=false)
     */
    protected $zakladni;

    /**
     * @var boolean $specialniCeny
     * @Column(name="specialniCeny", type="boolean", nullable=false)
     */
    protected $specialniCeny;

    /**
     * @var float $mesicniPausal
     * @Column(name="mesicniPausal", type="float", nullable=true)
     */
    protected $mesicniPausal;

    /**
     * @var float $ctvrtletniPausal
     * @Column(name="ctvrtletniPausal", type="float", nullable=true)
     */
    protected $ctvrtletniPausal;

    /**
     * @var float $pololetniPausal
     * @Column(name="pololetniPausal", type="float", nullable=true)
     */
    protected $pololetniPausal;

    /**
     * @var float $rocniPausal
     * @Column(name="rocniPausal", type="float", nullable=true)
     */
    protected $rocniPausal;

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
     * @var date $predplaceno
     * @Column(name="predplaceno", type="date", nullable=true)
     */
    protected $predplaceno;

    /**
     * @var boolean $aktivni
     * @Column(name="aktivni", type="boolean", nullable=false)
     */
    protected $aktivni;

    /**
     * @var boolean $zaplacenoCele
     * @Column(name="zaplacenoCele", type="boolean", nullable=true)
     */
    protected $zaplacenoCele;

}