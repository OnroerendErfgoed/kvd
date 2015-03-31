<?php
/**
 * @package    KVD.util
 * @subpackage gateway
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDutil_GatewayUnavailableException
 *
 * @package    KVD.util
 * @subpackage gateway
 * @since      20 okt 2006
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDutil_GatewayUnavailableException extends Exception
{
    /**
     * gatewayName
     *
     * @var string
     */
    private $gatewayName;

    /**
     * soapFault
     *
     * @var SoapFault
     */
    private $soapFault;

    /**
     * @param string $message
     * @param string $gatewayName
     * @param SoapFault $soapFault
     */
    public function __construct ( $message , $gatewayName , $soapFault = null )
    {
        parent::__construct ( $message );
        $this->gatewayName = $gatewayName;
        $this->soapFault = $soapFault;
        $this->message .= !is_null( $soapFault ) ? ' [' . $soapFault->getMessage( ) . ']' : '';
    }

    /**
     * getSoapFault
     *
     * De SoapFault die door de SoapClient aangemaakt werd.
     * @return SoapFault
     */
    public function getException( )
    {
        return $this->soapFault;
    }

    /**
     * getGatewayName
     *
     * Naam van de Gateway die niet beschikbaar is.
     * @return string
     */
    public function getGatewayName( )
    {
        return $this->gatewayName;
    }

}
?>
