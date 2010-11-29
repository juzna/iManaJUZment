<?php



/**
 * APServiceList
 *
 * @Table()
 * @Entity
 */
class APServiceList extends \ActiveEntity\Entity
{
    /**
     * @var string $code
     * @Column(name="code", type="string")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $code;

    /**
     * @var string $nazev
     * @Column(name="nazev", type="string", length=50, nullable=false)
     */
    protected $nazev;

    /**
     * @var string $popis
     * @Column(name="popis", type="string", length=255, nullable=true)
     */
    protected $popis;

}