<?php



/**
 * Platba
 *
 * @Table()
 * @Entity
 */
class Platba extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

}