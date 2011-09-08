<?php


class KVDdom_SqlLoggerTest extends PHPUnit_Framework_TestCase
{
    public function testLogtNiet( )
    {
        $logger = new KVDdom_SqlLogger( );
        $this->assertEquals( false, $logger->log( 'SELECT * FROM TEST;' ) );
    }
}
?>
