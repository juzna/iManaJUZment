<?php
/**
 * This file is part of the "iManaJUZment" - complex system for internet service providers
 *
 * Copyright (c) 2005 - 2011 Jan Dolecek (http://juzna.cz)
 *
 * iManaJUZment is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * You should have received a copy of the GNU General Public License
 * along with iManaJUZment.  If not, see <http://www.gnu.org/licenses/gpl.txt>.
 *
 * @license http://www.gnu.org/licenses/gpl.txt
 */

/**
* Basic communication over http with straightcore devices
*/
namespace Autoconfig\Drivers;


class StraightCore {
	public $ip; // IP address of accessible web app
	public $port;
	protected $browser;
	protected $params; // double-hash map of string
	protected $config; // Parsed config values
	
	// Sanitizers
	protected static $sanitizerValues = array(
		'wifi.mode' => array(
			'states(11);'	=> 'searching',
			'states(12);'	=> 'AP',
			'states(13);'	=> 'station',
			'states(16);'	=> 'adhoc',
			//'Stanice'	=> 'station',
			//'Pristupovy bod'=> 'AP',
			//'Stanice Ad-Hoc'=> 'adhoc',
		),
		'wifi.band' => array(
			'2.4 GHz 802.11b'	=> '802.11b',
			'2.4 GHz 802.11g'	=> '802.11g',
			'2.4 GHz 802.11b+g'	=> '802.11bg',
		),
		'encType' => array(
			'-'		=> 'none',
			'WEP 64bit'	=> 'wep',
			'WEP 128bit'	=> 'wep',
		),
		
		'ip.mode' => array(
			'states(0);'	=> 'dhcp', // DHCP obtaining
			'states(1);'	=> 'dhcp',
			'states(2);'	=> 'configured',
			//'Pevna IP' => 'configured',
		),
	);
	
	function __construct($ip, $port = null) {
		// Initialize browser
		$this->browser = new \Browser;
		
		$this->ip = $ip;
		$this->port = $port;
	}
	
	// Show actual status
	private function status($txt) {
	
	}
	
	/**
	* Generate request
	*/
	public function request($site, $post = null) {
		$url = "http://$this->ip" . ($this->port ? ":$this->port" : '') . "/$site";
		return $this->browser->request($url, $post);
	}
	
	/**
	* Flush cache
	*/
	public function flush() {
		$this->params = $this->config = null;
	}
	
	/**
	* Get DOM
	*/
	public function getDOM($code) {
		// Fix code defects
		$data = str_replace(')<i></td>', ')</i></td>', $code);
		
		// Vytvorime XML DOM
		$dom = new \DOMDocument('1.0', 'utf8');
		$data = str_replace('<head>', '<head><meta http-equiv="Content-Type" content="text/html; charset=windows-1250"/>', $data);
		@$dom->loadHTML($data);
		
		return $dom;
	}
	
	/**
	* Get actual settings
	*/
	public function getSettings() {
		if(isset($this->params)) return $this->params; // Get from cache
		
		// Get status page DOM
		$dom = $this->getDOM($code = $this->request('status.asp'));
		
		// Projdeme sloupecky
		$params = array();
		$sekce = '';
		foreach($dom->getElementsByTagName('tr') as $row) {
			$tds = $row->getElementsByTagName('td');
			if($tds->length < 2) {// Je to pouze hlavicka
				$sekce = cp($tds->item(0)->nodeValue);
				continue;
			}
			
			$nazev 	 = cp($tds->item(0)->nodeValue);
			$hodnota = cp($tds->item(1)->nodeValue);
			
			$params[bezdiakritiky($sekce)][bezdiakritiky($nazev)] = trim($hodnota);
		}
		
		return $this->params = $params;
	}
	
	/**
	* Get firmware version
	*/
	public function getFirmwareVersion() {
		$x = $this->getSettings();
		
		$versionString = @$x['Systemove parametry']['Verze firmware'];
		if(!$versionString) throw new \Exception('Nepodarilo se zjistit aktualni verzi firmware');
		list($verze) = explode('(', $versionString);
		return trim($verze);
	}
	
