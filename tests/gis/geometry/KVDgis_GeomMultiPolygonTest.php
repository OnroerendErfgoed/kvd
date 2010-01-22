<?php

require_once( 'PHPUnit/Framework.php' );

class KVDgis_GeomMultiPolygonTest extends PHPUnit_Framework_TestCase
{
    private $testMultiPoly;
    
    private $testPolygon1;
    
    private $testPolygon2;
    
    function setUp()
    {
        $this->testMultiPoly = new KVDgis_GeomMultiPolygon( );

        $this->testPolygon1 = new KVDgis_GeomPolygon;
        $this->testPolygon1->setGeometryFromText( 'POLYGON((178000.25 212000.35, 178100.50 212003.55, 180560 212100))' );
        
        $this->testPolygon2 = new KVDgis_GeomPolygon;
        $this->testPolygon2->setGeometryFromText( 'POLYGON((158000.25 200000.35, 158105.50 200203.55, 160560 200100))' );
    }

    function tearDown()
    {
        $this->testMultiPoly = null;
    }

    function testIsEmpty( )
    {
        $this->assertEquals( -1, $this->testMultiPoly->getSrid( ) );
        $this->assertTrue( $this->testMultiPoly->isEmpty( ) ); 
    }

    function testSetSrid()
    {
        $this->testMultiPoly->setSrid(5000);
        $this->assertEquals( 5000, $this->testMultiPoly->getSrid() );    
    }

    function testSetPolygons( )
    {
        $this->testMultiPoly->setPolygons( array( $this->testPolygon1, $this->testPolygon2 ) );
        $this->assertEquals($this->testMultiPoly->getAsText( ),
                                'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100)))' );
    }
    
    function testAddPolygon( )
    {
        $this->testMultiPoly->addPolygon( $this->testPolygon1 );
        $this->assertEquals($this->testMultiPoly->getAsText( ),
                           'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)))' );
    }
    

    function testConstructor( )
    {
        $testMultiPoly = new KVDgis_GeomMultiPolygon( 31300 , array($this->testPolygon1, $this->testPolygon2));
        $this->assertEquals( $testMultiPoly->getAsText( ) , 
                            'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100)))' );
        $this->assertEquals( $testMultiPoly->getSrid( ) , 31300 );
    }
    
    function testSetGeometryFromText()
    {
        $values = array ( 'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100), (160 250, 400 500, 210 321)))', 'EMPTY' );
        foreach ( $values as $v ) {
            $this->testMultiPoly->setGeometryFromText($v);
            $this->assertEquals( $v, $this->testMultiPoly->getAsText( ) );
        }
    }

    function testGeometryToString()
    {
        $this->testMultiPoly->setGeometryFromText(
                            'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100), (160 250, 400 500, 210 321)))'
                                                );
        $this->assertEquals( (string) $this->testMultiPoly, 
                            'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100), (160 250, 400 500, 210 321)))'
                            );
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
        $this->testMultiPoly->setGeometryFromText('MULTIPOINT(178000 212000)');    
    }
}
?>
