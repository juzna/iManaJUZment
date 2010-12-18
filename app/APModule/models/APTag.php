<?php



/**
 * APTag
 * @Table @Entity
 */
class APTag extends \ActiveEntity\Entity
{
  /**
   * @var integer $ID
   * @Column(name="ID", type="integer") @Id @GeneratedValue
   */
  protected $ID;
  
  /**
   * @var string $name
   * @Column(name="name", type="string", length=100, nullable=false, unique=true)
   */
  protected $name;

  /**
   * @Column(name="color", type="string", length=50, nullable=true)
   */
  protected $color;

  /**
   * Get all tags in database
   * @return array of strings
   */
  public static function getAllTagNames() {
    return dibi::query("select distinct [name] from [APTag] order by [name]")->fetchPairs(null, 'name');
  }
}
