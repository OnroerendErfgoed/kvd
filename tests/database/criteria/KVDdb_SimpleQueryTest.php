<?php
require_once( 'PHPUnit/Framework.php' );

class KVDdb_SimpleQueryTest extends PHPUnit_Framework_TestCase
{
    public function testExists( )
    {
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id') , 'gemeente' );
        $this->assertType( 'KVDdb_SimpleQuery', $query );
    }

    public function testWithoutCriteria( )
    {
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente' );
        $this->assertFalse( $query->hasJoins( ) );
    }

    public function testWithCriteria( )
    {
        $criteria = $this->getMock( 'KVDdb_Criteria' );
        $criteria->expects($this->once())->method( 'generateSql' )->will( $this->returnValue('WHERE ( provincie = 20001 )') );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $criteria );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente WHERE ( provincie = 20001 )' );
        $this->assertFalse( $query->hasJoins( ) );
    }

    public function testWithJoin( )
    {
        $join = $this->getMock( 'KVDdb_Join', array(), array( 'provincie', array(array( 'gemeente.provincie_id','provincie.id')),KVDdb_Join::LEFT_JOIN ) );
        $join->expects($this->once())->method( 'generateSql' )->will( $this->returnValue( 'LEFT JOIN provincie ON (gemeente.provincie_id = provincie.id)'));
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' );
        $this->assertFalse( $query->hasJoins( ) );
        $query->addJoin( $join );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente LEFT JOIN provincie ON (gemeente.provincie_id = provincie.id)' );
        $this->assertTrue( $query->hasJoins( ) );
    }
}

class SimpleQueryWithCriteriaTest extends PHPUnit_Framework_TestCase
{
    public function testSimple( )
    {
        $criteria = new KVDdb_Criteria( );
        $criteria->add( KVDdb_Criterion::equals( 'provincie_id' , 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' , 'gemeente' ) , 'gemeente' , $criteria );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id, gemeente FROM gemeente WHERE ( provincie_id = 20001 )' );
        $this->assertFalse( $query->hasJoins( ) );
    }
    
    public function testComplex( )
    {
        $criteria = new KVDdb_Criteria( );
        $criteria->add( KVDdb_Criterion::equals( 'provincie_id' , 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $criteria );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 )' );

        $criterion = KVDdb_Criterion::inSubselect( 'gemeente_id' , $query );
        $this->assertEquals ( $criterion->generateSql( ) , '( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 ) ) )' );

        $criteria2 = new KVDdb_Criteria( );
        $criteria2->add( $criterion );
        $this->assertEquals ( $criteria2->generateSql( ) , 'WHERE ( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 ) ) )' );
        $this->assertFalse( $query->hasJoins( ) );
    }
}


class SimpleQueryWithJoinTest extends PHPUnit_Framework_TestCase
{
    public function testSimple( )
    {
        $join = new KVDdb_Join( 'provincie', array ( array( 'gemeente.provincie_id', 'provincie.id') ), KVDdb_Join::LEFT_JOIN );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' );
        $this->assertType( 'KVDdb_SimpleQuery', $query );
        $this->assertFalse( $query->hasJoins( ) );
        $query->addJoin( $join );
        $this->assertEquals ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente LEFT JOIN provincie ON (gemeente.provincie_id = provincie.id)' );
        $this->assertTrue( $query->hasJoins( ) );
    }
}
?>
