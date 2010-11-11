<?php

// Browser capabilities check
if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE')) {
  if(strpos($_SERVER['HTTP_USER_AGENT'], 'chromeframe')) define('IE', 1);
  else {
    header('Location: browser/', 302);
    die("Vas prohlizec neni podporovan, postupujte podle navodu na browser/index.php");
  }
}


// absolute filesystem path to the web root
define('WWW_DIR', dirname(__FILE__));

// absolute filesystem path to the application root
define('APP_DIR', WWW_DIR . '/../app');

// absolute filesystem path to the libraries
define('LIBS_DIR', WWW_DIR . '/../libs');

// load bootstrap file
require APP_DIR . '/bootstrap-web.php';
