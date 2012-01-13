<?php

class KVDgis_GeomPointTest extends PHPUnit_Framework_TestCase
{

    private $_testPoint;
    
    function setUp()
    {
        $this->_testPoint = new KVDgis_GeomPoint();
    }

    function tearDown()
    {
        $this->_testPoint = null;
    }

    function testSetSrid()
    {
        $this->_testPoint->setSrid(5000);
        $this->assertEquals( 5000, $this->_testPoint->getSrid() );    
    }

    function testIsEmpty( )
    {
        $this->assertEquals( -1, $this->_testPoint->getSrid( ) );
        $this->assertTrue( $this->_testPoint->isEmpty( ) );
    }

    function testSetXY()
    {
        $this->_testPoint->setX(178000);
        $this->_testPoint->setY(212000);
        $this->assertEquals(178000, $this->_testPoint->getX() );
        $this->assertEquals(212000, $this->_testPoint->getY() );
    }

    function testSetGeometryFromText()
    {
        $this->_testPoint->setGeometryFromText('POINT(178000 212000)');
        $this->assertEquals(178000, $this->_testPoint->getX());
        $this->assertEquals(212000, $this->_testPoint->getY());
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
        $this->_testPoint->setGeometryFromText('POLYGON(178000 212000)');    
    }

    function testGetAsText( )
    {
        $values = array ( 'EMPTY', 'POINT(178000 212000)' );
        foreach ( $values as $v ) {
            $this->_testPoint->setGeometryFromText($v);
            $this->assertEquals( $v, $this->_testPoint->getAsText( ) );
            $this->assertEquals( $v, (string) $this->_testPoint );
        }
    }

    function testGetAsJson( )
    {
        $this->_testPoint->setGeometryFromText('POINT(178000 212000)');
        $js = new stdClass( );
        $js->type = 'Point';
        $js->coordinates = array( 178000, 212000 );
        $this->assertEquals( $js, json_decode( $this->_testPoint->getAsJson( ) ) );
    }

    function testGetAsJsonAsObject( )
    {
        $this->_testPoint->setGeometryFromText('POINT(178000 212000)');
        $js = new stdClass( );
        $js->type = 'Point';
        $js->coordinates = array( 178000, 212000 );
        $this->assertEquals( $js, $this->_testPoint->getAsJson( false ) );
    }


}
?>
