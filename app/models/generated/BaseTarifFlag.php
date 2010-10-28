<?php

/**
 * BaseTarifFlag
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property string $nazev
 * @property Doctrine_Collection $TarifRychlost
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTarifFlag extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('TarifFlag');
        $this->hasColumn('ID', 'integer', 11, array(
             'primary' => true,
             'unique' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('nazev', 'string', 50, array(
             'unique' => true,
             'type' => 'string',
             'notnull' => true,
             'length' => '50',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('TarifRychlost', array(
             'local' => 'ID',
             'foreign' => 'flag'));
    }
}