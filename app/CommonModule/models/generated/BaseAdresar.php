<?php

/**
 * BaseAdresar
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property string $nazev
 * @property boolean $jePlatceDph
 * @property boolean $zobrazit
 * @property Doctrine_Collection $AdresarAdresa
 * @property Doctrine_Collection $AdresarKontakt
 * @property Doctrine_Collection $AdresarUcet
 * @property Doctrine_Collection $AdresarZalohovyUcet
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseAdresar extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('Adresar');
        $this->hasColumn('ID', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('nazev', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('jePlatceDph', 'boolean', null, array(
             'default' => false,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('zobrazit', 'boolean', null, array(
             'default' => true,
             'type' => 'boolean',
             'notnull' => true,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasMany('AdresarAdresa', array(
             'local' => 'ID',
             'foreign' => 'adresar'));

        $this->hasMany('AdresarKontakt', array(
             'local' => 'ID',
             'foreign' => 'adresar'));

        $this->hasMany('AdresarUcet', array(
             'local' => 'ID',
             'foreign' => 'adresar'));

        $this->hasMany('AdresarZalohovyUcet', array(
             'local' => 'ID',
             'foreign' => 'adresar'));
    }
}