<?php


class TestOfGatewayRegistry extends UnitTestCase
{
    private $_gateway;

    private $_factory;
    
    private $_registry;

    function setUp( )
    {
        Mock::generate('KVDutil_GatewayFactory');
        $this->_factory = new MockKVDutil_GatewayFactory( $this );

        $parameters = array (   'wsdl' => 'http://ws.agiv.be/crabws/nodataset.asmx?WSDL',
                                'username' => 'test',
                                'password' => 'test');
        $this->_gateway = new KVDgis_Crab2Gateway ( $parameters );
        $this->_factory->setReturnReference( 'createGateway' , $this->_gateway , array ( 'KVDgis_Crab2Gateway' ) );
        $this->_factory->expectOnce( 'createGateway' , array( 'KVDgis_Crab2Gateway' ) );
        $this->_registry = new KVDutil_GatewayRegistry ( $this->_factory );
        
    }

    function tearDown( )
    {
        $this->_factory->tally( );
        $this->_gateway = null;
        $this->_factory = null;
        $this->_registry = null;
       
    }
    function testGetGateway()
    {
        $this->assertReference ($this->_registry->getGateway( 'KVDgis_Crab2Gateway') , $this->_gateway );
        $this->assertReference ($this->_registry->getGateway( 'KVDgis_Crab2Gateway') , $this->_gateway );
    }

}
?>
