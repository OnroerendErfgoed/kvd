<?php

class KVDutil_GatewayFactoryTest extends PHPUnit_Framework_TestCase
{
    protected $gatewayFactory;

    function setUp( )
    {
        $config = array ( 'KVDgis_Crab2Gateway'         => array (  'wsdl' => 'http://crab.agiv.be/wscrab/WsCrab.svc?wsdl' ),
                          'KVDutil_GatewayTestGateway'  => array (  'url'       => 'http://test.vioe.be/gateway',
                                                                    'username'  => 'TESTUSER',
                                                                    'pwd'       => 'TESTPWD' )
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
        $gateway = $this->gatewayFactory->createGateway ( 'KVDutil_GatewayTestGateway' );
        $this->assertNotNull ( $gateway );
        $this->assertInstanceOf ( 'KVDutil_Gateway', $gateway );
    }

    public function testGatewayInjection( )
    {
        $config = array( 
              'KVDutil_GatewayTestGateway'  => array (  'url'       => 'http://test.vioe.be/gateway',
                                                        'username'  => 'TESTUSER',
                                                        'pwd'       => 'TESTPWD',
                                                        'factory'   => array(   'class'     => 'KVDutil_GatewayTestGatewayFactory',
                                                                                'method'    => 'create') )
        );
        $gatewayFactory = new KVDutil_GatewayFactory ( $config );
        $gateway = $gatewayFactory->createGateway ( 'KVDutil_GatewayTestGateway' );
        $this->assertNotNull ( $gateway );
        $this->assertInstanceOf ( 'KVDutil_Gateway', $gateway );
    }
}
?>
