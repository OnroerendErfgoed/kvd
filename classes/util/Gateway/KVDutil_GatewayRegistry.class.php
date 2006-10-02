<?php
/**
 * @package KVD.util
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_GatewayRegistry 
 * 
 * @package KVD.util
 * @since jan 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_GatewayRegistry {
    
    /**
     * @var KVDutil_GatewayFactory
     */
    private $_factory;

    /**
     * @var array Een array met de verschillende al geladen gateways.
     */
    private $gateways;
    
    /**
     * @param KVDutil_GatewayFactory $factory
     */
    public function __construct ( $factory )
    {
        $this->_factory = $factory;
        $this->gateways = array( );
    }

    /**
     * @param string $gateway
     * @return KVDutil_Gateway
     */
    public function getGateway ( $gateway )
    {
        if ( !array_key_exists( $gateway, $this->gateways) ) {
            $this->gateways[$gateway] = $this->_factory->createGateway( $gateway );
        }
        return $this->gateways[$gateway];
    }
}
?>
