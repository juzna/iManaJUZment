<?php

/**
* Class for drawing maps
* @package ServisWeb
* @subpackage Maps
*/

class MapCreator {
	/* @var array Aliasy pro ruzne barvy */
	private $colorAlias = array(
		'red'	=> 0x00FF0000, // Cervena
		'green'	=> 0x0000FF00, // Zelena
		'blue'	=> 0x000000FF, // Modra
		'white'	=> 0x00FFFFFF, // Bila
		'black'	=> 0x00000000, // Cerna
		'alblue'=> 0x600000FF, // Pruhledna modra
		'algreen'=> 0x6000FF00, // Pruhledna zelena
		'alred'  => 0x60FF0000, // Pruhledna cervena
	);

	/** @var int Sirka vytvorene mapy */
	public $width = 1280;

	/** @var int Vyska vytvorene mapy */
	public $height = 1280;

	/** @var int Pocet bodu mapy na jeden pixel na obrazku */
	public $pixelScale;

	/** @var int Posun mezi dvema obrazky na mapy.cz */
	public $posun;

	/** @var int Zoom (3 = cela evropa, 16 = maximalni detail) */
	public $zoom;

	/** @var int X souradnice stredu */
	public $posX;

	/** @var int Y Souradnice stredu */
	public $posY;

	/** @var float Pomer kolikrat se ma zvetsit nacteny obrazek */
	public $resampleZoom = 1;

	/** @var resource Resource obrazku */
	public $img;

	/** @var string Typ mapy, moznosti: base, ophoto */
	public $typ = 'base';

	/** @var array Seznam alokovanych barev */
	private $colors = array();

	/** @var int X souradnice leveho horniho rohu */
	public $startX;

	/** @var int Y Souradnice eveho horniho rohu */
	public $startY;

	/** @var int X souradnice praveho dolniho rohu */
	public $endX;

	/** @var int Y Souradnice praveho dolniho rohu */
	public $endY;


	/**
	* Vyrvoreni nove mapy
	* @param $posX int Stred - X
	* @param $posY int Stred - Y
	*/
	function __construct($posX, $posY, $zoom = 14) {
		$this->posX = $posX;
		$this->posY = $posY;

		// Nastavime zoom
		$this->setZoom($zoom);
	}

	/**
	* Nastavi zoom (3 = cela evropa, 16 = maximalni detail)
	* @param $zoom int Nova hodnoa
	*/
	function setZoom($zoom) {
		$this->zoom = $zoom;

		$this->posun = 1 << 28 - $zoom;
		$this->pixelScale = ($this->posun >> 8) / $this->resampleZoom;
	}

	/**
	* Zmeni zoom tak, aby sel dany bod videt
	*/
	function makeVisible($posX, $posY) {
		// Rozdil na osach
		$diffX = abs($this->posX - $posX);
		$diffY = abs($this->posY - $posY);

		$zoomX = 20 - log($diffX * 2 / $this->width * $this->resampleZoom, 2);
		$zoomY = 20 - log($diffY * 2 / $this->height * $this->resampleZoom, 2);

		$this->setZoom(min(floor($zoomX), floor($zoomY), $this->zoom));
	}

	/**
	* Vytvori obrazek
	*/
	function create() {
		$this->img = imagecreatetruecolor($this->width, $this->height);

		// Souradnice leveho horniho rohu mapy
		$this->startX = (int) ($this->posX - $this->width * $this->pixelScale / 2);
		$this->startY = (int) ($this->posY + $this->height * $this->pixelScale / 2);

		// Souradnice praveho dolniho rohu
		$this->endX = (int) ($this->posX + $this->width * $this->pixelScale / 2);
		$this->endY = (int) ($this->posY - $this->height * $this->pixelScale / 2);


		// Pozice od ktere nacitame z mapy.cz
		$startLoadX = $this->startX & ~ ($this->posun - 1);
		$startLoadY = ($this->startY & ~ ($this->posun - 1)) + $this->posun; // Musime pripocist posun, protoze pocitame souradnice naopak


		// Nacitame a malujeme
		for($y = 0, $dstY = 0, $loadY = $startLoadY, $actualY = $this->startY;
			$dstY < $this->height; // Dokud nejzme uplne na spodu naseho obrazku
			$actualY = $loadY -= $this->posun, $y++) {

			for($x = 0, $dstX = 0, $loadX = $startLoadX, $actualX = $this->startX;
				$dstX < $this->width; // Dokud nejzme uplne vpravo naseho obrazku
				$actualX = $loadX += $this->posun, $x++) {

				// Nacteme ctverec
				$imgPart = $this->load($loadX, $loadY);

				// Souradnice ve zdrojovem obrazku
				$srcX = (int) (($actualX - $loadX) / $this->pixelScale);
				$srcY = - (int) (($actualY - $loadY) / $this->pixelScale);
				$srcW = min(256 - $srcX, ($this->width - $dstX) / $this->resampleZoom);
				$srcH = min(256 - $srcY, ($this->height - $dstY) / $this->resampleZoom);


				$dstW = (int) ($srcW * $this->resampleZoom);
				$dstH = (int) ($srcH * $this->resampleZoom);

				// Prekoprujeme
				if($imgPart) {
					if($this->resampleZoom == 1) imagecopy($this->img, $imgPart, $dstX, $dstY, $srcX, $srcY, $srcW, $srcH);
					else imagecopyresampled($this->img, $imgPart, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);
					//imagecopyresized($this->img, $imgPart, $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH);

					imagedestroy($imgPart);
				}

//				echo "$x $y: copy: $dstX, $dstY, $srcX, $srcY, $dstW, $dstH, $srcW, $srcH\n";


				// Uvolnime pamet

				$dstX += $dstW;
			}

			$dstY += $dstH;
		}

		// Alokujeme barvy
		$color_red = imagecolorallocate($this->img, 0xFF, 0x00, 0x00);
	}

