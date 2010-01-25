<?php

require_once ( 'PHPUnit/Framework.php' );

class CriterionTest extends PHPUnit_Framework_TestCase
{
    public function testCriterionExists( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $this->assertType( 'KVDdb_Criterion', $criterion );
    }

    public function testEquals( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie', 'West-Vlaanderen' );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals( "( provincie = 'West-Vlaanderen' )", $criterion->generateSql( ));
    }

    public function testNotEquals( )
    {
        $criterion = KVDdb_Criterion::notEquals( 'provincie', 'West-Vlaanderen' );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals( "( provincie <> 'West-Vlaanderen' )", $criterion->generateSql( ));
    }

    public function testMatch( )
    {
        $criterion = KVDdb_Criterion::matches( 'provincie', 'West-Vlaanderen' );
        $this->assertType ( 'KVDdb_MatchCriterion', $criterion );
        $this->assertEquals( "( UPPER( provincie ) LIKE UPPER( 'West-Vlaanderen' ) )", $criterion->generateSql( ) );
        
        $criterion = KVDdb_Criterion::matches( 'provincie', '%West-Vlaanderen' );
        $this->assertType ( 'KVDdb_MatchCriterion', $criterion );
        $this->assertEquals( "( UPPER( provincie ) LIKE UPPER( '%West-Vlaanderen' ) )", $criterion->generateSql( ) );
        
        $criterion = KVDdb_Criterion::matches( 'provincie', '__st-Vlaanderen' );
        $this->assertType ( 'KVDdb_MatchCriterion', $criterion );
        $this->assertEquals( "( UPPER( provincie ) LIKE UPPER( '__st-Vlaanderen' ) )", $criterion->generateSql( ) );
    }

    public function testGreaterThan( )
    {
        $criterion = KVDdb_Criterion::greaterThan( 'provincie', 40000 );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals("( provincie > 40000 )", $criterion->generateSql( ));
    }

    public function testLessThan( )
    {
        $criterion = KVDdb_Criterion::lessThan( 'provincie', 40000 );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals("( provincie < 40000 )", $criterion->generateSql( ) );
    }

    public function testMultipleOr( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion->addOr( KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' ) );
        $this->assertEquals( "( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' ) )", $criterion->generateSql( ));
    }
    
    public function testMultipleAnd( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion->addAnd( KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' ) );
        $this->assertEquals("( provincie = 'West-Vlaanderen' AND ( provincie = 'Oost-Vlaanderen' ) )", $criterion->generateSql( ));
    }

    public function testComplexCombination( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion2 = KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' );
        $criterion2->addAnd( KVDdb_Criterion::equals( 'gemeente' , 'Maldegem' ) );
        $criterion->addOr( $criterion2 );
        $this->assertEquals(  "( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' AND ( gemeente = 'Maldegem' ) ) )", $criterion->generateSql( ) );
    }

    public function testEqualBoolean( )
    {
        $criterion = KVDdb_Criterion::equals( 'gevonden', true );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals( "( gevonden = true )", $criterion->generateSql() );
    }

    public function testIn( )
    {
        $criterion = KVDdb_Criterion::in( 'gemeente_id' , array( 40000, 40001, 40002 ) );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals( "( gemeente_id IN ( 40000, 40001, 40002 ) )", $criterion->generateSql( ) );

        $criterion = KVDdb_Criterion::in( 'gemeente' , array( 'Knokke-Heist', 'Brugge', 'Dudzele' ) );
        $this->assertEquals( "( gemeente IN ( 'Knokke-Heist', 'Brugge', 'Dudzele' ) )",  $criterion->generateSql( ) );
    }

    public function testNotIn( )
    {
        $criterion = KVDdb_Criterion::notIn( 'gemeente_id' , array( 40000, 40001, 40002 ) );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals(  "( gemeente_id NOT IN ( 40000, 40001, 40002 ) )", $criterion->generateSql( ));

        $criterion = KVDdb_Criterion::notIn( 'gemeente' , array( 'Knokke-Heist', 'Brugge', 'Dudzele' ) );
        $this->assertEquals(  "( gemeente NOT IN ( 'Knokke-Heist', 'Brugge', 'Dudzele' ) )", $criterion->generateSql( ) );
    }

    public function testInSubselect( )
    {
        $subselect = new KVDdb_SqlQuery('SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001' );
        $criterion = KVDdb_Criterion::inSubselect( 'gemeente_id' , $subselect );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals( "( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001 ) )", $criterion->generateSql( ));
    }

    public function testNotInSubselect( )
    {
        $subselect = new KVDdb_SqlQuery('SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001' );
        $criterion = KVDdb_Criterion::notInSubselect( 'gemeente_id' , $subselect );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals(  "( gemeente_id NOT IN ( SELECT gemeente_id FROM gemeente WHERE provincie_id = 20001 ) )", $criterion->generateSql( ) );
    }

    public function testIsNull( )
    {
        $criterion = KVDdb_Criterion::isNull( 'gevonden' );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals( '( gevonden IS NULL )',  $criterion->generateSql( ) );
        $this->assertEquals( array( ), $criterion->getValues( ) );
    }

    public function testIsNotNull( )
    {
        $criterion = KVDdb_Criterion::isNotNull( 'gevonden' );
        $this->assertType ( 'KVDdb_Criterion', $criterion );
        $this->assertEquals( '( gevonden IS NOT NULL )', $criterion->generateSql( ));
        $this->assertEquals( array( ), $criterion->getValues( ) );
    }

    public function testGetValues( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $this->assertEquals( array ( 'West-Vlaanderen' ), $criterion->getValues( ) );

        $criterion->addOr( KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' ) );
        $this->assertEquals ( array ( 'West-Vlaanderen', 'Oost-Vlaanderen' ), $criterion->getValues( ) );

        $criterion->addOr( KVDdb_Criterion::in ( 'gemeente' , array( 'Overijse', 'Diest', 'Zemst', 'Vilvoorde' ) ) );
        $this->assertEquals ( array ( 'West-Vlaanderen', 'Oost-Vlaanderen', 'Overijse', 'Diest', 'Zemst', 'Vilvoorde' ), $criterion->getValues( ) );
    }

    public function testGetPreparedStatement( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $this->assertEquals( "( provincie = ? )", $criterion->generateSql(KVDdb_Criteria::MODE_PARAMETERIZED ) );


        $criterion->addOr( KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' ) );
        $this->assertEquals( "( provincie = ? OR ( provincie = ? ) )", $criterion->generateSql(KVDdb_Criteria::MODE_PARAMETERIZED)  );
    }

    public function testGetFields( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $this->assertEquals( array( 'provincie' ), $criterion->getFields() );

        $criterion->addOr( KVDdb_Criterion::equals ( 'gemeente' , 'Gent' ) );
        $this->assertEquals( array( 'provincie', 'gemeente' ), $criterion->getFields() );
    }

}