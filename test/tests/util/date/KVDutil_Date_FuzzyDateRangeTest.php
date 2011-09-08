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
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getSa( ) );
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getKa( ) );
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getKb( ) );
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $this->test->getSb( ) );
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
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $test->getSa( ) );
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $test->getKa( ) );
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $test->getKb( ) );
        $this->assertType( 'KVDutil_Date_FuzzyDateRange_Date', $test->getSb( ) );
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

}
?>
