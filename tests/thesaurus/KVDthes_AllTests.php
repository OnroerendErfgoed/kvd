<?php
if ( !defined( 'PHPUnit_MAIN_METHOD') ) {
    define( 'PHPUnit_MAIN_METHOD', 'KVDthes_AllTests::main');
}
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

if ( !defined( 'KVD_AUTOLOAD')) {
    define( 'KVD_AUTOLOAD', '../KVD_Autoload.php' );
}

require_once 'core/KVDthes_CoreAllTests.php';

class KVDthes_AllTests
{
    public static function main( )
    {
        PHPUnit_TextUI_TestRunner::run( self::suite( ));
    }
                             
    public static function suite( )
    {
        $suite = new PHPUnit_Framework_TestSuite( 'KVDthes All Tests' );
        
        $suite->addTestSuite( 'KVDthes_CoreAllTests');
        
        return $suite;
    }
}

if ( PHPUnit_MAIN_METHOD == 'KVDthes_AllTests::main') {
    KVDthes_AllTests::main( );
}
