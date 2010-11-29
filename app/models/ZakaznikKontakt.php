<?php



/**
 * ZakaznikKontakt
 *
 * @Table()
 * @Entity
 */
class ZakaznikKontakt extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $typ
     * @Column(name="typ", type="string", length=10, nullable=false)
     */
    protected $typ;

    /**
     * @var string $hodnota
     * @Column(name="hodnota", type="string", length=100, nullable=false)
     */
    protected $hodnota;

    /**
     * @var string $popis
     * @Column(name="popis", type="string", length=255, nullable=true)
     */
    protected $popis;

}