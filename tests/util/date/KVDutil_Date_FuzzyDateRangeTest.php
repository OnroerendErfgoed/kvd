<?php
require_once ( 'PHPUnit/Framework.php' );

class FuzzyDateRangeTest extends PHPUnit_Framework_TestCase
{
    private $test;
    
    public function setUp( )
    {
        $meta = array(  'type_van' => 'dag', 'type_tot' => 'dag', 
                        'omschrijving_van' => array( 'omschrijving' => 'vandaag', 'manueel' => true ),
                        'omschrijving_tot' => array( 'omschrijving' => 'vandaag', 'manueel' => true ));
        $this->test = new KVDutil_Date_FuzzyDateRange( new DateTime( ), new DateTime( ), new DateTime(  ), new DateTime( ), $meta );
    }

    public function tearDown( )
    {
        $this->test = null;
    }

    public function testBasis( )
    {
        $this->assertType( 'DateTime', $this->test->getSa( ) );
        $this->assertType( 'DateTime', $this->test->getKa( ) );
        $this->assertType( 'DateTime', $this->test->getKb( ) );
        $this->assertType( 'DateTime', $this->test->getSb( ) );
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

}
?>
