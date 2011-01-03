<?php

namespace BaseModule;

use dibi, Nette\Environment;

/**
 * Showing dialogs for postal addresses
 * It uses database from some czech ministry
 *
 * TODO: Used code is old, need to be reworked!!!!
 */
class UirPresenter extends \BasePresenter {
  protected $db;
  
  public function startup() {
    $this->db = dibi::connect(Environment::getConfig('database-uir'));
  }

	/**
	* Load data for UIR selectboxes
	*/
	function akce_loadData() {
		$what = explode(',', @$_REQUEST['what']);
		$ret = array();
		
		// Nastavime filtrovou podminku
		if(@$_REQUEST['filter']) {
			global $firma;
			$filter = "`show_$firma`=1";
		} else $filter = '1';
		
		// Propagate information from object to top
		$this->propagate($_REQUEST);
		
		if(@$_REQUEST['debug']) { echo '<pre>'; print_r($_REQUEST); }
		
		
		// Seznam oblasti
		if(in_array('oblast', $what)) {
			$p = 'top=0';
			if(@$_REQUEST['parent-oblast'] != $p) {
				$ret['oblast'] = array(
					'parent'	=> $p,
					'show'		=> 1,
					'htmlCode'	=> $this->geretateOptionList("select `oblast_kod`, `nazev` from `oblast` where $filter order by `nazev`"),
				);
			}
		}
		
		// Seznam kraju
		if(in_array('kraj', $what) && ($oblast = @$_REQUEST['value-oblast'])) {
			$p = "oblast-$oblast";
			if(@$_REQUEST['parent-kraj'] != $p) {
				$ret['kraj'] = array(
					'parent'	=> $p,
					'show'		=> 1,
					'htmlCode'	=> $this->geretateOptionList("select `kraj_kod`, `nazev` from `kraj` where `oblast_kod`='$oblast' and $filter order by `nazev`"),
				);
			}
		}
		
		// Seznam okresu
		if(in_array('okres', $what) && ($kraj = @$_REQUEST['value-kraj'])) {
			$p = "kraj-$kraj";
			if(@$_REQUEST['parent-okres'] != $p) {
				$ret['okres'] = array(
					'parent'	=> $p,
					'show'		=> 1,
					'htmlCode'	=> $this->geretateOptionList("select `okres_kod`, `nazev` from `okres` where `kraj_kod`='$kraj' and $filter order by `nazev`"),
				);
			}
		}
		
		// Seznam obci
		if(in_array('obec', $what) && ($okres = @$_REQUEST['value-okres'])) {
			$p = "okres-$okres";
			if(@$_REQUEST['parent-obec'] != $p) {
				$ret['obec'] = array(
					'parent'	=> $p,
					'show'		=> 1,
					'htmlCode'	=> $this->geretateOptionList("select `obec_kod`, `nazev` from `obec` where `okres_kod`='$okres' and $filter order by `nazev`"),
				);
			}
		}
		
		// Seznam casti obci
		if(in_array('cobce', $what) && ($obec = @$_REQUEST['value-obec'])) {
			$p = "obec-$obec";
			if(@$_REQUEST['parent-cobce'] != $p) {
				$ret['cobce'] = array(
					'parent'	=> $p,
					'show'		=> 1,
					'htmlCode'	=> $this->geretateOptionList("select `cobce_kod`, `nazev` from `cobce` where `obec_kod`='$obec' and $filter order by `nazev`"),
				);
			}
		}
		
		// Seznam ulic
		if(in_array('ulice', $what) && ($cobce = @$_REQUEST['value-cobce'])) {
			$p = "cobce-$obec";
			if(@$_REQUEST['parent-ulice'] != $p) {
				$sql = "select `ulice_kod`, `nazev`
					from `ulice` join `vazba` using(`ulice_kod`)
					where `vazba`.`cobce_kod`='$cobce' 
					order by `nazev`";
				$ret['ulice'] = array(
					'parent'	=> $p,
					'show'		=> 1,
					'htmlCode'	=> $this->geretateOptionList($sql),
				);
			}
		}
		
		// Seznam objektu
		if(in_array('objekt', $what)) {
			if($ulice = @$_REQUEST['value-ulice']) {
				$p = "ulice-$ulice";
				if(@$_REQUEST['parent-objekt'] != $p) {
					$sql = "select `objekt`.`objekt_kod`, concat(`cisdom_hod`, '/', `cisor_hod`)
						from `adresa` join `objekt` using(`objekt_kod`)
						where `adresa`.`ulice_kod`='$ulice' and $filter
						order by `cisdom_hod`";
						
					$ret['objekt'] = array(
						'parent'	=> $p,
						'show'		=> 1,
						'htmlCode'	=> $this->geretateOptionList($sql),
					);
				}
			}
			
			// 
			elseif(($cobce = @$_REQUEST['value-cobce']) && empty($ret['ulice']['htmlCode'])) {
				// Skryjeme ulici
				$ret['ulice']['show'] = 0;
				$p = "cobce-$cobce";
				if(@$_REQUEST['parent-objekt'] != $p) {
					$sql = "select `objekt_kod`, `cisdom_hod` from `objekt` where `cobce_kod`='$cobce' and $filter order by `cisdom_hod`";
					$ret['objekt'] = array(
						'parent'	=> $p,
						'show'		=> 1,
						'htmlCode'	=> $this->geretateOptionList($sql),
					);
				}
			}
		}
		
		// Add more values
		foreach($_REQUEST as $k => $v) {
			$x = substr($k, 6);
			if(startsWith($k, 'value-') && !empty($v) && isset($ret[$x])) $ret[$x]['value'] = $v;
		}
		
		if(@$_GET['debug']) print_r2($ret);
		
		return ajax_ret(1, 'OK', array('data' => $ret));
	}
	
