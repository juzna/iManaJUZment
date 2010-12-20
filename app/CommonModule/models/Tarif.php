<?php



/**
 * Tarif
 *
 * @Table @Entity
 * @ae:links(module="Common", presenter="tarif", alias="tarif", common={"add", "edit", "remove"}, {
 *  @ae:link(title="detail", view="detail", params={"$ID"})
 * })
 */
class Tarif extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer") @Id @GeneratedValue
     */
    protected $ID;

    /**
     * @var string $nazev
     * @Column(name="nazev", type="string", length=50, nullable=false, unique=true)
     * @ae:link(title="detail", view="detail", params={"$ID"})
     */
    protected $nazev;

    /**
     * @var float $mesicniPausal
     * @Column(name="mesicniPausal", type="float", nullable=false)
     */
    protected $mesicniPausal;

    /**
     * @var float $ctvrtletniPausal
     * @Column(name="ctvrtletniPausal", type="float", nullable=false)
     */
    protected $ctvrtletniPausal;

    /**
     * @var float $pololetniPausal
     * @Column(name="pololetniPausal", type="float", nullable=false)
     */
    protected $pololetniPausal;

    /**
     * @var float $rocniPausal
     * @Column(name="rocniPausal", type="float", nullable=false)
     */
    protected $rocniPausal;

    /**
     * @var string $popis
     * @Column(name="popis", type="string", length=255, nullable=false)
     */
    protected $popis;

    /**
     * @var boolean $posilatFaktury
     * @Column(name="posilatFaktury", type="boolean", nullable=false)
     */
    protected $posilatFaktury;

}