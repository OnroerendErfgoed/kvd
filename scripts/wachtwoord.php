<?php
require_once( dirname(__FILE__) . DIRECTORY_SEPARATOR . '../vendor/autoload.php' );

$wg = new KVDutil_WachtwoordGenerator( );
echo $wg->generate( ) . "\n";
?>
