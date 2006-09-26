<?php
class TestOfSqlLogger extends UnitTestCase
{
    public function testLogtNiet( )
    {
        $logger = new KVDdom_SqlLogger( );
        $this->assertEqual( $logger->log( 'SELECT * FROM TEST;' ) , false );
    }
}
?>
