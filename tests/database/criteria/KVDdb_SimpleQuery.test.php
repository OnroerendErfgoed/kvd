<?php
Mock::generate( 'KVDdb_Criteria' );

class TestOfSimpleQuery extends UnitTestCase
{
    public function testExists( )
    {
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id') , 'gemeente' );
        $this->assertIsA( $query , 'KVDdb_SimpleQuery' );
    }

    public function testWithoutCriteria( )
    {
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' );
        $this->assertIsA( $query , 'KVDdb_SimpleQuery' );
        $this->assertEqual ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente' );
    }

    public function testWithCriteria( )
    {
        $criteria = new MockKVDdb_Criteria( );
        $criteria->setReturnValue( 'generateSql' , 'WHERE ( provincie = 20001 )');
        $criteria->expectOnce( 'generateSql' );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $criteria );
        $this->assertIsA( $query , 'KVDdb_SimpleQuery' );
        $this->assertEqual ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente WHERE ( provincie = 20001 )' );
        $criteria->tally( );
    }

}

class TestOfSimpleQueryWithCriteria extends UnitTestCase
{
    public function testSimple( )
    {
        $criteria = new KVDdb_Criteria( );
        $criteria->add( KVDdb_Criterion::equals( 'provincie_id' , 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' , 'gemeente' ) , 'gemeente' , $criteria );
        $this->assertIsA( $query, 'KVDdb_SimpleQuery' );
        $this->assertEqual ( $query->generateSql( ) , 'SELECT gemeente_id, gemeente FROM gemeente WHERE ( provincie_id = 20001 )' );
    }

    public function testComplex( )
    {
        $criteria = new KVDdb_Criteria( );
        $criteria->add( KVDdb_Criterion::equals( 'provincie_id' , 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $criteria );
        $this->assertIsA( $query, 'KVDdb_SimpleQuery' );
        $this->assertEqual ( $query->generateSql( ) , 'SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 )' );

        $criterion = KVDdb_Criterion::inSubselect( 'gemeente_id' , $query );
        $this->assertIsA( $criterion, 'KVDdb_Criterion' );
        $this->assertEqual ( $criterion->generateSql( ) , '( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 ) ) )' );

        $criteria2 = new KVDdb_Criteria( );
        $criteria2->add( $criterion );
        $this->assertEqual ( $criteria2->generateSql( ) , 'WHERE ( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 ) ) )' );
    }
}
?>
