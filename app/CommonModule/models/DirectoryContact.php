<?php



/**
 * DirectoryContact
 *
 * @Table @Entity
 */
class DirectoryContact extends \ActiveEntity\Entity {
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;

  /**
   * @ManyToOne(targetEntity="DirectoryEntry", inversedBy="Contacts")
   * @JoinColumn(name="directoryId", referencedColumnName="ID")
   * @ae:immutable @ae:required
   */
  protected $directory;

  /**
   * @var string $type
   * @Column(name="type", type="string", length=10, nullable=false)
   */
  protected $type;

  /**
   * @var string $value
   * @Column(name="value", type="string", length=100, nullable=false)
   */
  protected $value;

  /**
   * @var string $comment
   * @Column(name="comment", type="string", length=255, nullable=true)
   */
  protected $comment;
}