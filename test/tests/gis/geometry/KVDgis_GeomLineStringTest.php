<?php

class KVDgis_GeomLineStringTest extends PHPUnit_Framework_TestCase
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
        $this->assertEquals(5000, $this->testLineString->getSrid() );    
    }

    function testAddPoint( )
    {
        $this->testLineString->addPoint( $this->testPointOne );
        $this->assertEquals( $this->testLineString->getAsText( ) , 'LINESTRING(178000 212000)');
    }

    function testAddEmptyPoint( )
    {
        $p = new KVDgis_GeomPoint();
        $p->setGeometryFromText( 'EMPTY' );
        $this->testLineString->addPoint( $p );
        $this->assertTrue( $this->testLineString->isEmpty() );
    }

    function testSetPoints( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $this->testLineString->setPoints( $points );
        $this->assertEquals( $this->testLineString->getAsText( ) , 'LINESTRING(178000 212000, 100000 150000)');
    }

    function testSetPointsAndSridInConstructor( )
    {
        $points = array ( $this->testPointOne, $this->testPointTwo );
        $testLineString = new KVDgis_GeomLineString( 31300 , $points );
        $this->assertEquals( $testLineString->getAsText( ) , 'LINESTRING(178000 212000, 100000 150000)');
        $this->assertEquals( 31300, $testLineString->getSrid( ) );
    }

    function testSetGeometryFromText()
    {
        $this->testLineString->setGeometryFromText('LINESTRING(178000 212000, 100000 150000)');
        $points = $this->testLineString->getPoints( );
        $this->assertEquals( $points[0]->getAsText( ) , 'POINT(178000 212000)');
        $this->assertEquals( $points[1]->getAsText( ) , 'POINT(100000 150000)');
    }

    
    function testGeometryToString()
    {
        $this->testLineString->setGeometryFromText('LINESTRING(178000 212000, 100000 150000)');
        $this->assertEquals( 'LINESTRING(178000 212000, 100000 150000)', (string) $this->testLineString );
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
        $this->testLineString->setGeometryFromText('POLYGON(178000 212000)');    
    }

    function testGetAsText( )
    {
        $values = array ( 'EMPTY', 'LINESTRING(178000 212000, 100000 150000)' );
        foreach ( $values as $v ) {
            $this->testLineString->setGeometryFromText($v);
            $this->assertEquals( $v, $this->testLineString->getAsText( ) );
            $this->assertEquals( $v, (string) $this->testLineString );
        }
    }

    function testWithSpaces( )
    {
        $this->testLineString->setGeometryFromText('LINESTRING ( 178000 212000, 100000 150000 ) ');
        $this->assertEquals( 'LINESTRING(178000 212000, 100000 150000)', (string) $this->testLineString );
    }
}
?>