	/**
	* Zobrazi zdrojovou pozici na mape
	*/
	function meRect($color = 'red', $size = 6, $filled = true) {
		$this->rect($this->posX, $this->posY, $color, $size, $filled);
	}


	/**
	* Nacte obrazek z mapy.cz
	*/
	private function load($posX, $posY) {
		// My pocitame souradnice od vrchu
		$posY -= $this->posun;

		// Nacteme kus
		$file = sprintf('%d_%x_%x', $this->zoom, $posX, $posY);

		// Kontrola cache, kdyztak sosneme
		if(!file_exists($path = TMP_DIR . "/mapa_cache/$this->typ/$file")) {
			$url = "http://mapserver.mapy.cz/$this->typ/$file";
			$data = file_get_contents($url);

			if(strlen($data) > 1000) {
				mkdir2(dirname($path));
				file_put_contents($path, $data);
			}
		}

		// Create
		if(file_exists($path)) return imagecreatefromgif($path);
	}


	/**
	* Provede vystup obrazku
	* @param $file string Nazev souboru (nebo false -> echuje)
	*/
	function output($file = false) {
		if(!$file) {
			imagepng($this->img);
		}

		else {
			imagepng($this->img, $file);
		}
	}

	/**
	* Vrati resource dane barvy
	*/
	private function getColor($name) {
		// Barva je jiz alokovana
		if(isset($this->colors[$name])) return $this->colors[$name];

		// Hledame alias
		if(!isset($this->colorAlias[$name])) throw new Exception("Alias barvy $name neni definovan");
		$alias = $this->colorAlias[$name];

		// Alokujeme
		return $this->allocateColor($name, ($alias >> 16) & 255, ($alias >> 8) & 255, $alias & 255, ($alias >> 24) & 255);
	}

	/**
	* Alokujeme novou barvu
	*/
	private function allocateColor($name, $red, $green, $blue, $alpha = 0) {
		// Barva je jiz alokovana
		if(isset($this->colors[$name])) return $this->colors[$name];

		// Je to pruhledna barva
		if($alpha) {
			$ret = imagecolorallocatealpha($this->img, $red, $green, $blue, $alpha);
		} else {
			$ret = imagecolorallocate($this->img, $red, $green, $blue);
		}

		return $this->colors[$name] = $ret;
	}

	/**
	* Prepocet bodu X na souradnice v obrazku
	*/
	private function posX($posX) {
		return ($posX - $this->startX) / $this->pixelScale;
	}

	/**
	* Prepocet bodu Y na souradnice v obrazku
	*/
	private function posY($posY) {
		return - ($posY - $this->startY) / $this->pixelScale;
	}

	/**
	* Nakresli ctverecek
	*/
	function rect($posX, $posY, $color, $size = 2, $filled = false) {
		// Prepocitame na souradnice v obrazu
		$posX = $this->posX($posX);
		$posY = $this->posY($posY);

		// Kontrola zda je ve vykreslitelne oblasti
		if($posX < 0 || $posX > $this->width || $posY < 0 || $posY > $this->height) return false;


		// Vykreslime
		$fce = $filled ? 'imagefilledrectangle' : 'imagerectangle';
		$sizePosun = (int) ($size / 2);
		return $fce($this->img, $posX - $sizePosun, $posY - $sizePosun, $posX + $sizePosun, $posY + $sizePosun, $this->getColor($color));
	}

	/**
	* Prevede pozici na mape na pozici v obrazku
	*/
	function getPos($posX, $posY) {
		$posX = $this->posX($posX);
		$posY = $this->posY($posY);

		return array($posX, $posY);
	}


	/**
	* Namaluje caru
	*/
	function line($x1, $y1, $x2, $y2, $color) {
		// Prepocitame na souradnice v obrazu
		$x1 = $this->posX($x1);
		$y1 = $this->posY($y1);
		$x2 = $this->posX($x2);
		$y2 = $this->posY($y2);

		return imageline($this->img, $x1, $y1, $x2, $y2, $this->getColor($color));
	}

	/**
	* Namaluje caru do stredu
	*/
	function lineStred($x, $y, $color) {
		$this->line($this->posX, $this->posY, $x, $y, $color);
	}

	/**
	* Zda je dany bod v nasi mape viditelny
	*/
	function isVisible($posX, $posY) {
		return (bool) ($this->startX < $posX && $this->endX > $posX && $this->startY > $posY && $this->endY < $posY);
	}

	/**
	* Vykresli sektor
	*/
	function filledArc($posX, $posY, $width, $height, $startAngle, $endAngle, $color, $styl = IMG_ARC_PIE) {
		// Ohraniceni
		imagearc($this->img, $startX = $this->posX($posX), $startY = $this->posY($posY), $w = $width / $this->pixelScale, $h = $height / $this->pixelScale, $startAngle, $endAngle, $black = $this->getColor('black'));

		// Jedna lajna
		$endX = $startX + cos(deg2rad($startAngle)) * $w / 2;
		$endY = $startY + sin(deg2rad($startAngle)) * $h / 2;
		imageline($this->img, $startX, $startY, $endX, $endY, $black);


		// Druha lajna
		$endX = $startX + cos(deg2rad($endAngle)) * $w / 2;
		$endY = $startY + sin(deg2rad($endAngle)) * $h / 2;
		imageline($this->img, $startX, $startY, $endX, $endY, $black);


		// Sektor
		$ret = imagefilledarc($this->img, $this->posX($posX), $this->posY($posY), $width / $this->pixelScale, $height / $this->pixelScale, $startAngle, $endAngle, $this->getColor($color), IMG_ARC_PIE);
		return $ret;
	}
}
