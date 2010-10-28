<?php

/**
 * BaseAdresarUcet
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property integer $adresar
 * @property string $predcisli
 * @property string $cislo
 * @property string $kodBanky
 * @property string $poznamka
 * @property Adresar $Adresar
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAdresarUcet extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('AdresarUcet');
        $this->hasColumn('ID', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('adresar', 'integer', 11, array(
             'type' => 'integer',
             'length' => '11',
             ));
        $this->hasColumn('predcisli', 'string', 10, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '10',
             ));
        $this->hasColumn('cislo', 'string', 10, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '10',
             ));
        $this->hasColumn('kodBanky', 'string', 4, array(
             'type' => 'string',
             'notnull' => true,
             'length' => '4',
             ));
        $this->hasColumn('poznamka', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Adresar', array(
             'local' => 'adresar',
             'foreign' => 'ID'));
    }
}