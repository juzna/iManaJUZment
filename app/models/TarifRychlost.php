<?php



/**
 * TarifRychlost
 *
 * @Table()
 * @Entity
 */
class TarifRychlost extends \ActiveEntity\Entity
{
    /**
     * @var integer $tarif
     * @Column(name="tarif", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $tarif;

    /**
     * @var integer $flag
     * @Column(name="flag", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $flag;

}