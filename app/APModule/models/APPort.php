<?php



/**
 * APPort
 *
 * @Table()
 * @Entity
 */
class APPort extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $port
     * @Column(name="port", type="string", length=50, nullable=false)
     */
    protected $port;

    /**
     * @var enum $typ
     * @Column(name="typ", type="enum", nullable=false)
     */
    protected $typ;

    /**
     * @var integer $PorCis
     * @Column(name="PorCis", type="integer", length=11, nullable=true)
     */
    protected $PorCis;

    /**
     * @var integer $odbernaAdresa
     * @Column(name="odbernaAdresa", type="integer", length=11, nullable=true)
     */
    protected $odbernaAdresa;

    /**
     * @var string $cisloVchodu
     * @Column(name="cisloVchodu", type="string", length=20, nullable=true)
     */
    protected $cisloVchodu;

    /**
     * @var string $cisloBytu
     * @Column(name="cisloBytu", type="string", length=20, nullable=true)
     */
    protected $cisloBytu;

    /**
     * @var integer $connectedTo
     * @Column(name="connectedTo", type="integer", length=11, nullable=true)
     */
    protected $connectedTo;

    /**
     * @var string $connectedInterface
     * @Column(name="connectedInterface", type="string", length=50, nullable=true)
     */
    protected $connectedInterface;

    /**
     * @var string $connectedPort
     * @Column(name="connectedPort", type="string", length=50, nullable=true)
     */
    protected $connectedPort;

    /**
     * @var boolean $isUplink
     * @Column(name="isUplink", type="boolean", nullable=false)
     */
    protected $isUplink;

    /**
     * @var string $popis
     * @Column(name="popis", type="string", length=255, nullable=true)
     */
    protected $popis;

}