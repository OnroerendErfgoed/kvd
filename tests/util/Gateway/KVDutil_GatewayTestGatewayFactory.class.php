<?php
/**
 * @package     KVD.util
 * @subpackage  Gateway
 * @version     $Id$
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Testfactory om factory manier van aanmaken gateway te testen.
 * 
 * @package     KVD.util
 * @subpackage  Gateway
 * @since       1.4
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_GatewayTestGatewayFactory
{
    public function create( $config )
    {
        return new KVDutil_GatewayTestGateway( $config );
    }
}
?>
