<?php

namespace Mikrotik;


/**
 * Working with Winbox config file
 * TODO: rework, it's old version
 */
class Winbox {

  function load($data) {
    // Otestujeme hlavicku
    $header = "\x0F\x10\xC0\xBE";
    if(substr($data, 0, 4)!=$header) {
      user_error('Neplatny soubor');
      return false;
    }


    $data = substr($data, 4);
    $ret = array();

    for($index = $x = 0; $x < 1000 && $data; $x++) {
      // Prvni 2 bajty vyjadruji delku
      $len = ord($data{0}) + ord($data{1}) * 256;

      // Ziskame par atribut=hodnota
      $pair = substr($data, 2, $len);

      // Posuneme se na dalsi
      $data = substr($data, $len + 2);

      // Pokud je prazny, pokracujeme
      if(!$len) {
        $index++;
        continue;
      }


      // delka atrubutu
      $len2 = ord($pair{0});

      $name = substr($pair, 1, $len2);
      $value = substr($pair, 1 + $len2);

      $ret[$index][$name] = $value;
    }

    return $ret;
  }




  function save($params) {
    // Hlavicka
    $header = "\x0F\x10\xC0\xBE";

    //Defaultni hodnoty
    $default =   array (
        'type' => 'addr',
        'host' => '',
        'login' => 'admin',
        'note' => '(neznamy)',
        'secure-mode' => "\x01",
        'keep-pwd' => "\x01",
        'pwd' => '',
    );


    // Kontrola zda je prvni polozka informace o okne
    if(empty($params[0]['type'])) {
      $zaklad = array(
        'type' 		=> 'window',
        'bounds' 	=> pack('V*', 400, 47, 873, 900), // Pozice - left, top, right, bottom
        'list-col-0' => pack('V', 100),
        'list-col-1' => pack('V', 80),
        'list-col-2' => pack('V', 245),
      );
      array_unshift($params, $zaklad);
    }


    $data = $header;
    // Projdeme parametry
    foreach($params as $host) {

      if(isset($host['type'])) {
        // Nastaveni okna


      } else {
        // Co neni nastavime z defaultu
        foreach($default as $k=>$v) if(!isset($host[$k])) $host[$k] = $v;
      }

      // Ulozime parametry
      foreach($host as $name=>$value) {
        $pair = chr(strlen($name)) . $name . $value; // Vytvorime par
        $len = strlen($pair);

        $lenb = chr($len % 256) . chr($len / 256); // Delka binarne

        $data .= $lenb . $pair; // Pripojime
      }

      $data .= "\x00\x00"; // Ukonceni hostu
    }

    return $data;
  }
}