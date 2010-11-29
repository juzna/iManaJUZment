<?php



/**
 * APParent
 *
 * @Table()
 * @Entity
 */
class APParent extends \ActiveEntity\Entity
{
    /**
     * @var integer $ID
     * @Column(name="ID", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $ID;

    /**
     * @var string $parentInterface
     * @Column(name="parentInterface", type="string", length=50, nullable=true)
     */
    protected $parentInterface;

    /**
     * @var string $parentPort
     * @Column(name="parentPort", type="string", length=50, nullable=true)
     */
    protected $parentPort;

    /**
     * @var integer $parentVlan
     * @Column(name="parentVlan", type="integer", length=4, nullable=false)
     */
    protected $parentVlan;

    /**
     * @var string $childInterface
     * @Column(name="childInterface", type="string", length=50, nullable=true)
     */
    protected $childInterface;

    /**
     * @var string $childPort
     * @Column(name="childPort", type="string", length=50, nullable=true)
     */
    protected $childPort;

    /**
     * @var integer $childVlan
     * @Column(name="childVlan", type="integer", length=4, nullable=false)
     */
    protected $childVlan;

    /**
     * @var string $comment
     * @Column(name="comment", type="string", length=255, nullable=true)
     */
    protected $comment;

}