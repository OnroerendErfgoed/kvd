<?php
class TestOfGeomMultiPolygon extends UnitTestCase
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
        $this->assertEqual( $this->testMultiPoly->getSrid( ), -1 );
        $this->assertTrue( $this->testMultiPoly->isEmpty( ) ); 
    }

    function testSetSrid()
    {
        $this->testMultiPoly->setSrid(5000);
        $this->assertEqual($this->testMultiPoly->getSrid() , 5000 , 'De via setSrid() ingesteld srid is niet dezelfde als die die via getSrid() teruggegeven wordt!');    
    }

    function testSetPolygons( )
    {
        $this->testMultiPoly->setPolygons( array( $this->testPolygon1, $this->testPolygon2 ) );
        $this->assertEqual($this->testMultiPoly->getAsText( ),
                                'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100)))' );
    }
    
    function testAddPolygon( )
    {
        $this->testMultiPoly->addPolygon( $this->testPolygon1 );
        $this->assertEqual($this->testMultiPoly->getAsText( ),
                           'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)))' );
    }
    

    function testConstructor( )
    {
        $testMultiPoly = new KVDgis_GeomMultiPolygon( 31300 , array($this->testPolygon1, $this->testPolygon2));
        $this->assertEqual( $testMultiPoly->getAsText( ) , 
                            'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100)))' );
        $this->assertEqual( $testMultiPoly->getSrid( ) , 31300 );
    }
    
    function testSetGeometryFromText()
    {
        $values = array ( 'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100), (160 250, 400 500, 210 321)))', 'EMPTY' );
        foreach ( $values as $v ) {
            $this->testMultiPoly->setGeometryFromText($v);
            $this->assertEqual( $this->testMultiPoly->getAsText( ) , $v );
        }
    }

    function testGeometryToString()
    {
        $this->testMultiPoly->setGeometryFromText(
                            'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100), (160 250, 400 500, 210 321)))'
                                                );
        ob_start( );
        echo $this->testMultiPoly;
        $buffer = ob_get_contents( );
        ob_end_clean( );
        $this->assertEqual( $buffer , 
                            'MULTIPOLYGON(((178000.25 212000.35, 178100.50 212003.55, 180560 212100)), ((158000.25 200000.35, 158105.50 200203.55, 160560 200100), (160 250, 400 500, 210 321)))'
                            );
    }

    function testSetIllegalGeometryFromText()
    {
        try {
            $this->testMultiPoly->setGeometryFromText('MULTIPOINT(178000 212000)');    
            $this->fail( 'Er had een Exception moeten zijn.');
        } catch (Exception $e) {
            $this->pass( );
        }
    }
}
?>
