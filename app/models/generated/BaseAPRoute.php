<?php

/**
 * BaseAPRoute
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property integer $AP
 * @property string $ip
 * @property integer $netmask
 * @property string $gateway
 * @property string $preferedSource
 * @property integer $distance
 * @property string $popis
 * @property boolean $enabled
 * @property AP $AP
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAPRoute extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('APRoute');
        $this->hasColumn('ID', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('AP', 'integer', 11, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '11',
             ));
        $this->hasColumn('ip', 'string', 15, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '15',
             ));
        $this->hasColumn('netmask', 'integer', 2, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '2',
             ));
        $this->hasColumn('gateway', 'string', 15, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '15',
             ));
        $this->hasColumn('preferedSource', 'string', 15, array(
             'type' => 'string',
             'length' => '15',
             ));
        $this->hasColumn('distance', 'integer', 2, array(
             'default' => 1,
             'type' => 'integer',
             'notnull' => true,
             'length' => '2',
             ));
        $this->hasColumn('popis', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('enabled', 'boolean', null, array(
             'default' => true,
             'type' => 'boolean',
             'notnull' => true,
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