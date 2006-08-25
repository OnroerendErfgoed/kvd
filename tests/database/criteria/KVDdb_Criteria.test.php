<?php
Mock::generate( 'KVDdb_Criterion' );

class TestOfCriteria extends UnitTestCase
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
    
    public function testAddCriteria( )
    {
        $criterion = new MockKVDdb_Criterion( );
        $this->criteria->add( $criterion );
        $this->assertEqual ( count( $this->criteria ) , 1 );
    }

    public function testGenerateSqlOneCriterion( )
    {
        $criterion = new MockKVDdb_Criterion( $this );
        $criterion->setReturnValue( 'generateSql' , 'provincie = 40000');
        $criterion->expectOnce( 'generateSql' );

        $this->criteria->add( $criterion );
        $this->assertEqual( $this->criteria->generateSql( ) , 'WHERE provincie = 40000');
        $criterion->tally( ); 
    }

    public function testGenerateSqlMultipleCriteria( )
    {
        $criterion = new MockKVDdb_Criterion( $this );
        $criterion->setReturnValue( 'generateSql' , 'provincie = 40000');
        $criterion->expectOnce( 'generateSql' );
        $this->criteria->add( $criterion );
        
        $criterion2 = new MockKVDdb_Criterion( $this );
        $criterion2->setReturnValue( 'generateSql' , "naam LIKE '%huis%'");
        $criterion2->expectOnce( 'generateSql' );
        $this->criteria->add( $criterion2 );

        $this->assertEqual( $this->criteria->generateSql( ) , "WHERE provincie = 40000 AND naam LIKE '%huis%'");
        $criterion->tally( ); 
        $criterion2->tally( );
    }

    public function testGenerateSqlEmptyCriteria( )
    {
        $this->assertEqual( $this->criteria->generateSql( ), '' );
    }

    public function testGenerateSqlEmptyCriteriaWithSort( )
    {
        $this->criteria->addAscendingOrder( 'provincie' );
        $this->assertEqual( $this->criteria->generateSql( ), 'ORDER BY provincie ASC' );
    }

    public function testGenerateSqlWithMultipleOrder( )
    {
        $criterion = new MockKVDdb_Criterion( $this );
        $criterion->setReturnValue( 'generateSql' , "naam LIKE '%huis%'");
        $criterion->expectOnce( 'generateSql' );
        $this->criteria->add( $criterion );
        
        $this->criteria->addDescendingOrder( 'provincie' );
        $this->criteria->addAscendingOrder( 'gemeente' );
        
        $this->assertEqual( $this->criteria->generateSql( ), "WHERE naam LIKE '%huis%' ORDER BY provincie DESC , gemeente ASC" );
        
        $criterion->tally( );
    }

    public function testClearOrder( )
    {
        $this->criteria->addAscendingOrder( 'provincie' );
        $this->assertEqual( $this->criteria->generateSql( ), 'ORDER BY provincie ASC' );
        $this->criteria->clearOrder( );
        $this->assertEqual( $this->criteria->generateSql( ), '' );
    }
    
}

class TestOfCriteriaWithCriterion extends UnitTestCase
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
        $this->assertEqual( $this->criteria->generateSql( ) , "WHERE ( provincie = 'West-Vlaanderen' ) AND ( gemeente = 'Knokke-Heist' )");
    }

    public function testComplexCombination( )
    {
        $criterion = KVDdb_Criterion::equals( 'provincie' , 'West-Vlaanderen' );
        $criterion2 = KVDdb_Criterion::equals ( 'provincie' , 'Oost-Vlaanderen' );
        $criterion2->addAnd( KVDdb_Criterion::equals( 'gemeente' , 'Maldegem' ) );
        $criterion->addOr( $criterion2 );
        $this->assertEqual( $criterion->generateSql( ) , "( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' AND ( gemeente = 'Maldegem' ) ) )" );
        
        $this->criteria->add( $criterion );
        
        $this->criteria->add( KVDdb_Criterion::matches ( 'naam' , '%berg%' ) );
        $this->assertEqual( $this->criteria->generateSql( ) , "WHERE ( provincie = 'West-Vlaanderen' OR ( provincie = 'Oost-Vlaanderen' AND ( gemeente = 'Maldegem' ) ) ) AND ( UPPER( naam ) LIKE UPPER( '%berg%' ) )");
    }
}
?>
