<?php

/**
 * BaseZakanzikTarifUhrada
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property integer $tarif
 * @property integer $mesicu
 * @property date $datumOd
 * @property date $datumDo
 * @property ZakaznikTarif $ZakaznikTarif
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseZakanzikTarifUhrada extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ZakanzikTarifUhrada');
        $this->hasColumn('ID', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('tarif', 'integer', 11, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '11',
             ));
        $this->hasColumn('mesicu', 'integer', 3, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '3',
             ));
        $this->hasColumn('datumOd', 'date', null, array(
             'type' => 'date',
             'notnull' => true,
             ));
        $this->hasColumn('datumDo', 'date', null, array(
             'type' => 'date',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('ZakaznikTarif', array(
             'local' => 'tarif',
             'foreign' => 'ID'));
    }
}