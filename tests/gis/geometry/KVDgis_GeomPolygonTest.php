<?php
require_once( 'PHPUnit/Framework.php' );

class KVDgis_GeomPolygonTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals(5000, $this->testPolygon->getSrid());    
    }

    function testIsEmpty( )
    {
        $this->assertEquals( -1, $this->testPolygon->getSrid( ) );
        $this->assertTrue( $this->testPolygon->isEmpty( ) );
    }

    function testSetOuterRing( )
    {
        $this->testPolygon->setOuterRing( $this->testLinearRing1 );
        $this->assertEquals($this->testPolygon->getAsText( ),
                            'POLYGON((178000 212000, 100000 150000))');
    }

    function testSetInnerRings( )
    {
        $this->testPolygon->setOuterRing( $this->testLinearRing1 );
        $this->testPolygon->setInnerRings( array( $this->testLinearRing2 ) );
        $this->assertEquals($this->testPolygon->getAsText( ),
                            'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))');
    }

    
    function testAddInnerRing( )
    {
        $this->testPolygon->setOuterRing( $this->testLinearRing1 );
        $this->testPolygon->addInnerRing( $this->testLinearRing2 );
        $this->assertEquals($this->testPolygon->getAsText( ),
                            'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))');
    }
    

    function testConstructor( )
    {
        $testPolygon = new KVDgis_GeomPolygon( 31300 , $this->testLinearRing1, array($this->testLinearRing2));
        $this->assertEquals( $testPolygon->getAsText( ) , 
                            'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))');
        $this->assertEquals( 31300, $testPolygon->getSrid( ) );
    }
    function testSetGeometryFromText()
    {
        $values = array ( 'POLYGON((178000.05 212000.10, 100000 150000), (178000 212000, 100000 150000))', 'EMPTY' );
        foreach ( $values as $v ) {
            $this->testPolygon->setGeometryFromText($v);
            $this->assertEquals( $v, $this->testPolygon->getAsText( ) ); 
        }
    }
    
    function testGeometryToString()
    {

        $this->testPolygon->setGeometryFromText(
                                                'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))'
                                                );
        $this->assertEquals( 'POLYGON((178000 212000, 100000 150000), (178000 212000, 100000 150000))', (string) $this->testPolygon );
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
            $this->testPolygon->setGeometryFromText('MULTIPOINT(178000 212000)');    
    }
}
?>
