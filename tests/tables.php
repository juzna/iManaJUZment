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
$ds = Doctrine::getTable('AP')->findAll();

// Renderer
$rend = new Tables\SimpleRenderer($def, $ds);
//$rend->generateTemplateCode(); exit;

// Create output
echo $rend->render();

