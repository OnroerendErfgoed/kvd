<?php
class KVDutil_GatewayRegistryTest extends PHPUnit_Framework_TestCase
{
    private $gateway;

    private $factory;
    
    private $registry;

    function setUp( )
    {
        $sessie = new StdClass();

        $this->factory = $this->getMock('KVDutil_GatewayFactory', array(), array( $sessie ) );

        $parameters = array (  'url'       => 'http://test.vioe.be/gateway',
                               'username'  => 'TESTUSER',
                               'pwd'       => 'TESTPWD' );

        $this->gateway = new KVDutil_GatewayTestGateway ( $parameters );

        $this->factory->expects( $this->once() )->method( 'createGateway' )->will( $this->returnValue( $this->gateway ) );
        $this->registry = new KVDutil_GatewayRegistry ( $this->factory );
        
    }

    function tearDown( )
    {
        $this->gateway = null;
        $this->factory = null;
        $this->registry = null;
       
    }

    function testGetGateway()
    {
        $this->assertSame ($this->registry->getGateway( 'KVDutil_GatewayTestGateway') , $this->gateway );
        $this->assertSame ($this->registry->getGateway( 'KVDutil_GatewayTestGateway') , $this->gateway );
    }


}
?>
