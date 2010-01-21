<?php
require_once( 'PHPUnit/Framework.php' );

class KVDdb_SqlTest extends PHPUnit_Framework_TestCase
{
    public function testExists( )
    {
        $query = new KVDdb_SqlQuery( 'SELECT * FROM provincie');
        $this->assertType( 'KVDdb_SqlQuery', $query );
    }

    public function testGenerateSql(  )
    {
        $query = new KVDdb_SqlQuery( 'SELECT * FROM provincie');
        $this->assertEquals( 'SELECT * FROM provincie', $query->generateSql() );
    }
}
?>
