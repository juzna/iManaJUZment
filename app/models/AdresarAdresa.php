<?php



/**
 * AdresarAdresa
 *
 * @Table()
 * @Entity
 */
class AdresarAdresa extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var boolean $isOdberna
     * @Column(name="isOdberna", type="boolean", nullable=false)
     */
    protected $isOdberna;

    /**
     * @var boolean $isFakturacni
     * @Column(name="isFakturacni", type="boolean", nullable=false)
     */
    protected $isFakturacni;

    /**
     * @var boolean $isKorespondencni
     * @Column(name="isKorespondencni", type="boolean", nullable=false)
     */
    protected $isKorespondencni;

    /**
     * @var string $popis
     * @Column(name="popis", type="string", length=255, nullable=true)
     */
    protected $popis;

    /**
     * @var string $firma
     * @Column(name="firma", type="string", length=100, nullable=true)
     */
    protected $firma;

    /**
     * @var string $firma2
     * @Column(name="firma2", type="string", length=100, nullable=true)
     */
    protected $firma2;

    /**
     * @var string $titulPred
     * @Column(name="titulPred", type="string", length=50, nullable=true)
     */
    protected $titulPred;

    /**
     * @var string $jmeno
     * @Column(name="jmeno", type="string", length=50, nullable=true)
     */
    protected $jmeno;

    /**
     * @var string $druheJmeno
     * @Column(name="druheJmeno", type="string", length=50, nullable=true)
     */
    protected $druheJmeno;

    /**
     * @var string $prijmeni
     * @Column(name="prijmeni", type="string", length=50, nullable=true)
     */
    protected $prijmeni;

    /**
     * @var string $druhePrijmeni
     * @Column(name="druhePrijmeni", type="string", length=50, nullable=true)
     */
    protected $druhePrijmeni;

    /**
     * @var string $titulZa
     * @Column(name="titulZa", type="string", length=50, nullable=true)
     */
    protected $titulZa;

    /**
     * @var string $ulice
     * @Column(name="ulice", type="string", length=50, nullable=true)
     */
    protected $ulice;

    /**
     * @var string $cisloPopisne
     * @Column(name="cisloPopisne", type="string", length=20, nullable=true)
     */
    protected $cisloPopisne;

    /**
     * @var string $mesto
     * @Column(name="mesto", type="string", length=50, nullable=true)
     */
    protected $mesto;

    /**
     * @var string $PSC
     * @Column(name="PSC", type="string", length=10, nullable=true)
     */
    protected $PSC;

    /**
     * @var integer $uir_objekt
     * @Column(name="uir_objekt", type="integer", length=11, nullable=true)
     */
    protected $uir_objekt;

    /**
     * @var string $ICO
     * @Column(name="ICO", type="string", length=20, nullable=true)
     */
    protected $ICO;

    /**
     * @var string $DIC
     * @Column(name="DIC", type="string", length=20, nullable=true)
     */
    protected $DIC;

    /**
     * @var string $poznamka
     * @Column(name="poznamka", type="string", length=255, nullable=true)
     */
    protected $poznamka;

    /**
     * @var string $rodneCislo
     * @Column(name="rodneCislo", type="string", length=20, nullable=true)
     */
    protected $rodneCislo;

    /**
     * @var string $datumNarozeni
     * @Column(name="datumNarozeni", type="string", length=20, nullable=true)
     */
    protected $datumNarozeni;

}