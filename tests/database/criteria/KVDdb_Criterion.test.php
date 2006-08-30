<?php
Mock::generate( 'KVDdb_SimpleQuery' );

class TestOfCriterion extends UnitTestCase
{
    public function testCriterionExists( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $this->assertNoErrors( );
    }

    public function testEqual( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie', 'West-Vlaanderen' );
        $this->assertIsA ( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ), "( provincie = 'West-Vlaanderen' )");
    }

    public function testMatch( )
    {
        $criterion = KVDdb_Criterion::matches( 'provincie', 'West-Vlaanderen' );
        $this->assertIsA ( $criterion , 'KVDdb_MatchCriterion' );
        $this->assertEqual( $criterion->generateSql( ), "( UPPER( provincie ) LIKE UPPER( 'West-Vlaanderen' ) )");
        
        $criterion = KVDdb_Criterion::matches( 'provincie', '%West-Vlaanderen' );
        $this->assertIsA ( $criterion , 'KVDdb_MatchCriterion' );
        $this->assertEqual( $criterion->generateSql( ), "( UPPER( provincie ) LIKE UPPER( '%West-Vlaanderen' ) )");
        
        $this->assertIsA ( $criterion , 'KVDdb_MatchCriterion' );
        $criterion = KVDdb_Criterion::matches( 'provincie', '__st-Vlaanderen' );
        $this->assertEqual( $criterion->generateSql( ), "( UPPER( provincie ) LIKE UPPER( '__st-Vlaanderen' ) )");
    }

    public function testGreaterThan( )
    {
        $criterion = KVDdb_Criterion::greaterThan( 'provincie', 40000 );
        $this->assertIsA ( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ), "( provincie > 40000 )");
    }

    public function testLessThan( )
    {
        $criterion = KVDdb_Criterion::lessThan( 'provincie', 40000 );
        $this->assertIsA ( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ), "( provincie < 40000 )");
    }

    public function testMultipleOr( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion->addOr( KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' ) );
        $this->assertEqual( $criterion->generateSql( ) , "( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' ) )" );
    }
    
    public function testMultipleAnd( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion->addAnd( KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' ) );
        $this->assertEqual( $criterion->generateSql( ) , "( provincie = 'West-Vlaanderen' AND ( provincie = 'Oost-Vlaanderen' ) )" );
    }

    public function testComplexCombination( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion2 = KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' );
        $criterion2->addAnd( KVDdb_Criterion::equals( 'gemeente' , 'Maldegem' ) );
        $criterion->addOr( $criterion2 );
        $this->assertEqual( $criterion->generateSql( ) , "( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' AND ( gemeente = 'Maldegem' ) ) )" );
    }

    public function testEqualBoolean( )
    {
        $criterion = KVDdb_Criterion::equals( 'gevonden', true );
        $this->assertIsA ( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ), "( gevonden = true )");
    }

    public function testIn( )
    {
        $criterion = KVDdb_Criterion::in( 'gemeente_id' , array( 40000, 40001, 40002 ) );
        $this->assertIsA ( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ), "( gemeente_id IN ( 40000, 40001, 40002 ) )");

        $criterion = KVDdb_Criterion::in( 'gemeente' , array( 'Knokke-Heist', 'Brugge', 'Dudzele' ) );
        $this->assertEqual( $criterion->generateSql( ), "( gemeente IN ( 'Knokke-Heist', 'Brugge', 'Dudzele' ) )");
    }

    public function testInSubselect( )
    {
        $subselect = new MockKVDdb_SimpleQuery( );
        $subselect->setReturnValue( 'generateSql' , 'SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001' );
        $subselect->expectOnce( 'generateSql' );
        $criterion = KVDdb_Criterion::inSubselect( 'gemeente_id' , $subselect );
        $this->assertIsA ( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ), "( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001 ) )");
        $subselect->tally( );
    }

    public function testIsNull( )
    {
        $criterion = KVDdb_Criterion::isNull( 'gevonden' );
        $this->assertIsA( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ) , '( gevonden IS NULL )' );
    }

    public function testIsNotNull( )
    {
        $criterion = KVDdb_Criterion::isNotNull( 'gevonden' );
        $this->assertIsA( $criterion , 'KVDdb_Criterion' );
        $this->assertEqual( $criterion->generateSql( ) , '( gevonden IS NOT NULL )' );
    }
    

}
