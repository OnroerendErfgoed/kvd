<?php

class TestOfDimensie extends UnitTestCase
{
    public function testOfVoorwerpAfmeting()
    {
        $dim = new KVDutil_VoorwerpAfmeting ( 5 , 'm', 'lengte' );
        $this->assertEqual ( $dim->getDimensie() , 5000 );
        $this->assertEqual ( $dim->getDimensieMaat() , 'mm' );
        $this->assertEqual ( $dim->getDimensieSoort() , 'lengte' );
        $this->assertEqual ( $dim->getOmschrijving() , 'L: 5000mm' );
    }

    public function testOfVoorwerpGewicht()
    {
        $dim = new KVDutil_VoorwerpGewicht ( 0.346 , 'kg', 'gewicht' );
        $this->assertEqual ( $dim->getDimensie() , 346 );
        $this->assertEqual ( $dim->getDimensieMaat() , 'gr' );
        $this->assertEqual ( $dim->getDimensieSoort() , 'gewicht' );
        $this->assertEqual ( $dim->getOmschrijving() , 'G: 346gr' );
    }
}

?>
