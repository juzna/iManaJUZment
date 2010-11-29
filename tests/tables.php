<?php
define('APP_DIR', __DIR__ . '/../app');
define('LIBS_DIR', __DIR__ . '/../libs');

require_once APP_DIR . '/bootstrap-common.php';

// Definition
$path = APP_DIR . '/APModule/tables/list.xml';
$def = new Tables\XMLTableDefinition($path);
$def->load();

//print_r($def); exit;

// Datasource
//print_r(APIP::getClassMetadata()); echo "AHOJ";  exit;


$ds = AP::findAll();
//$ds = $em->getRepository('AP')->findAll();
//echo '<pre>'; print_r($ds); echo '</pre>';

// Renderer
$rend = new Tables\SimpleRenderer($def, $ds);
echo "<!--\n\n"; $rend->generateTemplateCode(); echo "\n-->\n\n";

// Create output
echo $rend->render();

