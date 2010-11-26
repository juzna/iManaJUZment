<?php

/**
 * BaseAPServiceOSList
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property string $code
 * @property string $os
 * @property string $version
 * @property APServiceList $APServiceList
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAPServiceOSList extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('APServiceOSList');
        $this->hasColumn('ID', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('code', 'string', 20, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '20',
             ));
        $this->hasColumn('os', 'string', 20, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '20',
             ));
        $this->hasColumn('version', 'string', 20, array(
             'default' => '%',
             'type' => 'string',
             'notnull' => true,
             'length' => '20',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('APServiceList', array(
             'local' => 'code',
             'foreign' => 'code'));
    }
}