<?php

/**
 * BaseAPVlan
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $AP
 * @property integer $vlan
 * @property string $description
 * @property AP $AP
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAPVlan extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('APVlan');
        $this->hasColumn('AP', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'length' => '11',
             ));
        $this->hasColumn('vlan', 'integer', 4, array(
             'primary' => true,
             'type' => 'integer',
             'length' => '4',
             ));
        $this->hasColumn('description', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('AP', array(
             'local' => 'AP',
             'foreign' => 'ID'));
    }
}