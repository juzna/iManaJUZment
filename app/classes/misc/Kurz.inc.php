<?php
/**
 * Zakladni funkce pro pocitani s kurzem
 * TODO: rework for new version of framework
 */
class Kurz {
	const DOMACI = 'CZK';
	
	protected static function getDB() {
	  return \dibi::connect(\Nette\Environment::getConfig('database-kurz'));
  }

	/**
	* Nacte kurz z CNB a ulozi jej
	* @param timestamp $datum Den, ze ktereho se ma hledat kurz
	* @param string $datum out: textovy vysledek funkce
	* @return bool Zda se povedlo nacitani nebo ne
	*/
	static function nactiKurz($datum, &$error = '') {
		// Nacteme z netu
		$url = 'http://www.cnb.cz/cs/financni_trhy/devizovy_trh/kurzy_devizoveho_trhu/denni_kurz.txt?date=' . date('d.m.Y', $datum);
		$data = @file_get_contents($url);
		if(empty($data)) {
			$error = 'Chyba pri nacitani kurzu';
			return false;
		}

		// Datum z vypisu
		$rows = explode("\n", $data);
		list($datum) = explode(' ', array_shift($rows));
		if(!$datum = @strtotime($datum)) {
			$error = 'Nepovedlo se nalezt datum ve vypise';
			return false;
		}
		array_shift($rows); // Odebereme hlavicku


		// Projdeme kurzy
		$d = date('Y-m-d', $datum);
		$domaci = kurz::DOMACI;
		foreach($rows as $row) {
			@list($stat, $mena, $mnozstvi, $kod, $kurz) = explode('|', trim($row));
			if(empty($kurz)) continue;

			// Prevod desetinne carky
			$kurz = strtr($kurz, ',', '.');

			// Ulozime
			q("REPLACE INTO `Kurz` SET datum='$d', domaci='$domaci', mena='$kod', mnozstvi='$mnozstvi', kurz='$kurz'");
		}

		$error = 'Kurz byl ulozen';
		return true;
	}

	/**
	* Najde kurz meny k danemu datu
	* @param string $mena Kod meny
	* @param date $datum Datum, ke kteremu se ma hledat kurz
	* @param array $row out: Kompletni informace o danem kurzu
	* @return float Koeficient pro dany kurz
	*/
	static function najdiKurz($mena, $datum, &$row = null) {
		if($mena == self::DOMACI) return 1; // Jedna se o domaci menu

		// Uprava data
		if(empty($datum)) $datum = time();
		elseif(!is_numeric($datum)) $datum = @strtotime($datum);
		$datum = date('Y-m-d', $datum);

		// Hledame kurz
		$row = mfa("SELECT * FROM `Kurz` WHERE `mena`='$mena' AND `datum`<='$datum' ORDER BY `datum` DESC");

		// Kurz neexistuje -> zda je mozne ho dohledat
		if(!$row) {
			// Kontrola, zda je to platna mena
			if(mr("SELECT count(*) FROM Mena WHERE kod='$mena'")) {
				// Zkusime nacist z CNB
				$ret = self::nactiKurz($datum, $err);
				
				// Nenacteno
				if(!$ret) throw new NotFoundException("Chyba pri hledani kurzu: $err");
				
				// Hledame znova v DB
				$row = mfo("SELECT * FROM `Kurz` WHERE `mena`='$mena' AND `datum`<='$datum' ORDER BY `datum` DESC");
				if(!$row) throw new NotFoundException("Kurz meny $mena ke dni $datum nebyl nalezen");
			}
			
			// Neni platba mena
			else throw new NotFoundException("Mena $mena v systemu neexistuje");
		}
		
		// Vratime vysledny kurz
		return $row['kurz'] / $row['mnozstvi'];
	}
	
	/**
	* Prepocitani castky v jedne mene na druhou
	* @param float $castka Castka zadana v mene $zMeny
	* @param string $zMeny Kod zdrojove castky
	* @param string $naMenu Kod castky, do ktere chceme prepocitat
	* @param date $datum Datum, ke kteremu se castka prevadi
	* @return float Castka v nove mene
	*/
	static function prepocitat($castka, $zMeny, $naMenu, $datum = null) {
		if($zMeny == $naMenu) return $castka; // Zmena na tu samou menu

		// Prepocitame na domaci menu
		if($zMeny != self::DOMACI) $castka *= self::najdiKurz($zMeny, $datum);

		// Prepocet na vyslednou
		if($naMenu != self::DOMACI) $castka /= self::najdiKurz($naMenu, $datum);

		return $castka;
	}
}

