<?php

/**
 * BaseZakaznikAdresa
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $ID
 * @property integer $PorCis
 * @property boolean $isOdberna
 * @property boolean $isFakturacni
 * @property boolean $isKorespondencni
 * @property string $popis
 * @property string $firma
 * @property string $firma2
 * @property string $titulPred
 * @property string $jmeno
 * @property string $druheJmeno
 * @property string $prijmeni
 * @property string $druhePrijmeni
 * @property string $titulZa
 * @property string $ICO
 * @property string $DIC
 * @property string $poznamka
 * @property string $rodneCislo
 * @property string $datumNarozeni
 * @property Zakaznik $Zakaznik
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseZakaznikAdresa extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ZakaznikAdresa');
        $this->hasColumn('ID', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'autoincrement' => true,
             'length' => '11',
             ));
        $this->hasColumn('PorCis', 'integer', 11, array(
             'type' => 'integer',
             'notnull' => true,
             'length' => '11',
             ));
        $this->hasColumn('isOdberna', 'boolean', null, array(
             'default' => false,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('isFakturacni', 'boolean', null, array(
             'default' => false,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('isKorespondencni', 'boolean', null, array(
             'default' => false,
             'type' => 'boolean',
             'notnull' => true,
             ));
        $this->hasColumn('popis', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('firma', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('firma2', 'string', 100, array(
             'type' => 'string',
             'length' => '100',
             ));
        $this->hasColumn('titulPred', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('jmeno', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('druheJmeno', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('prijmeni', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('druhePrijmeni', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('titulZa', 'string', 50, array(
             'type' => 'string',
             'length' => '50',
             ));
        $this->hasColumn('ICO', 'string', 20, array(
             'type' => 'string',
             'length' => '20',
             ));
        $this->hasColumn('DIC', 'string', 20, array(
             'type' => 'string',
             'length' => '20',
             ));
        $this->hasColumn('poznamka', 'string', 255, array(
             'type' => 'string',
             'length' => '255',
             ));
        $this->hasColumn('rodneCislo', 'string', 20, array(
             'type' => 'string',
             'length' => '20',
             ));
        $this->hasColumn('datumNarozeni', 'string', 20, array(
             'type' => 'string',
             'length' => '20',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Zakaznik', array(
             'local' => 'PorCis',
             'foreign' => 'PorCis'));

        $geographicalcz0 = new Doctrine_Template_GeographicalCZ(array(
             'postal' => true,
             'uir' => true,
             ));
        $this->actAs($geographicalcz0);
    }
}