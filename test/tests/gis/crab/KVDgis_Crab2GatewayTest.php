<?php


class KVDgis_Crab2GatewayTest extends PHPUnit_Framework_TestCase
{

    private $parameters;
    
    function setUp()
    {
        $this->parameters = array ( 'wsdl' => 'http://ws.agiv.be/crabws/nodataset.asmx?WSDL',
                                    'username' => CRAB_USER,
                                    'password' => CRAB_PWD,
                                    'safe_mode'=> false
                            );
        if ( defined ( 'CRAB_PROXY_HOST' ) && CRAB_PROXY_HOST != '' ) {
            $this->parameters['proxy_host'] = CRAB_PROXY_HOST;
            if ( defined( 'CRAB_PROXY_PORT' && CRAB_PROXY_PORT != '') ) {
                $this->parameters['proxy_port'] = CRAB_PROXY_PORT;
            }
        }
    }

    private function getGateway( )
    {
        try {
            return new KVDgis_Crab2Gateway( $this->parameters );
        } catch ( KVDutil_GatewayUnavailableException $e ) {
            $this->markTestSkipped( $e->getMessage( ) );
        }
    }
   

    function tearDown()
    {
        $this->parameters = null;
    }

    /**
     * testUnavailable 
     * 
     * @access public
     * @return void
     */
    /*
    function testUnavailable( )
    {
        $parameters = $this->parameters;
        $parameters['wsdl'] = 'http://webservices.gisvlaanderen.be/crab_1_0/ws_crab_NDS.asmx?WSDL';
        $parameters['username'] = 'user';
        $parameters['password'] = 'password';
        $testGateway = new KVDgis_Crab2Gateway( $parameters );
    }
    */

    function testListGemeentenByGewestId( )
    {
        $gemeenten = $this->getGateway( )->listGemeentenByGewestId( KVDgis_Crab2Gateway::GEWEST_VLAANDEREN, KVDgis_Crab2Gateway::GEM_SORT_NAAM );
        $this->assertInternalType( 'array', $gemeenten);
        $this->assertInternalType( 'array', $gemeenten[20]);
        $this->assertNotNull ( $gemeenten[20]['gemeenteId']);
        $this->assertNotNull ( $gemeenten[20]['gemeenteNaam']);
        $this->assertNotNull ( $gemeenten[20]['taalCode']);
        $this->assertNotNull ( $gemeenten[20]['taalCodeGemeenteNaam']);
    }

    private function assertIsKnokke( $gemeente ) 
    {
        $this->assertInternalType( 'array', $gemeente );
        $this->assertEquals( $gemeente['gemeenteId'], 191);
        $this->assertEquals( $gemeente['gemeenteNaam'], 'Knokke-Heist');
        $this->assertEquals( $gemeente['nisGemeenteCode'], 31043);
        $this->assertEquals( $gemeente['taalCode'], 'nl');
        $this->assertEquals( $gemeente['taalCodeGemeenteNaam'], 'nl');
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
    

    /**
     * testIllegalGetGemeenteByGemeenteId 
     * 
     * @expectedException   RuntimeException
     * @return void
     */
    public function testIllegalGetGemeenteByGemeenteId( )
    {
        $gemeente = $this->getGateway( )->getGemeenteByGemeenteId( 5486512 );
    }

    public function testListStraatnamenByGemeenteId( )
    {
        $straatnamen = $this->getGateway( )->listStraatnamenByGemeenteId( 191, KVDgis_Crab2Gateway::STRAAT_SORT_NAAM);
        $this->assertInternalType( 'array', $straatnamen);
        $this->assertInternalType( 'array', $straatnamen[20]);
        $this->assertNotNull ( $straatnamen[20]['straatnaam']);
        $this->assertNotNull ( $straatnamen[20]['straatnaamId']);
        $this->assertNotNull ( $straatnamen[20]['straatnaamLabel']);
    }
    
    private function assertIsNieuwstraat ( $straatnaam )
    {
        $this->assertInternalType( 'array', $straatnaam );
        $this->assertEquals( $straatnaam['straatnaamId'], 48086);
        $this->assertEquals( $straatnaam['straatnaam'], 'Nieuwstraat');
        $this->assertEquals( $straatnaam['straatnaamLabel'], 'Nieuwstraat' );
        $this->assertEquals( $straatnaam['taalCode'], 'nl');
        $this->assertEquals( $straatnaam['gemeenteId'], 191 );
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
        $this->assertInternalType( 'array', $huisnummers);
        $this->assertInternalType( 'array', $huisnummers[20]);
        $this->assertNotNull ( $huisnummers[20]['huisnummerId']);
        $this->assertNotNull ( $huisnummers[20]['huisnummer']);
    }

    private function assertIsNieuwstraat68 ( $huisnummer )
    {
        $this->assertInternalType( 'array', $huisnummer );
        $this->assertEquals ( $huisnummer['huisnummerId'] , 887821);
        $this->assertEquals ( $huisnummer['huisnummer'] , '68');
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
        $this->assertEquals ( $postkanton['postkantonCode'] , 8300 );
    }
        
    public function testListWegobjectenByStraatnaamId( )
    {
        $wegobjecten = $this->getGateway( )->listWegobjectenByStraatnaamId( 48086 , KVDgis_Crab2Gateway::WEG_SORT_ID );
        $this->assertInternalType( 'array', $wegobjecten );
        $this->assertInternalType( 'array', $wegobjecten[1] );
        $this->assertNotNull ( $wegobjecten[1]['identificatorWegobject'] );
    }

    public function testListTerreinObjectenByHuisnummerId( )
    {
        $terreinobjecten = $this->getGateway( )->listTerreinobjectenByHuisnummerId( 887821, KVDgis_Crab2Gateway::TERREIN_SORT_ID );
        $this->assertInternalType( 'array', $terreinobjecten );
        $this->assertInternalType( 'array', $terreinobjecten[0] );
        $this->assertNotNull( $terreinobjecten[0]['identificatorTerreinobject']);
        $this->assertNotNull( $terreinobjecten[0]['aardTerreinobjectCode']);
    }

    public function testGetTerreinobjectByIdentificatorTerreinobject()
    {
        $gateway = $this->getGateway( );
        $terreinobjecten = $gateway->listTerreinobjectenByHuisnummerId( 887821 );
        $terreinobject = $gateway->getTerreinobjectByIdentificatorTerreinobject( $terreinobjecten[0]['identificatorTerreinobject'] );
        $this->assertInternalType( 'array', $terreinobject );
        $this->assertEquals( $terreinobject['identificatorTerreinobject'] , $terreinobjecten[0]['identificatorTerreinobject'] );
        $this->assertNotNull( $terreinobject['centerX']);
        $this->assertNotNull( $terreinobject['centerY']);
        $this->assertNotNull( $terreinobject['aardTerreinobjectCode']);
    }

    public function getHuisnummerWithSubAdresByHuisnummer( )
    {
       $huisnummer = $this->getGateway( )->getHuisnummerByHuisnummer( '111_1' , 1568 );
       $this->assertInternalType( 'array', $huisnummer );
       $this->assertEqual( $huisnummer['huisnummer'] , '111_1' );
       $this->assertEqual( $huisnummer['straatnaamId'] , 1568 );
    }
}
?>
