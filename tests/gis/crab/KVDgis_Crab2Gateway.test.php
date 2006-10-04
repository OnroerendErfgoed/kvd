<?php

class TestOfCrab2Gateway extends UnitTestCase
{

    private $_testGateway;
    
    function setUp()
    {
        $parameters = array (   'wsdl' => 'http://webservices.gisvlaanderen.be/crab_1_0/ws_crab_NDS.asmx?WSDL',
                                'username' => 'VIOE',
                                'password' => 'GISTLIBE'
                            );
        $this->_testGateway = new KVDgis_Crab2Gateway( $parameters );
    }

    function tearDown()
    {
        $this->_testGateway = null;
    }

    function testListGemeentenByGewestId( )
    {
        $gemeenten = $this->_testGateway->listGemeentenByGewestId( KVDgis_Crab2Gateway::GEWEST_VLAANDEREN, KVDgis_Crab2Gateway::GEM_SORT_NAAM );
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
        $gemeente = $this->_testGateway->getGemeenteByGemeenteId( 191 );
        $this->assertIsKnokke( $gemeente );
    }

    public function testGetGemeenteByGemeenteNaam( )
    {
        $gemeente = $this->_testGateway->getGemeenteByGemeenteNaam( 'Knokke-Heist' );
        $this->assertIsKnokke( $gemeente );
    }

    public function testGetGemeenteByNisGemeenteCode( )
    {
        $gemeente = $this->_testGateway->getGemeenteByNISGemeenteCode( 31043 );
        $this->assertIsKnokke( $gemeente );
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
        $straatnamen = $this->_testGateway->listStraatnamenByGemeenteId( 191, KVDgis_Crab2Gateway::STRAAT_SORT_NAAM);
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
        $straatnaam = $this->_testGateway->getStraatnaamByStraatnaamId( 48086 );
        $this->assertIsNieuwstraat ( $straatnaam );
    }

    public function testGetStraatnaamByStraatnaam( )
    {
        $straatnaam = $this->_testGateway->getStraatnaamByStraatnaam( 'Nieuwstraat', 191 );
        $this->assertIsNieuwstraat ( $straatnaam );
    }

    public function testListHuisnummersByStraatnaamId ()
    {
        $huisnummers = $this->_testGateway->listHuisnummersByStraatnaamId ( 48086 , 2 );
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
        $huisnummer = $this->_testGateway->getHuisnummerByHuisnummerId( 887821 );
        $this->assertIsNieuwstraat68( $huisnummer );
    }

    public function testGetHuisnummerByHuisnummer( )
    {
        $huisnummer = $this->_testGateway->getHuisnummerByHuisnummer( '68' , 48086 );
        $this->assertIsNieuwstraat68( $huisnummer );
    }

    public function testGetPostkantonByHuisnummerId( )
    {
        $postkanton = $this->_testGateway->getPostkantonByHuisnummerId( 887821 );
        $this->assertEqual ( $postkanton['postkantonCode'] , 8300 );
    }
        
    public function testListWegobjectenByStraatnaamId( )
    {
        $wegobjecten = $this->_testGateway->listWegobjectenByStraatnaamId( 48086 , KVDgis_Crab2Gateway::WEG_SORT_ID );
        $this->assertIsA( $wegobjecten, 'array' );
        $this->assertIsA( $wegobjecten[1], 'array' );
        $this->assertNotNull ( $wegobjecten[1]['identificatorWegobject'] );
    }

    public function testListTerreinObjectenByHuisnummerId( )
    {
        $terreinobjecten = $this->_testGateway->listTerreinobjectenByHuisnummerId( 887821, KVDgis_Crab2Gateway::TERREIN_SORT_ID );
        $this->assertIsA( $terreinobjecten , 'array' );
        $this->assertIsA( $terreinobjecten[0] , 'array');
        $this->assertNotNull( $terreinobjecten[0]['identificatorTerreinobject']);
        $this->assertNotNull( $terreinobjecten[0]['aardTerreinobjectCode']);
    }

    public function testGetTerreinobjectByIdentificatorTerreinobject()
    {
        $terreinobjecten = $this->_testGateway->listTerreinobjectenByHuisnummerId( 887821 );
        $terreinobject = $this->_testGateway->getTerreinobjectByIdentificatorTerreinobject( $terreinobjecten[0]['identificatorTerreinobject'] );
        $this->assertIsA( $terreinobject, 'array' );
        $this->assertEqual( $terreinobject['identificatorTerreinobject'] , $terreinobjecten[0]['identificatorTerreinobject'] );
        $this->assertNotNull( $terreinobject['centerX']);
        $this->assertNotNull( $terreinobject['centerY']);
        $this->assertNotNull( $terreinobject['aardTerreinobjectCode']);
    }

}
?>
