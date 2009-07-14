<?php
class TestOfGeomLineString extends UnitTestCase
{
    private $testLineString;

    private $testPointOne;

    private $testPointTwo;
    
    function setUp()
    {
        $this->testLineString = new KVDgis_GeomLineString();
        $this->testPointOne = new KVDgis_GeomPoint( );
        $this->testPointOne->setGeometryFromText( 'POINT(178000 212000)' );
        $this->testPointTwo = new KVDgis_GeomPoint( );
        $this->testPointTwo->setGeometryFromText( 'POINT(100000 150000)' );
    }

    function tearDown()
    {
        $this->testLineString = null;
    }

    function testSetSrid()
    {
        $this->testLineString->setSrid(5000);
        $this->assertEqual($this->testLineString->getSrid() , 5000 , 'De via setSrid() ingesteld srid is niet dezelfde als die die via getSrid() teruggegeven wordt!');    
    }

    function testAddPoint( )
    {
        $this->testLineString->addPoint( $this->testPointOne );
        $this->assertEqual( $this->testLineString->getAsText( ) , 'LINESTRING(178000 212000)');
    }

    function testSetPoints( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $this->testLineString->setPoints( $points );
        $this->assertEqual( $this->testLineString->getAsText( ) , 'LINESTRING(178000 212000, 100000 150000)');
    }

    function testSetPointsAndSridInConstructor( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $testLineString = new KVDgis_GeomLineString( 31300 , $points );
        $this->assertEqual( $testLineString->getAsText( ) , 'LINESTRING(178000 212000, 100000 150000)');
        $this->assertEqual( $testLineString->getSrid( ) , 31300 );
    }

    function testSetGeometryFromText()
    {
        $this->testLineString->setGeometryFromText('LINESTRING(178000 212000, 100000 150000)');
        $points = $this->testLineString->getPoints( );
        $this->assertEqual( $points[0]->getAsText( ) , 'POINT(178000 212000)');
        $this->assertEqual( $points[1]->getAsText( ) , 'POINT(100000 150000)');
    }

    
    function testGeometryToString()
    {
        $this->testLineString->setGeometryFromText('LINESTRING(178000 212000, 100000 150000)');
        ob_start( );
        echo $this->testLineString;
        $buffer = ob_get_contents( );
        ob_end_clean( );
        $this->assertEqual( $buffer , 'LINESTRING(178000 212000, 100000 150000)');
    }
    
    /**
     * testSetGeometryFromInvalidText 
     * 
     * Nodig omdat postgis < 1.2 een verkeerde WKT teruggeven
     * @access public
     * @return void
     */
    /*
    function testSetGeometryFromInvalidText()
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT(178000 212000, 100000 150000)');
        $points = $this->testMultiPoint->getPoints( );
        $this->assertEqual( $points[0]->getAsText( ) , 'POINT(178000 212000)');
        $this->assertEqual( $points[1]->getAsText( ) , 'POINT(100000 150000)');
    }

    function testSetGeometryFromSpacyData( )
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT( ( 178000 212000 ) , (100000 150000) )');
        $points = $this->testMultiPoint->getPoints( );
        $this->assertEqual( $points[0]->getAsText( ) , 'POINT(178000 212000)');
        $this->assertEqual( $points[1]->getAsText( ) , 'POINT(100000 150000)');
    }

    function testSetGeometryFromBogusData( )
    {
        try {
            $this->testMultiPoint->setGeometryFromText('MULTIPOINT( ( coorda coordb ) , (testa testb) )');
            $this->fail( 'Er had een Exception moeten zijn.');
        } catch ( InvalidArgumentException $e ) {
            $this->pass( );
        } catch ( Exception $e ) {
            $this->fail ( 'Er is wel een Exception maar van een verkeerd type.' );
        }
    }

    function testSetIllegalGeometryFromText()
    {
        try {
            $this->testMultiPoint->setGeometryFromText('POLYGON(178000 212000)');    
            $this->fail( 'Er had een Exception moeten zijn.');
        } catch (Exception $e) {
            $this->pass( );
        }
    }
    */
}
?>
