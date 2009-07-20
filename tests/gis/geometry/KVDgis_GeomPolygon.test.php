<?php
class TestOfGeomPolygon extends UnitTestCase
{
    private $testPolygon;
    
    private $testLinearRing1;

    private $testLinearRing2;

    private $testPointOne;

    private $testPointTwo;
    
    function setUp()
    {
        $this->testPolygon = new KVDgis_GeomPolygon;
        
        $this->testPointOne = new KVDgis_GeomPoint( );
        $this->testPointOne->setGeometryFromText( 'POINT(178000 212000)' );
        $this->testPointTwo = new KVDgis_GeomPoint( );
        $this->testPointTwo->setGeometryFromText( 'POINT(100000 150000)' );

        $this->testLinearRing1 = new KVDgis_GeomLinearRing(5000, array($this->testPointOne, $this->testPointTwo ) );
        $this->testLinearRing2 = new KVDgis_GeomLinearRing(5000, array($this->testPointOne, $this->testPointTwo ) );
    }

    function tearDown()
    {
        $this->testPolygon = null;
    }

    function testSetSrid()
    {
        $this->testPolygon->setSrid(5000);
        $this->assertEqual($this->testPolygon->getSrid() , 5000 , 'De via setSrid() ingesteld srid is niet dezelfde als die die via getSrid() teruggegeven wordt!');    
    }

    function testIsEmpty( )
    {
        $this->assertEqual( $this->testPolygon->getSrid( ), -1);
        $this->assertTrue( $this->testPolygon->isEmpty( ) );
    }

    function testSetOuterRing( )
    {
        $this->testPolygon->setOuterRing( $this->testLinearRing1 );
        $this->assertEqual($this->testPolygon->getAsText( ),
                            'POLYGON((178000 212000, 100000 150000))');
    }

    function testSetInnerRings( )
    {
        $this->testPolygon->setOuterRing( $this->testLinearRing1 );
        $this->testPolygon->setInnerRings( array( $this->testLinearRing2 ) );
        $this->assertEqual($this->testPolygon->getAsText( ),
                            'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))');
    }

    
    function testAddInnerRing( )
    {
        $this->testPolygon->setOuterRing( $this->testLinearRing1 );
        $this->testPolygon->addInnerRing( $this->testLinearRing2 );
        $this->assertEqual($this->testPolygon->getAsText( ),
                            'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))');
    }
    

    function testConstructor( )
    {
        $testPolygon = new KVDgis_GeomPolygon( 31300 , $this->testLinearRing1, array($this->testLinearRing2));
        $this->assertEqual( $testPolygon->getAsText( ) , 
                            'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))');
        $this->assertEqual( $testPolygon->getSrid( ) , 31300 );
    }
    function testSetGeometryFromText()
    {
        $values = array ( 'POLYGON((178000.05 212000.10, 100000 150000), (178000 212000, 100000 150000))', 'EMPTY' );
        foreach ( $values as $v ) {
            $this->testPolygon->setGeometryFromText($v);
            $this->assertEqual( $this->testPolygon->getAsText( ), $v ); 
        }
    }
    
    function testGeometryToString()
    {

        $this->testPolygon->setGeometryFromText(
                                                'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))'
                                                );
        ob_start( );
        echo $this->testPolygon;
        $buffer = ob_get_contents( );
        ob_end_clean( );
        $this->assertEqual( $buffer , 
                            'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))');
    }

    function testSetIllegalGeometryFromText()
    {
        try {
            $this->testPolygon->setGeometryFromText('MULTIPOINT(178000 212000)');    
            $this->fail( 'Er had een Exception moeten zijn.');
        } catch (Exception $e) {
            $this->pass( );
        }
    }
}
?>
