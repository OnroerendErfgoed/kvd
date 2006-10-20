<?php

class TestOfCrab2Gateway extends UnitTestCase
{

    private $parameters;
    
    function setUp()
    {
        $this->parameters = array ( 'wsdl' => 'http://ws.agiv.be/crabws/nodataset.asmx?WSDL',
                                    'username' => 'VIOE',
                                    'password' => 'GISTLIBE'
                            );
    }

    private function getGateway( )
    {
        try {
            return new KVDgis_Crab2Gateway( $this->parameters );
        } catch ( KVDutil_GatewayUnavailableException $e ) {
            $this->fail( 'De Crab2 webservice is niet beschikbaar.');
        }
    }

    function tearDown()
    {
        $this->parameters = null;
    }

    function testUnavailable( )
    {
        $parameters = $this->parameters;
        $parameters['wsdl'] = 'http://webservices.gisvlaanderen.be/crab_1_0/ws_crab_NDS.asmx?WSDL';
        try {
            $testGateway = new KVDgis_Crab2Gateway( $parameters );
            $this->fail ( 'Initialiseren met een onbestaande WSDL zou een KVDutil_GatewayUnavailableException moeten geven.');
        } catch ( KVDutil_GatewayUnavailableException $e ) {
            $this->pass( 'Geslaagd' );
        } catch ( Exception $e ) {
            $this->fail ( 'Initialiseren met een onbestaande WSDL zou een KVDutil_GatewayUnavailableException moeten geven. Ik heb een generieke Exception opgevangen.');
        }
    }

    function testListGemeentenByGewestId( )
    {
        $gemeenten = $this->getGateway( )->listGemeentenByGewestId( KVDgis_Crab2Gateway::GEWEST_VLAANDEREN, KVDgis_Crab2Gateway::GEM_SORT_NAAM );
        $this->assertIsA( $gemeenten, 'array');
        $this->assertIsA ( $gemeenten[20] , 'array');
        $this->assertNotNull ( $gemeenten[20]['gemeenteId']);
        $this->assertNotNull ( $gemeenten[20]['gemeenteNaam']);
        $this->assertNotNull ( $gemeenten[20]['taalCode']);
        $this->assertNotNull ( $gemeenten[20]['taalCodeGemeenteNaam']);
    }

    private function assertIsKnokke( $gemeente ) 
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
        $gemeente = $this->getGateway( )->getGemeenteByGemeenteId( 191 );
        $this->assertIsKnokke( $gemeente );
    }

    public function testGetGemeenteByGemeenteNaam( )
    {
        $gemeente = $this->getGateway( )->getGemeenteByGemeenteNaam( 'Knokke-Heist' );
        $this->assertIsKnokke( $gemeente );
    }

    public function testGetGemeenteByNisGemeenteCode( )
    {
        $gemeente = $this->getGateway( )->getGemeenteByNISGemeenteCode( 31043 );
        $this->assertIsKnokke( $gemeente );
    }

    public function testIllegalGetGemeenteByGemeenteId( )
    {
        try {
            $gemeente = $this->getGateway( )->getGemeenteByGemeenteId( 5486512 );
        } catch ( RuntimeException $e) {
            $this->pass( );
        } catch ( Exception $e ) {
            $this->fail( 'Onverwachte Exception opgevangen!');
        }
    }

    public function testListStraatnamenByGemeenteId( )
    {
        $straatnamen = $this->getGateway( )->listStraatnamenByGemeenteId( 191, KVDgis_Crab2Gateway::STRAAT_SORT_NAAM);
        $this->assertIsA( $straatnamen, 'array');
        $this->assertIsA( $straatnamen[20],'array');
        $this->assertNotNull ( $straatnamen[20]['straatnaam']);
        $this->assertNotNull ( $straatnamen[20]['straatnaamId']);
        $this->assertNotNull ( $straatnamen[20]['straatnaamLabel']);
    }
    
    private function assertIsNieuwstraat ( $straatnaam )
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
        $straatnaam = $this->getGateway( )->getStraatnaamByStraatnaamId( 48086 );
        $this->assertIsNieuwstraat ( $straatnaam );
    }

    public function testGetStraatnaamByStraatnaam( )
    {
        $straatnaam = $this->getGateway( )->getStraatnaamByStraatnaam( 'Nieuwstraat', 191 );
        $this->assertIsNieuwstraat ( $straatnaam );
    }

    public function testListHuisnummersByStraatnaamId ()
    {
        $huisnummers = $this->getGateway( )->listHuisnummersByStraatnaamId ( 48086 , 2 );
        $this->assertIsA( $huisnummers, 'array');
        $this->assertIsA( $huisnummers[20], 'array');
        $this->assertNotNull ( $huisnummers[20]['huisnummerId']);
        $this->assertNotNull ( $huisnummers[20]['huisnummer']);
    }

    private function assertIsNieuwstraat68 ( $huisnummer )
    {
        $this->assertIsA( $huisnummer , 'array' );
        $this->assertEqual ( $huisnummer['huisnummerId'] , 887821);
        $this->assertEqual ( $huisnummer['huisnummer'] , '68');
    }

    public function testGetHuisnummerByHuisnummerId ()
    {
        $huisnummer = $this->getGateway( )->getHuisnummerByHuisnummerId( 887821 );
        $this->assertIsNieuwstraat68( $huisnummer );
    }

    public function testGetHuisnummerByHuisnummer( )
    {
        $huisnummer = $this->getGateway( )->getHuisnummerByHuisnummer( '68' , 48086 );
        $this->assertIsNieuwstraat68( $huisnummer );
    }

    public function testGetPostkantonByHuisnummerId( )
    {
        $postkanton = $this->getGateway( )->getPostkantonByHuisnummerId( 887821 );
        $this->assertEqual ( $postkanton['postkantonCode'] , 8300 );
    }
        
    public function testListWegobjectenByStraatnaamId( )
    {
        $wegobjecten = $this->getGateway( )->listWegobjectenByStraatnaamId( 48086 , KVDgis_Crab2Gateway::WEG_SORT_ID );
        $this->assertIsA( $wegobjecten, 'array' );
        $this->assertIsA( $wegobjecten[1], 'array' );
        $this->assertNotNull ( $wegobjecten[1]['identificatorWegobject'] );
    }

    public function testListTerreinObjectenByHuisnummerId( )
    {
        $terreinobjecten = $this->getGateway( )->listTerreinobjectenByHuisnummerId( 887821, KVDgis_Crab2Gateway::TERREIN_SORT_ID );
        $this->assertIsA( $terreinobjecten , 'array' );
        $this->assertIsA( $terreinobjecten[0] , 'array');
        $this->assertNotNull( $terreinobjecten[0]['identificatorTerreinobject']);
        $this->assertNotNull( $terreinobjecten[0]['aardTerreinobjectCode']);
    }

    public function testGetTerreinobjectByIdentificatorTerreinobject()
    {
        $gateway = $this->getGateway( );
        $terreinobjecten = $gateway->listTerreinobjectenByHuisnummerId( 887821 );
        $terreinobject = $gateway->getTerreinobjectByIdentificatorTerreinobject( $terreinobjecten[0]['identificatorTerreinobject'] );
        $this->assertIsA( $terreinobject, 'array' );
        $this->assertEqual( $terreinobject['identificatorTerreinobject'] , $terreinobjecten[0]['identificatorTerreinobject'] );
        $this->assertNotNull( $terreinobject['centerX']);
        $this->assertNotNull( $terreinobject['centerY']);
        $this->assertNotNull( $terreinobject['aardTerreinobjectCode']);
    }

}
?>
