<?php
require_once 'PHPUnit/Framework.php';

class KVDdb_JoinTest extends PHPUnit_Framework_TestCase
{
    public function testExists( )
    {
        $join = new KVDdb_Join( 'gemeente', array( array( 'locatie.gemeente_id', 'gemeente.id' ) ), KVDdb_Join::LEFT_JOIN );
        $this->assertType( 'KVDdb_Join', $join );
    }

    public function testLeftJoin( )
    {
        $join = new KVDdb_Join( 'gemeente', array( array( 'locatie.gemeente_id', 'gemeente.id' ) ), KVDdb_Join::LEFT_JOIN );
        $this->assertEquals( $join->generateSql( ), 'LEFT JOIN gemeente ON (locatie.gemeente_id = gemeente.id)');
    }

    /**
     * testNoFields 
     * 
     * @expectedException   InvalidArgumentException
     * @return void
     */
    public function testNoFields( )
    {
        $join = new KVDdb_Join( 'gemeente', array(), KVDdb_Join::LEFT_JOIN );
    }

    /**
     * testIllegalJoinType 
     * 
     * @expectedException   InvalidArgumentException
     * @return void
     */
    public function testIllegalJoinType( )
    {
        $join = new KVDdb_Join( 'gemeente', array( array( 'locatie.gemeente_id', 'gemeente.id' ) ), 'ILLEGAL JOIN' );
    }

    /**
     * testVeldpaarBevatExactTweeVelden 
     * 
     * @expectedException   InvalidArgumentException
     * @return void
     */
    public function testVeldpaarBevatExactTweeVelden( )
    {
        $join = new KVDdb_Join( 'gemeente', array( array( 'locatie.gemeente_id', 'gemeente.id', 'gemeente.provincie_id' ) ), KVDdb_Join::LEFT_JOIN );
    }

}
?>
