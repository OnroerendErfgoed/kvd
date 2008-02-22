<?php
if ( !defined( 'PHPUnit_MAIN_METHOD') ) {
    define( 'PHPUnit_MAIN_METHOD', 'KVDthes_CoreAllTests::main');
}
 
require_once 'PHPUnit/Framework.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'KVDthes_RelationTest.php';
require_once 'KVDthes_RelationsTest.php';
require_once 'KVDthes_RelationsIteratorTest.php';

class KVDthes_CoreAllTests
{
    public static function main( )
    {
        PHPUnit_TextUI_TestRunner::run( self::suite( ));
    }
                             
    public static function suite( )
    {
        $suite = new PHPUnit_Framework_TestSuite( 'KVDthes Core' );
        
        $suite->addTestSuite( 'KVDthes_RelationTest');
        $suite->addTestSuite( 'KVDthes_RelationsTest');
        $suite->addTestSuite( 'KVDthes_RelationsIteratorTest');
        
        return $suite;
    }
}

if ( PHPUnit_MAIN_METHOD == 'KVDthes_CoreAllTests::main') {
    KVDthes_CoreAllTests::main( );
}
