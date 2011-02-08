<?php
/**
 * @package     KVD.util
 * @subpackage  gateway
 * @version     $Id$
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_GatewayRegistryTest 
 * 
 * @package     KVD.util
 * @subpackage  gateway
 * @since       1.4.2
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_GatewayRegistryTest extends PHPUnit_Framework_TestCase
{
    private $gateway;

    private $factory;
    
    private $registry;

    public function setUp( )
    {
        $sessie = new StdClass();

        $this->factory = $this->getMock('KVDutil_GatewayFactory', array(), array( $sessie ) );

        $parameters = array (  'url'       => 'http://test.vioe.be/gateway',
                               'username'  => 'TESTUSER',
                               'pwd'       => 'TESTPWD' );

        $this->gateway = new KVDutil_GatewayTestGateway ( $parameters );

        $this->registry = new KVDutil_GatewayRegistry ( $this->factory );
        
    }

    public function tearDown( )
    {
        $this->gateway = null;
        $this->factory = null;
        $this->registry = null;
       
    }

    public function testGetGateway()
    {
        $this->factory->expects( $this->once() )->method( 'createGateway' )->will( $this->returnValue( $this->gateway ) );
        $this->assertSame ($this->registry->getGateway( 'KVDutil_GatewayTestGateway') , $this->gateway );
        $this->assertSame ($this->registry->getGateway( 'KVDutil_GatewayTestGateway') , $this->gateway );
    }

    /**
     * Controleer of het serialiseren van een gateway registry er voor zorgt 
     * dat de geladen gateways niet mee opgeslagen worden en achteraf terug 
     * worden aangemaakt.
     * 
     * @return void
     */
    public function testSerialise(  )
    {
        $this->factory->expects( $this->exactly( 2 ) )->method( 'createGateway' )->will( $this->returnValue( $this->gateway ) );
        $gw = $this->registry->getGateway( 'KVDutil_GatewayTestGateway' );
        $tmp = serialize($this->registry);
        $reg = unserialize($tmp);
        $gw2 = $this->registry->getGateway( 'KVDutil_GatewayTestGateway' );
    }


}
?>
