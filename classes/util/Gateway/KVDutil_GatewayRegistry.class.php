<?php
/**
 * @package    KVD.util
 * @subpackage gateway
 * @version    $Id$
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_GatewayRegistry 
 * 
 * @package    KVD.util
 * @subpackage gateway
 * @since      jan 2006
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_GatewayRegistry {
    
    /**
     * @var KVDutil_GatewayFactory
     */
    private $factory;

    /**
     * @var array Een array met de verschillende al geladen gateways.
     */
    private $gateways;
    
    /**
     * @param KVDutil_GatewayFactory $factory
     */
    public function __construct ( $factory )
    {
        $this->factory = $factory;
        $this->gateways = array( );
    }

    /**
     * @param string $gateway
     * @return KVDutil_Gateway
     */
    public function getGateway ( $gateway )
    {
        if ( !array_key_exists( $gateway, $this->gateways) ) {
            $this->gateways[$gateway] = $this->factory->createGateway( $gateway );
        }
        return $this->gateways[$gateway];
    }

    /**
     * Indien we het object serialiseren, verwijderen we alle gekende gateways. 
     * Dit zorgt er voor dat eventuele resources die de gateways vast hebben 
     * niet meer geserialiseerd worden. 
     * 
     * @since 1.4.2
     * @return void
     */
    public function __sleep(  )
    {
        $this->gateways = array( );
        return array( );
    }
}
?>
