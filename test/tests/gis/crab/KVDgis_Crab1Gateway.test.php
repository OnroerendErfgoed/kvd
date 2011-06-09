<?php

class TestOfCrab1Gateway extends UnitTestCase
{

    private $_testGateway;
    
    function setUp()
    {
        $parameters = array (   'wsdl' => 'http://webservices.gisvlaanderen.be/crab/wscrab.asmx?WSDL',
                                'username' => CRABUSER ,
                                'password' => CRABPWD
                            );
        try {
            $this->_testGateway = new KVDgis_Crab1Gateway( $parameters );
        } catch ( KVDutil_GatewayUnavailableException $e ) {
            $this->fail( 'De Crab1 webservice is niet beschikbaar.');
        }
    }

    function tearDown()
    {
        $this->_testGateway = null;
    }

    function testListGemeentenByGewestId( )
    {
        $gemeenten = $this->_testGateway->listGemeentenByGewestId( 2, 2);
        $this->assertIsA( $gemeenten, 'array');
        $this->assertIsA ( $gemeenten[20] , 'array');
        $this->assertNotNull ( $gemeenten[20]['gemeenteId']);
        $this->assertNotNull ( $gemeenten[20]['gemeenteNaam']);
        $this->assertNotNull ( $gemeenten[20]['nisGemeenteCode']);
    }

    private function testKnokke( $gemeente ) 
    {
        $this->assertIsA( $gemeente , 'array' );
        $this->assertEqual( $gemeente['gemeenteId'], 191);
        $this->assertEqual( $gemeente['gemeenteNaam'], 'Knokke-Heist');
        $this->assertEqual( $gemeente['nisGemeenteCode'], 31043);
        $this->assertEqual( $gemeente['taalCode'], 'nl');
        $this->assertEqual( $gemeente['taalCodeGemeenteNaam'], 'nl');
    }

    public function testGetGemeenteByGemeenteId( )
    {
        $gemeente = $this->_testGateway->getGemeenteByGemeenteId( 191 );
        $this->testKnokke( $gemeente );
    }

    public function testGetGemeenteByGemeenteNaam( )
    {
        $gemeente = $this->_testGateway->getGemeenteByGemeenteNaam( 'Knokke-Heist');
        $this->testKnokke( $gemeente );
    }

    public function testGetGemeenteByNisGemeenteCode( )
    {
        $gemeente = $this->_testGateway->getGemeenteByNISGemeenteCode( 31043 );
        $this->testKnokke( $gemeente );
    }

    public function testIllegalGetGemeenteByGemeenteId( )
    {
        try {
            $gemeente = $this->_testGateway->getGemeenteByGemeenteId( 5486512 );
        } catch ( RuntimeException $e) {
            $this->pass( );
        } catch ( Exception $e ) {
            $this->fail( 'Onverwachte Exception opgevangen!');
        }
    }

    public function testListStraatnamenByGemeenteId( )
    {
        $straatnamen = $this->_testGateway->listStraatnamenByGemeenteId( 191, 2);
        $this->assertIsA( $straatnamen, 'array');
        $this->assertIsA( $straatnamen[20],'array');
        $this->assertNotNull ( $straatnamen[20]['straatnaam']);
        $this->assertNotNull ( $straatnamen[20]['straatnaamId']);
        $this->assertNotNull ( $straatnamen[20]['straatnaamLabel']);
    }

    private function testNieuwstraat ( $straatnaam )
    {
        $this->assertIsA( $straatnaam , 'array' );
        $this->assertEqual( $straatnaam['straatnaamId'], 48086);
        $this->assertEqual( $straatnaam['straatnaam'], 'Nieuwstraat');
        $this->assertEqual( $straatnaam['straatnaamLabel'], 'Nieuwstraat' );
        $this->assertEqual( $straatnaam['taalCode'], 'nl');
        $this->assertEqual( $straatnaam['gemeenteId'], 191 );
    }

    public function testGetStraatnaamByStraatnaamId( )
    {
        $straatnaam = $this->_testGateway->getStraatnaamByStraatnaamId( 48086 );
        $this->testNieuwstraat ( $straatnaam );
    }

    public function testGetStraatnaamByStraatnaam( )
    {
        $straatnaam = $this->_testGateway->getStraatnaamByStraatnaam( 'Nieuwstraat', 191 );
        $this->testNieuwstraat ( $straatnaam );
    }

    public function testListHuisnummersByStraatnaamId ()
    {
        $huisnummers = $this->_testGateway->listHuisnummersByStraatnaamId ( 48086 , 2 );
        $this->assertIsA( $huisnummers, 'array');
        $this->assertIsA( $huisnummers[20], 'array');
        $this->assertNotNull ( $huisnummers[20]['huisnummerId']);
        $this->assertNotNull ( $huisnummers[20]['huisnummer']);
    }

    private function testNieuwstraat68 ( $huisnummer )
    {
        $this->assertIsA( $huisnummer , 'array' );
        $this->assertEqual ( $huisnummer['huisnummerId'] , 887821);
        $this->assertEqual ( $huisnummer['huisnummer'] , '68');
    }

    public function testGetHuisnummerByHuisnummerId ()
    {
        $huisnummer = $this->_testGateway->getHuisnummerByHuisnummerId( 887821 );
        $this->testNieuwstraat68( $huisnummer );
    }

    public function testGetHuisnummerByHuisnummer( )
    {
        $huisnummer = $this->_testGateway->getHuisnummerByHuisnummer( '68' , 48086 );
        $this->testNieuwstraat68( $huisnummer );
    }
        
    public function testListWegobjectenByStraatnaamId( )
    {
        $wegobjecten = $this->_testGateway->listWegobjectenByStraatnaamId( 48086 , 1);
        $this->assertIsA( $wegobjecten, 'array' );
        $this->assertIsA( $wegobjecten[1], 'array' );
        $this->assertNotNull ( $wegobjecten[1]['identificatorWegobject'] );
    }

    public function testListTerreinObjectenByHuisnummerId( )
    {
        $terreinobjecten = $this->_testGateway->listTerreinobjectenByHuisnummerId( 887821, 1 );
        $this->assertIsA( $terreinobjecten , 'array' );
        $this->assertIsA( $terreinobjecten[0] , 'array');
        $this->assertNotNull( $terreinobjecten[0]['identificatorTerreinobject']);
        $this->assertNotNull( $terreinobjecten[0]['aardTerreinobjectCode']);
        $this->assertNotNull( $terreinobjecten[0]['centerX']);
        $this->assertNotNull( $terreinobjecten[0]['centerY']);
    }

}
?>
