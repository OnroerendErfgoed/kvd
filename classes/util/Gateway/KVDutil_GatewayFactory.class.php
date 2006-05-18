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
class KVDutil_GatewayFactory {

    /**
     * @var array
     */
    private $config;
    
    /**
     * @param array $config Een array met configuratie-parameters voor de gateways.
     *                      Elke gateway is een sleutel in deze array, de dieper liggende sleutels worden doorgegeven aan de gateway voor de configuratie.
     */
    public function __construct ( $config )
    {
        $this->config = $config;
    }

    /**
     * @param string $gateway De naam van de gevraagde gateway.
     * @return KVDutil_Gateway Een gateway.
     */
    public function createGateway ( $gateway )
    {
        if ( !array_key_exists( $gateway, $this->config ) ) {
            throw new InvalidArgumentException ( "$gateway is geen gekende gateway!" );
        }
        return new $gateway ( $this->config[$gateway]);
    }
}
?>
