<?php



/**
 * Zakaznik
 *
 * @Table()
 * @Entity
 */
class Zakaznik extends \ActiveEntity\Entity
{
    /**
     * @var integer $PorCis
     * @Column(name="PorCis", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $PorCis;

    /**
     * @var integer $cisloSmlouvy
     * @Column(name="cisloSmlouvy", type="integer", length=11, nullable=true, unique=true)
     */
    protected $cisloSmlouvy;

    /**
     * @var string $heslo
     * @Column(name="heslo", type="string", length=50, nullable=true)
     */
    protected $heslo;

    /**
     * @var date $aktivniOd
     * @Column(name="aktivniOd", type="date", nullable=true)
     */
    protected $aktivniOd;

    /**
     * @var boolean $accepted
     * @Column(name="accepted", type="boolean", nullable=false)
     */
    protected $accepted;

    /**
     * @var integer $acceptedUser
     * @Column(name="acceptedUser", type="integer", nullable=true)
     */
    protected $acceptedUser;

    /**
     * @var timestamp $acceptedTime
     * @Column(name="acceptedTime", type="timestamp", nullable=true)
     */
    protected $acceptedTime;

    /**
     * @var date $predplaceno
     * @Column(name="predplaceno", type="date", nullable=true)
     */
    protected $predplaceno;

    /**
     * @var boolean $aktivni
     * @Column(name="aktivni", type="boolean", nullable=true)
     */
    protected $aktivni;

    /**
     * @var integer $staryDluh
     * @Column(name="staryDluh", type="integer", nullable=true)
     */
    protected $staryDluh;

    /**
     * @var boolean $nepocitatPredplatne
     * @Column(name="nepocitatPredplatne", type="boolean", nullable=false)
     */
    protected $nepocitatPredplatne;

    /**
     * @var string $nepocitatPredplatneDuvod
     * @Column(name="nepocitatPredplatneDuvod", type="string", length=255, nullable=true)
     */
    protected $nepocitatPredplatneDuvod;

    /**
     * @var float $instalacniPoplatek
     * @Column(name="instalacniPoplatek", type="float", nullable=true)
     */
    protected $instalacniPoplatek;

    /**
     * @var integer $doporucitel
     * @Column(name="doporucitel", type="integer", length=11, nullable=true)
     */
    protected $doporucitel;

    /**
     * @var date $sepsaniSmlouvy
     * @Column(name="sepsaniSmlouvy", type="date", nullable=true)
     */
    protected $sepsaniSmlouvy;

    /**
     * @var integer $neplaticSkupina
     * @Column(name="neplaticSkupina", type="integer", nullable=true)
     */
    protected $neplaticSkupina;

    /**
     * @var integer $neplaticTolerance
     * @Column(name="neplaticTolerance", type="integer", nullable=true)
     */
    protected $neplaticTolerance;

    /**
     * @var date $neplaticNeresitDo
     * @Column(name="neplaticNeresitDo", type="date", nullable=true)
     */
    protected $neplaticNeresitDo;

}