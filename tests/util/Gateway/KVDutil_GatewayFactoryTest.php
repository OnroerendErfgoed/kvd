<?php

class KVDutil_GatewayFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $gatewayFactory;

    function setUp( )
    {

        $config = array ( 'KVDgis_Crab2Gateway' => array (  'wsdl' => 'http://ws.agiv.be/crabws/nodataset.asmx?WSDL',
                                                            'username' => 'CRABUSER',
                                                            'password' => 'CRABPWD'
                                                            )
                        );
        $this->gatewayFactory = new KVDutil_GatewayFactory ( $config );
    }

    function tearDown( )
    {
        $this->gatewayFactory = null;
    }

    /**
     * testIllegalGatewayName 
     *
     * @expectedException InvalidArgumentException 
     * @access public
     * @return void
     */
    function testIllegalGatewayName( )
    {
        $this->gatewayFactory->createGateway ( 'OnbestaandeClass' );
    }

    function testExisting()
    {
        $gateway = $this->gatewayFactory->createGateway ( 'KVDgis_Crab2Gateway' );
        $this->assertNotNull ( $gateway );
        $this->assertType ( 'KVDutil_Gateway', $gateway );
    }
}
?>
