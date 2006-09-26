<?php
class TestOfGeomMultiPoint extends UnitTestCase
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
        $this->assertEqual($this->testMultiPoint->getSrid() , 5000 , 'De via setSrid() ingesteld srid is niet dezelfde als die die via getSrid() teruggegeven wordt!');    
    }

    function testAddPoint( )
    {
        $this->testMultiPoint->addPoint( $this->testPointOne );
        $this->assertEqual( $this->testMultiPoint->getAsText( ) , 'MULTIPOINT((178000 212000))');
    }

    function testSetPoints( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $this->testMultiPoint->setPoints( $points );
        $this->assertEqual( $this->testMultiPoint->getAsText( ) , 'MULTIPOINT((178000 212000), (100000 150000))');
    }

    function testSetPointsAndSridInConstructor( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $testMultiPoint = new KVDgis_GeomMultiPoint( 31300 , $points );
        $this->assertEqual( $testMultiPoint->getAsText( ) , 'MULTIPOINT((178000 212000), (100000 150000))');
        $this->assertEqual( $testMultiPoint->getSrid( ) , 31300 );
    }

    function testSetGeometryFromText()
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT((178000 212000), (100000 150000))');
        $points = $this->testMultiPoint->getPoints( );
        $this->assertEqual( $points[0]->getAsText( ) , 'POINT(178000 212000)');
        $this->assertEqual( $points[1]->getAsText( ) , 'POINT(100000 150000)');
    }

    
    function testGeometryToString()
    {
        $this->testMultiPoint->setGeometryFromText('MULTIPOINT((178000 212000), (100000 150000))');
        ob_start( );
        echo $this->testMultiPoint;
        $buffer = ob_get_contents( );
        ob_end_clean( );
        $this->assertEqual( $buffer , 'MULTIPOINT((178000 212000), (100000 150000))');
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
    
}
?>
