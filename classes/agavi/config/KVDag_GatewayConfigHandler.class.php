<?php
/**
 * @package    KVD.agavi
 * @subpackage config
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Config handler die een xml config file voor gateways omzet naar een volledig 
 * geladen {@link KVDutil_Gatewayfactory}.
 *
 * @package    KVD.agavi
 * @subpackage config
 * @since      1.4
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_GatewayConfigHandler extends AgaviXmlConfigHandler
{
    const XML_NAMESPACE = 'http://xml.vioe.be/agavi/config/parts/gateway/1.0';

    /**
     * Voer de config handler uit.
     * 
     * @param   AgaviXmlConfigDomDocument   $document 
     * @return  string                      Data die moet weggeschreven worden 
     *                                      naar de cache.
     * @throws  AgaviParseException         Indien de xml file niet geldig is.
     */
    public function execute( AgaviXmlConfigDomDocument $document)
    {
        // setting up our default namespace
        $document->setDefaultNamespace( self::XML_NAMESPACE, 'gateways');

        $data = array( );
        
        foreach( $document->getConfigurationElements( ) as $cfg) {
        
            if(!$cfg->has('gateways')) {
                continue;
            }

            $gateways = $cfg->get( 'gateways' );
            
            foreach( $gateways as $gateway) {
                $gn = $gateway->getAttribute( 'name' );
                if ( !isset( $data[$gn] ) ) {
                    $data[$gn] = array( );
                }
                $data[$gn] = $gateway->getAgaviParameters( $data[$gn] );
            }
            
        }

        $code = sprintf( 
            '$gatewayRegistry = new KVDutil_GatewayRegistry( new KVDutil_GatewayFactory( %s ) );', 
            var_export( $data, true )
        );
        
        return $this->generate( $code, $document->documentURI);
    
    }

}
?>