	/**
	* Propagate data; if you have more specific information, find the common one (eg. from objekt id find ulice or cast obce, etc.)
	*/
	function propagate(&$data) {
		// Je zadan objekt, zjistujeme adresu nebo cast obce
		if(!empty($data['value-objekt'])) {
			$objId = $data['value-objekt'];
			$data['value-cobce'] = mr("select cobce_kod from `objekt` where `objekt_kod`='$objId'", 'adresy');
			
			// Najdeme adresu
			$ra = mfa("select * from `adresa` where `objekt_kod`='$objId'", 'adresy');
			if($ra) {
				$data['value-adresa'] = $ra['adresa_kod'];
				$data['value-ulice'] = $ra['ulice_kod'];
			}
			else {
				$data['value-ulice'] = null;
			}
		}
		
		// Neni objekt, ale je ulice
		elseif(!empty($data['value-ulice'])) {
			$data['value-cobce'] = mr("select `cobce_kod` from `vazba` where `ulice_kod`='{$data['value-ulice']}'", 'adresy');
		}
		
		// Je zadana cast obce -> dohledame obec
		if(!empty($data['value-cobce'])) $data['value-obec'] = mr("select `obec_kod` from `cobce` where `cobce_kod`='{$data['value-cobce']}'", 'adresy');
	
		// Je zadana obec -> dohledame okres
		if(!empty($data['value-obec'])) $data['value-okres'] = mr("select `okres_kod` from `obec` where `obec_kod`='{$data['value-obec']}'", 'adresy');
	
		// Je zadan okres -> dohledame kraj
		if(!empty($data['value-okres'])) $data['value-kraj'] = mr("select `kraj_kod` from `okres` where `okres_kod`='{$data['value-okres']}'", 'adresy');
	
		// Je zadana kraj -> dohledame oblast
		if(!empty($data['value-kraj'])) $data['value-oblast'] = mr("select `oblast_kod` from `kraj` where `kraj_kod`='{$data['value-kraj']}'", 'adresy');
	}

	
	/**
	* Convert SQL to html code option list
	*/
	function geretateOptionList($sql) {
		if(@$_REQUEST['debug']) echo "SQL: $sql\n";
		
		$list = massoc(q($sql, 'adresy'));
		if(empty($list)) return null;
		
		$ret = array();
		$ret[] = '<option></option>';
		foreach($list as $key => $val) $ret[] = "<option value=\"$key\">$val</option>";
		
		return implode($ret);
	}
	
	/**
	* Get info about an object
	*/
	function akce_getInfo($i) {
		$ret = uir_objektInfo($i);
		ajax_ret(1, 'retrieved', $ret);
		print_r2($ret);
	}
}
