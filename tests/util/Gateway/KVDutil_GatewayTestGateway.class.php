<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_GatewayTestGateway 
 * 
 * @package     PACKAGE
 * @version     //autogen//
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_GatewayTestGateway implements KVDutil_Gateway
{
    public function __construct( $parameters = array(  ))
    {
        $this->url = $parameters['url'];
        $this->username = $parameters['username'];
        $this->pwd = $parameters['pwd'];
        $this->options = $parameters['options'];
    }
}
?>
