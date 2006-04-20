<?php
class TestOfDimensies extends UnitTestCase
{
    private $_dimensies;

    private $_breedte;

    private $_gewicht;

    public function setUp ()
    {
        $toegestaneDimensies = array ( 'lengte' , 'breedte' , 'hoogte' , 'dikte' , 'diameter' , 'gewicht' );
        $this->_dimensies = new KVDutil_Dimensies ( $toegestaneDimensies );

    }

    public function tearDown ()
    {
        $this->_dimensies = null;
    }
    
    public function testOfEmptyCollection()
    {
        $this->assertFalse ( isset ( $this->_dimensies['lengte'] ) );
        $this->assertFalse ( $this->_dimensies['lengte'] );
    }

    public function testOfAddDimensie()
    {
        $breedte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm', 'breedte' );
        $gewicht = new KVDutil_VoorwerpGewicht ( 0.346 , 'kg', 'gewicht' );
        
        $this->_dimensies['breedte'] = $breedte;
        $this->_dimensies['gewicht'] = $gewicht;

        $breedte2 = $this->_dimensies['breedte'];
        $gewicht2 = $this->_dimensies['gewicht'];
        
        $this->assertReference ( $breedte2 , $breedte );
        $this->assertReference ( $gewicht2 , $gewicht );
    }
    
    public function testOfRemoveDimensie()
    {
        $breedte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm', 'breedte' );
        $this->_dimensies['breedte'] = $breedte;
        $this->assertReference ( $this->_dimensies['breedte'] , $breedte );
        unset ( $this->_dimensies['breedte'] );
        $this->assertFalse ( $this->_dimensies['breedte'] );
    }
    
    public function testOfIllegalDimensie()
    {
        try {
            $diepte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm' , 'diepte' );
            $this->_dimensies['diepte'] = $diepte;    
        } catch (Exception $e) {
            $this->assertWantedPattern ( '/Deze dimensie hoort niet/',$e->getMessage());   
        }
    }

    public function testOfOmschrijving()
    {
        $breedte = new KVDutil_VoorwerpAfmeting ( 5 , 'cm', 'breedte' );
        $gewicht = new KVDutil_VoorwerpGewicht ( 0.346 , 'kg', 'gewicht' );
        
        $this->_dimensies['breedte'] = $breedte;
        $this->_dimensies['gewicht'] = $gewicht;

        $this->assertEqual ( $this->_dimensies->getOmschrijving() , 'B: 50mm, G: 346gr.');
    }
    
    
}
?>
