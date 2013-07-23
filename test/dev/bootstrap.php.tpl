<?php

error_reporting( E_ALL | E_STRICT );

require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . '../vendor/autoload.php' );

define('CRAB_USER', '@@CRAB_USER@@');
define('CRAB_PWD', '@@CRAB_PWD@@');

define('CRAB_PROXY_HOST', '@@CRAB_PROXY_HOST@@');
define('CRAB_PROXY_PORT', '@@CRAB_PROXY_PORT@@');

define('OSM_PROXY_HOST', '@@OSM_PROXY_HOST@@');
define('OSM_PROXY_PORT', '@@OSM_PROXY_PORT@@');

?>
