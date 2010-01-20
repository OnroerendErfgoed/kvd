<?php
class KVDutil_GatewayRegistryTest extends PHPUnit_Framework_TestCase
{
    private $gateway;

    private $factory;
    
    private $registry;

    public static function main()
    {
        echo 'main';

        require_once 'PHPUnit/TextUI/TestRunner.php';

        $suite  = new PHPUnit_Framework_TestSuite('KVDutil_GatewayRegistryTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    function setUp( )
    {
        echo 'setting up';
        $sessie = new StdClass();
        $this->factory = $this->getMock('KVDutil_GatewayFactory', array(), array( $sessie ) );

        $parameters = array (   'wsdl' => 'http://ws.agiv.be/crabws/nodataset.asmx?WSDL',
                                'username' => 'test',
                                'password' => 'test');
        $this->gateway = new KVDgis_Crab2Gateway ( $parameters );

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
        //$this->assertSame ($this->registry->getGateway( 'KVDgis_Crab2Gateway') , $this->gateway );
        //$this->assertSame ($this->registry->getGateway( 'KVDgis_Crab2Gateway') , $this->gateway );
    }


}
?>
