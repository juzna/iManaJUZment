<?php



/**
 * ZakaznikNeaktivniTarif
 *
 * @Table()
 * @Entity
 */
class ZakaznikNeaktivniTarif extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

}