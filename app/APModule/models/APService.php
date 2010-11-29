<?php



/**
 * APService
 *
 * @Table()
 * @Entity
 */
class APService extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $state
     * @Column(name="state", type="string", length=20, nullable=false)
     */
    protected $state;

    /**
     * @var string $stateText
     * @Column(name="stateText", type="string", length=100, nullable=true)
     */
    protected $stateText;

    /**
     * @var timestamp $lastCheck
     * @Column(name="lastCheck", type="timestamp", nullable=true)
     */
    protected $lastCheck;

}