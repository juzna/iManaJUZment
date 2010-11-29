<?php



/**
 * APParams
 *
 * @Table()
 * @Entity
 */
class APParams extends \ActiveEntity\Entity
{
    /**
     * @var integer $AP
     * @Column(name="AP", type="integer")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $AP;

    /**
     * @var string $name
     * @Column(name="name", type="string")
     * @Id
     * @GeneratedValue(strategy="NONE")
     */
    protected $name;

    /**
     * @var string $value
     * @Column(name="value", type="string", length=50, nullable=true)
     */
    protected $value;

    /**
     * @var string $comment
     * @Column(name="comment", type="string", length=255, nullable=true)
     */
    protected $comment;

}