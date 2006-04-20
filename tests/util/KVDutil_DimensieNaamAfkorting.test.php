<?php

class TestOfDimensieNaamAfkorting extends UnitTestCase
{
    private $_dimensieNaamAfkorting;
    
    public function setUp()
    {
        $this->_dimensieNaamAfkorting = new KVDutil_DimensieNaamAfkorting();
    }

    public function tearDown()
    {
        $this->_dimensieNaamAfkorting = null;    
    }

    public function testNaamNaarAfkorting()
    {
        $this->assertEqual ( $this->_dimensieNaamAfkorting->convertDimensieNaamNaarAfkorting ( 'lengte' ) , 'L' );
        $this->assertEqual ( $this->_dimensieNaamAfkorting->convertDimensieNaamNaarAfkorting ( 'gewicht' ) , 'G' );
    }

    public function testAfkortingNaarNaam()
    {
        $this->assertEqual ( $this->_dimensieNaamAfkorting->convertDimensieAfkortingNaarNaam ( 'H' ) , 'hoogte' );
        $this->assertEqual ( $this->_dimensieNaamAfkorting->convertDimensieAfkortingNaarNaam ( 'Diam' ) , 'diameter' );
    }
}

?>
