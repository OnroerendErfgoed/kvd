<?php
class TestOfJoin extends UnitTestCase
{
    public function testExists( )
    {
        $join = new KVDdb_Join( 'gemeente', array( array( 'locatie.gemeente_id', 'gemeente.id' ) ), KVDdb_Join::LEFT_JOIN );
        $this->assertIsA( $query , 'KVDdb_Join' );
    }

    public function testLeftJoin( )
    {
        $join = new KVDdb_Join( 'gemeente', array( array( 'locatie.gemeente_id', 'gemeente.id' ) ), KVDdb_Join::LEFT_JOIN );
        $this->assertEquals( $join->generateSql( ), 'LEFT JOIN gemeente ON (locatie.gemeente_id = gemeente.id)');
    }

    public function testNoFields( )
    {
        try {
            $join = new KVDdb_Join( 'gemeente', array(), KVDdb_Join::LEFT_JOIN );
        } catch ( InvalidArgumentException $e ) {
            $this->pass( );
        } catch ( Exception $e ) {
            $this->fail( 'Er werd een InvalidArgumentException verwacht, maar een andere exception ontvangen.' );
        }
        $this->fail( 'Doorgeven van lege fields zou een Exception moeten genereren.' );
    }

    public function testIllegalJoinType( )
    {

        try {
            $join = new KVDdb_Join( 'gemeente', array( array( 'locatie.gemeente_id', 'gemeente.id' ) ), KVDdb_Join::ILLEGAL_JOIN );
        } catch ( InvalidArgumentException $e ) {
            $this->pass( );
        } catch ( Exception $e ) {
            $this->fail( 'Er werd een InvalidArgumentException verwacht, maar een andere exception ontvangen.' );
        }
        $this->fail( 'Doorgeven van een illegaal join type zou een Exception moeten genereren.' );
    }

}
?>
