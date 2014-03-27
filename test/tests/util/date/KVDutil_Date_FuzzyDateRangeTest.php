<?php

class FuzzyDateRangeTest extends PHPUnit_Framework_TestCase
{
    private $test;
    
    public function setUp( )
    {
        $meta = array(  'type_van' => 'dag', 'type_tot' => 'dag', 
                        'omschrijving_van' => array( 'omschrijving' => 'vandaag', 'manueel' => true ),
                        'omschrijving_tot' => array( 'omschrijving' => 'vandaag', 'manueel' => true ));
        $this->test = new KVDutil_Date_FuzzyDateRange(  
            new KVDutil_Date_FuzzyDateRange_Date( ), 
            new KVDutil_Date_FuzzyDateRange_Date( ), 
            new KVDutil_Date_FuzzyDateRange_Date( ), 
            new KVDutil_Date_FuzzyDateRange_Date( ), $meta );
    }

    public function tearDown( )
    {
        $this->test = null;
    }

    public function testBasis( )
    {
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getSa( ) );
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getKa( ) );
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getKb( ) );
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getSb( ) );
        $this->assertFalse($this->test->isNull());
    }

    public function testType(  )
    {
        $this->assertEquals( 'dag', $this->test->getTypeVan( ) );
        $this->assertEquals( 'dag', $this->test->getTypeTot( ) );
    }

    public function testOmschrijving(  )
    {
        $this->assertEquals( 'vandaag', $this->test->getOmschrijvingVan(  ) );
        $this->assertEquals( 'vandaag', $this->test->getOmschrijvingTot(  ) );
        $this->assertTrue( $this->test->isOmschrijvingVanManueel(  ) );
        $this->assertTrue( $this->test->isOmschrijvingTotManueel(  ) );
    }

    public function testToString(  )
    {
        $this->assertEquals( 'vandaag - vandaag', ( string ) $this->test );
    }

    public function testConstructWithStrings(  )
    {
        $meta = array(  'type_van' => 'dag', 'type_tot' => 'dag', 
                        'omschrijving_van' => array( 'omschrijving' => 'vandaag', 'manueel' => true ),
                        'omschrijving_tot' => array( 'omschrijving' => 'vandaag', 'manueel' => true ));
        $test = new KVDutil_Date_FuzzyDateRange( '2010-01-01', '2010-01-31','2010-12-01','2010-12-31',$meta );
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $test->getSa( ) );
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $test->getKa( ) );
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $test->getKb( ) );
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange_Date', $test->getSb( ) );
        $test = new KVDutil_Date_FuzzyDateRange( '2010', '2010','2010','2010',$meta );
        $this->assertEquals( 2010, $test->getSa( ) );
        $this->assertEquals( 2010, $test->getKa( ) );
        $this->assertEquals( 2010, $test->getKb( ) );
        $this->assertEquals( 2010, $test->getSb( ) );
        $test = new KVDutil_Date_FuzzyDateRange( 2010, 2010,2010,2010,$meta );
        $this->assertEquals( 2010, $test->getSa( ) );
        $this->assertEquals( 2010, $test->getKa( ) );
        $this->assertEquals( 2010, $test->getKb( ) );
        $this->assertEquals( 2010, $test->getSb( ) );
    }

    public function testNullFuzzyDateRange( )
    {
        $test = new KVDutil_Date_NullFuzzyDateRange();
        $this->assertEquals( 'Onbepaald', $test->getOmschrijving() );
        $this->assertEquals( null, $test->getSa() );
        $this->assertEquals( null, $test->getKa() );
        $this->assertEquals( null, $test->getKb() );
        $this->assertEquals( null, $test->getSb() );
        $this->assertTrue($test->isNull());
    }

    public function testNewNull()
    {
        $fuzzynull = KVDutil_Date_NullFuzzyDateRange::newNull();
        $this->assertInstanceOf( 'KVDutil_Date_FuzzyDateRange', $fuzzynull );
        $this->assertInstanceOf( 'KVDutil_Date_NullFuzzyDateRange', $fuzzynull );
    }

}
?>
