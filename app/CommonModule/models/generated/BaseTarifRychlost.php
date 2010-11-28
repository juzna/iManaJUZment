<?php

/**
 * BaseTarifRychlost
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $tarif
 * @property integer $flag
 * @property Tarif $Tarif
 * @property TarifFlag $TarifFlag
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTarifRychlost extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('TarifRychlost');
        $this->hasColumn('tarif', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'length' => '11',
             ));
        $this->hasColumn('flag', 'integer', 11, array(
             'primary' => true,
             'type' => 'integer',
             'length' => '11',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Tarif', array(
             'local' => 'tarif',
             'foreign' => 'ID'));

        $this->hasOne('TarifFlag', array(
             'local' => 'flag',
             'foreign' => 'ID'));

        $inetspeed0 = new Doctrine_Template_InetSpeed();
        $this->actAs($inetspeed0);
    }
}