<?php
/**
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @package KVD.util
 * @version $Id$
 */

/**
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @package KVD.util
 * @since 1.0.0
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
