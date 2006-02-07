<?php

require_once ( GISMAP . '/geometry/KVDgis_GeomGeometry.class.php');
require_once ( GISMAP . '/geometry/KVDgis_GeomPoint.class.php');

class TestOfGeomPoint extends UnitTestCase
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
        $this->assertTrue($this->_testPoint->getSrid() == 5000, 'De via setSrid() ingesteld srid is niet dezelfde als die die via getSrid() teruggegeven wordt!');    
    }

    function testSetXY()
    {
        $this->_testPoint->setX(178000);
        $this->_testPoint->setY(212000);
        $this->assertTrue($this->_testPoint->getX() == 178000);
        $this->assertTrue($this->_testPoint->getY() == 212000);
    }

    function testSetGeometryFromText()
    {
        $this->_testPoint->setGeometryFromText('POINT(178000 212000)');
        $this->assertTrue($this->_testPoint->getX() == 178000);
        $this->assertTrue($this->_testPoint->getY() == 212000);
    }

    function testSetIllegalGeometryFromText()
    {
        try {
            $this->_testPoint->setGeometryFromText('POLYGON(178000 212000)');    
        } catch (Exception $e) {
            $exception = $e;    
        }
        $this->assertIsA($e, 'Exception');
        
    }
    
}
?>
