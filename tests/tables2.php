<?php
define('APP_DIR', __DIR__ . '/../app');
define('LIBS_DIR', __DIR__ . '/../libs');

require_once APP_DIR . '/bootstrap-common.php';

// Input parameters
$variables = array();
$model = @$_GET['model'] or $model = 'AP';
if(isset($_GET['vars'])) foreach(explode(',', $_GET['vars']) as $var) {
  if(!$var = trim($var)) continue;
  $variables[$var] = $_GET[$var];
}

// Definition
$def = new Tables\DoctrineEntityTableDefinition($model);

// Datasource
$ds = null; //Doctrine::getTable('AP')->findAll();

// Renderer
$rend = new Tables\SimpleRenderer($def, $ds, $variables);
//$rend->generateTemplateCode(); exit;

// Create output
echo $rend->render();

//echo '<pre>'; print_r($rend->getDataSource());
