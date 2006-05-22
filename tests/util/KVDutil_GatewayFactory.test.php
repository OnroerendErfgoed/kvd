<?php

class TestOfMapperFactory extends UnitTestCase
{
    private $_gatewayFactory;

    function setUp( )
    {
        $config = array ( 'KVDgis_Crab1Gateway' =>  array ( 'wsdl' => 'http://webservices.gisvlaanderen.be/crab/wscrab.asmx?WSDL',
                                                            'username' => 'VIOE',
                                                            'password' => 'GISTLIBE'
                                                            )
                        );
        $this->_gatewayFactory = new KVDutil_GatewayFactory ( $config );
    }

    function tearDown( )
    {
        $this->_gatewayFactory = null;
    }

    function testIllegalGatewayName( )
    {
        try {
            $this->_gatewayFactory->createGateway ( 'OnbestaandeClass' );
        } catch (InvalidArgumentException $e) {
            $this->pass( );
        } catch ( Exception $e) {
            $this->fail( 'Ongeldige exception opgevangen!');        
        }
    }

    function testExisting()
    {
        $gateway = $this->_gatewayFactory->createGateway ( 'KVDgis_Crab1Gateway' );
        $this->assertNotNull ( $gateway );
        $this->assertIsA ( $gateway, 'KVDutil_Gateway');
    }

}
?>
