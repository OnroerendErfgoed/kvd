<?php

class KVDutil_DimensieNaamAfkortingTest extends PHPUnit_Framework_TestCase
{
    protected $dimensieNaamAfkorting;
    
    public function setUp()
    {
        $this->dimensieNaamAfkorting = new KVDutil_DimensieNaamAfkorting();
    }

    public function tearDown()
    {
        $this->dimensieNaamAfkorting = null;    
    }

    public function testNaamNaarAfkorting()
    {
        $this->assertEquals ( $this->dimensieNaamAfkorting->convertDimensieNaamNaarAfkorting ( 'lengte' ) , 'L' );
        $this->assertEquals ( $this->dimensieNaamAfkorting->convertDimensieNaamNaarAfkorting ( 'gewicht' ) , 'G' );
    }

    public function testAfkortingNaarNaam()
    {
        $this->assertEquals ( $this->dimensieNaamAfkorting->convertDimensieAfkortingNaarNaam ( 'H' ) , 'hoogte' );
        $this->assertEquals ( $this->dimensieNaamAfkorting->convertDimensieAfkortingNaarNaam ( 'Diam' ) , 'diameter' );
    }
}

?>
