<?php

require_once( 'PHPUnit/Framework.php' );

class KVDgis_GeomMultiPointTest extends PHPUnit_Framework_TestCase
{
    private $testMultiPoint;

    private $testPointOne;

    private $testPointTwo;
    
    function setUp()
    {
        $this->testMultiPoint = new KVDgis_GeomMultiPoint();
        $this->testPointOne = new KVDgis_GeomPoint( );
        $this->testPointOne->setGeometryFromText( 'POINT(178000 212000)' );
        $this->testPointTwo = new KVDgis_GeomPoint( );
        $this->testPointTwo->setGeometryFromText( 'POINT(100000 150000)' );
    }

    function tearDown()
    {
        $this->testMultiPoint = null;
    }

    function testSetSrid()
    {
        $this->testMultiPoint->setSrid(5000);
        $this->assertEquals( 5000, $this->testMultiPoint->getSrid() );    
    }

    function testIsEmpty( )
    {
        $this->assertEquals( -1, $this->testMultiPoint->getSrid( ) );
        $this->assertTrue( $this->testMultiPoint->isEmpty( ));        
    }

    function testAddPoint( )
    {
        $this->testMultiPoint->addPoint( $this->testPointOne );
        $this->assertEquals( 'MULTIPOINT(178000 212000)', $this->testMultiPoint->getAsText( ) );
    }

    function testSetPoints( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $this->testMultiPoint->setPoints( $points );
        $this->assertEquals( 'MULTIPOINT(178000 212000, 100000 150000)', $this->testMultiPoint->getAsText( ) );
    }

    function testSetPointsAndSridInConstructor( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $testMultiPoint = new KVDgis_GeomMultiPoint( 31300 , $points );
        $this->assertEquals( 'MULTIPOINT(178000 212000, 100000 150000)', $testMultiPoint->getAsText( ) );
        $this->assertEquals(31300, $testMultiPoint->getSrid( ) , 31300 );
    }

    function testSetGeometryFromText()
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT(178000 212000, 100000 150000)');
        $points = $this->testMultiPoint->getPoints( );
        $this->assertEquals( 'POINT(178000 212000)',  $points[0]->getAsText( ) );
        $this->assertEquals( 'POINT(100000 150000)', $points[1]->getAsText( ) );
    }

    function testSetEmptyGeometryFromText( )
    {
        $this->testMultiPoint->setGeometryFromText('EMPTY');
        $this->assertTrue( $this->testMultiPoint->isEmpty( ) );
    }

    function testgetAsText()
    {
        $values = array ( 'MULTIPOINT(178000 212000, 100000 150000)', 'EMPTY' );
        foreach ( $values as $wkt ) {
            $this->testMultiPoint->setGeometryFromText($wkt);
            $this->assertEquals( $wkt, $this->testMultiPoint->getAsText( ) );
        }
    }

    function testGeometryToString( )
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT(178000 212000, 100000 150000)');
        $this->assertEquals( 'MULTIPOINT(178000 212000, 100000 150000)', (string) $this->testMultiPoint );
    }
    
    
    /**
     * testSetGeometryFromInvalidText 
     * 
     * Nodig omdat postgis < 1.2 een verkeerde WKT teruggeven
     * @access public
     * @return void
     */
    function testSetGeometryFromInvalidText()
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT(178000 212000, 100000 150000)');
        $points = $this->testMultiPoint->getPoints( );
        $this->assertEquals( 'POINT(178000 212000)', $points[0]->getAsText( ) );
        $this->assertEquals( 'POINT(100000 150000)', $points[1]->getAsText( ) );
    }

    function testSetGeometryFromSpacyData( )
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT(  178000 212000  , 100000 150000 )');
        $points = $this->testMultiPoint->getPoints( );
        $this->assertEquals( 'POINT(178000 212000)', $points[0]->getAsText( ) );
        $this->assertEquals( 'POINT(100000 150000)', $points[1]->getAsText( ) );
    }

    /**
     * testSetGeometryFromBogusData 
     * 
     * @expectedException   InvalidArgumentException 
     * @access public
     * @return void
     */
    function testSetGeometryFromBogusData( )
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT( ( coorda coordb ) , (testa testb) )');
    }

    /**
     * testSetIllegalGeometryFromText 
     *
     * @expectedException   InvalidArgumentException 
     * @access public
     * @return void
     */
    function testSetIllegalGeometryFromText()
    {
            $this->testMultiPoint->setGeometryFromText('POLYGON(178000 212000)');    
    }
    
}
?>
