<?php

class TestOfMapperFactory extends UnitTestCase
{
    private $_gatewayFactory;

    function setUp( )
    {

        $config = array ( 'KVDgis_Crab2Gateway' => array (  'wsdl' => 'http://ws.agiv.be/crabws/nodataset.asmx?WSDL',
                                                            'username' => CRABUSER,
                                                            'password' => CRABPWD
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
        $gateway = $this->_gatewayFactory->createGateway ( 'KVDgis_Crab2Gateway' );
        $this->assertNotNull ( $gateway );
        $this->assertIsA ( $gateway, 'KVDutil_Gateway');
    }

}
?>