	/**
	* Upgrade firmware in device
	* (This takes more than one minute!)
	* @param string $dir Directory, which contains neweset version
	*/
	public function upgradeFirmware($dir) {
		// Upload all bin files
		foreach(glob("$dir/*.bin") as $file) {
			$this->status("Uploading file " . basename($file));
			
			$fh = new \BrowserFile($file);
			$this->request('goform/formUpload', array('binary' => $fh));
			
			// Rebooting
			$this->status('Rebooting');
			sleep(20);
			
			// Waiting for device to become ready
			do {
				sleep(5);
				echo " Stale cekam...\n";
			} while(!$this->request('status.asp'));
		}
	}
	
	private function sanitize($what, $value) {
		if(isset(self::$sanitizerValues[$what])) {
			return isset(self::$sanitizerValues[$what][$value]) ? self::$sanitizerValues[$what][$value] : $value;
		}
		else return $value;
	}
	
	private function sanitizeWifiSpeed($s) {
		$s = trim(str_replace('Mbit/s', '', $s));
		if($s == '--') return '';
		else return $s;
	}
	
	
	/**
	* Get compact configuration
	*/
	public function getConfig() {
		if(isset($this->config)) return $this->config; // Get from cache
		
		$config = array();
		$x = $this->getSettings();
		
		// Systemove parametry
		$s = 'Systemove parametry';
		$config['system.location'] = @$x[$s]['Umisteni'];
		$config['system.uptime'] = @$x[$s]['Doba behu'];
		$config['system.firmware'] = $fw = @$x[$s]['Verze firmware'];
		if(preg_match('|^([0-9.]+) \\((.+)\\)$|', $fw, $match)) list(, $config['firmware.version'], $config['firmware.date']) = $match;
		
		// Wireless
		$s = 'Konfigurace bezdratove casti';
		if(isset($x[$s])) {
			$config['wifi.mode'] = $this->sanitize('wifi.mode', @$x[$s]['Operacni mod']);
			$config['wifi.band'] = $this->sanitize('wifi.band', @$x[$s]['Typ provozu']);
			$config['wifi.ssid'] = @$x[$s]['Nazev site - SSID'];
			$config['wifi.channel'] = @$x[$s]['Operacni kanal'];
			$config['wifi.encType'] = $this->sanitize('encType', @$x[$s]['Sifrovani']);
			$config['wifi.bssid']	= getMac(@$x[$s]['MAC site - BSSID']);
			$config['wifi.speed']	= $this->sanitizeWifiSpeed(@$x[$s]['Rychlost pripojeni']);
			$config['wifi.numStations'] = @$x[$s]['Pripojenych stanic'];
		}
		
		// IP
		$s = 'Konfigurace protokolu TCP/IP';
		if(isset($x[$s])) {
			$config['ip.mode'] = $this->sanitize('ip.mode', @$x[$s]['Adresa pridelena z']);
			$config['ip.addr'] = @$x[$s]['IP Adresa'];
			$config['ip.netmask'] = @$x[$s]['Sitova maska'];
			$config['ip.gateway'] = @$x[$s]['Vychozi brana'];
		}
		
		// IP LAN
		$s = 'Konfigurace protokolu TCP/IP LAN';
		if(isset($x[$s])) {
			$config['lan.mode'] = $this->sanitize('ip.mode', @$x[$s]['Adresa pridelena z']);
			$config['lan.addr'] = @$x[$s]['IP Adresa'];
			$config['lan.netmask'] = @$x[$s]['Sitova maska'];
		}
		
		// IP WAN
		$s = 'Konfigurace protokolu TCP/IP WAN';
		if(isset($x[$s])) {
			$config['wan.mode'] = $this->sanitize('ip.mode', @$x[$s]['Adresa pridelena z']);
			$config['wan.addr'] = @$x[$s]['IP Adresa'];
			$config['wan.netmask'] = @$x[$s]['Sitova maska'];
			$config['wan.gateway'] = @$x[$s]['Vychozi brana'];
		}
		
		// MAC adresy
		$s = 'Linkova vrstva';
		if(isset($x[$s])) {
			$config['mac.wlan'] = getMac(@$x[$s]['wlan MAC']);
			$config['mac.eth0'] = getMac(@$x[$s]['eth0 MAC']);
			$config['mac.eth1'] = getMac(@$x[$s]['eth1 MAC']);
			$config['mac.bridge'] = getMac(@$x[$s]['bridge MAC']);
		}
		
		return $this->config = $config;
	}
}