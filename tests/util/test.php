<?php
//echo spl_autoload_extensions('.class.php,.interface.php,.test.php');
echo __FILE__;
function KVD_autoload( $class ) {
    echo 'trying to autoload ' . $class;
}
spl_autoload_register( KVD_autoload );
$kvd = new KVD( );
?>
