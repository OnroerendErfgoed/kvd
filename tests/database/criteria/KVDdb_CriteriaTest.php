<?php
require_once( 'PHPUnit/Framework.php' );

Error_Reporting( E_ALL );

class KVDdb_CriteriaTest extends PHPUnit_Framework_TestCase
{
    private $criteria;
    
    public function setUp( )
    {
        $this->criteria = new KVDdb_Criteria( );
        $this->criterion_one = KVDdb_Criterion::equals( 'provincie', 40000 );
        $this->criterion_two = KVDdb_Criterion::matches( 'naam', '%huis%' );
    }

    public function tearDown( )
    {
        $this->criteria = null;
    }

    public function testEmpty( )
    {
        $this->assertEquals( '', $this->criteria->generateSql(  ) );
    }
    
    public function testAddCriteria( )
    {
        $this->criteria->add( $this->criterion_one );
        $this->assertEquals ( 1, count( $this->criteria ) );
        $this->assertTrue( $this->criteria->hasCriteria( ) );
        $this->assertTrue( $this->criteria->hasCriteria( 'provincie' ) );
    }

    public function testGenerateSqlOneCriterion( )
    {
        $this->criteria->add( $this->criterion_one );
        $this->assertEquals( 'WHERE ( provincie = 40000 )', $this->criteria->generateSql() );
    }

    public function testGenerateSqlMultipleCriteria( )
    {
        $this->criteria->add( $this->criterion_one );
        
        $this->criteria->add( $this->criterion_two );

        $this->assertEquals( "WHERE ( provincie = 40000 ) AND ( UPPER( naam ) LIKE UPPER( '%huis%' ) )", $this->criteria->generateSql());
    }

    public function testGenerateSqlEmptyCriteria( )
    {
        $this->assertEquals( '', $this->criteria->generateSql( ) );
    }

    public function testGenerateSqlEmptyCriteriaWithSort( )
    {
        $this->criteria->addAscendingOrder( 'provincie' );
        $this->assertEquals( 'ORDER BY provincie ASC', $this->criteria->generateSql() );
        $this->assertTrue( $this->criteria->hasOrder( ) );
    }

    public function testGenerateSqlWithMultipleOrder( )
    {
        $this->criteria->add( $this->criterion_two );
        
        $this->criteria->addDescendingOrder( 'provincie' );
        $this->criteria->addAscendingOrder( 'gemeente' );
        
        $this->assertEquals( "WHERE ( UPPER( naam ) LIKE UPPER( '%huis%' ) ) ORDER BY provincie DESC , gemeente ASC", $this->criteria->generateSql( ) );
    }

    public function testClearOrder( )
    {
        $this->criteria->addAscendingOrder( 'provincie' );
        $this->assertEquals( 'ORDER BY provincie ASC', $this->criteria->generateSql() );
        $this->criteria->clearOrder( );
        $this->assertEquals( '', $this->criteria->generateSql()  );
    }
    
}


class CriteriaIntegrationTest extends PHPUnit_Framework_TestCase
{
    private $criteria;
    
    public function setUp( )
    {
        $this->criteria = new KVDdb_Criteria( );
    }

    public function tearDown( )
    {
        $this->criteria = null;
    }

    public function testSimpleIntegration( )
    {
        $this->criteria->add( KVDdb_Criterion::equals ( 'provincie' , 'West-Vlaanderen' ) );
        $this->criteria->add( KVDdb_Criterion::equals ( 'gemeente' , 'Knokke-Heist' ) );
        $this->assertEquals( "WHERE ( provincie = 'West-Vlaanderen' ) AND ( gemeente = 'Knokke-Heist' )", $this->criteria->generateSql() );
    }

    public function testComplexCombination( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion2 = KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' );
        $criterion2->addAnd( KVDdb_Criterion::equals( 'gemeente' , 'Maldegem' ) );
        $criterion->addOr( $criterion2 );
        $this->assertEquals( "( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' AND ( gemeente = 'Maldegem' ) ) )", $criterion->generateSql() );
        
        $this->criteria->add( $criterion );
        
        $this->criteria->add( KVDdb_Criterion::matches ( 'naam' , '%berg%' ) );
        $this->assertEquals( "WHERE ( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' AND ( gemeente = 'Maldegem' ) ) ) AND ( UPPER( naam ) LIKE UPPER( '%berg%' ) )", $this->criteria->generateSql() );
    }
    
    public function testParameterized( )
    {
        $this->criteria->add( KVDdb_Criterion::equals ( 'provincie' , 'West-Vlaanderen' ) );
        $this->criteria->add( KVDdb_Criterion::equals ( 'gemeente' , 'Knokke-Heist' ) );
        $this->assertEquals( "WHERE ( provincie = ? ) AND ( gemeente = ? )", $this->criteria->generateSql( KVDdb_Criteria::MODE_PARAMETERIZED ) );
        $this->assertEquals( array ( 'West-Vlaanderen' , 'Knokke-Heist' ), $this->criteria->getValues()  );
    }

    /**
     * testParameterizedWithSubSelect 
     *
     * Test een geparameterizeerde subselect. Tot voor kort (zie ticket 363 in de OEPS applicatie) bevatte het getValues( ) array een SimpleQuery object.
     * Probleem zou moeten opgelost zijn, een InSubSelect geeft enkel nog de values van zijn children terug.
     * @return void
     */
    public function testParameterizedWithSubSelect( )
    {
        $crit = new KVDdb_Criteria( );
        $crit->add( KVDdb_Criterion::equals ( 'provincie_id', 20001 ) );
        $query = new KVDdb_SimpleQuery( array( 'gemeente_id' ) , 'gemeente' , $crit );
        $criterion = KVDdb_Criterion::inSubselect( 'gemeente_id', $query);
        $this->criteria->add( $criterion );
        $this->assertEquals("WHERE ( gemeente_id IN ( SELECT gemeente_id FROM gemeente WHERE ( provincie_id = 20001 ) ) )",  $this->criteria->generateSql( KVDdb_Criteria::MODE_PARAMETERIZED ) );
        $this->assertEquals( array( ), $this->criteria->getValues() );
    }

}
?>
