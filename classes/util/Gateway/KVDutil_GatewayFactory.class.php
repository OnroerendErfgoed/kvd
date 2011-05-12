<?php
/**
 * @package KVD.util
 * @subpackage gateway
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_GatewayFactory 
 * 
 * @package KVD.util
 * @subpackage gateway
 * @since jan 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
     * @throws InvalidArgumentException Indien de gateway niet gekend is.
     */
    public function createGateway ( $gateway )
    {
        if ( !array_key_exists( $gateway, $this->config ) ) {
            throw new InvalidArgumentException ( "$gateway is geen gekende gateway!" );
        }
        if ( array_key_exists( 'factory', $this->config[$gateway] ) ) {
            $f = $this->config[$gateway]['factory'];
            if ( !array_key_exists( 'method', $f ) ) {
                throw new LogicException ( "De factory voor $gateway is onvolledig!" );
            }
            $m = $f['method'];
            if ( array_key_exists( 'class', $f ) ) {
                $c = $f['class'];
                return $c::$m($this->config[$gateway]);
            } else {
                return $m($this->config[$gateway] );
            }
        } else {
            return new $gateway ( $this->config[$gateway]);
        }
    }
}
?>
