<?php
/**
 * @package     KVD.agavi
 * @subpackage  validation
 * @version     $Id$
 * @copyright   2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Validate whether the input is a valid NIS provincie number.
 * 
 * Parameters:
 * <ul>
 *  <li>'session_name' : Name of the session that knows where to find the datamapper for provincies.</li> 
 * </ul>
 * @package     KVD.agavi
 * @subpackage  validation
 * @since       19 aug 2008
 * @copyright   2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_ProvincieNISValidator extends KVDag_IdValidator
{

    /**
     * initialize 
     * 
     * @param AgaviContext  $context 
     * @param array         $parameters 
     * @param array         $arguments 
     * @param array         $errors 
     * @return void
     */
    public function initialize(AgaviContext $context, array $parameters = array(), array $arguments = array(), array $errors = array())
    {
        if ( !isset( $parameters['domain_object'] ) ) {
            $parameters['domain_object'] = 'KVDdo_AdrProvincie';
        }
        
        if ( !isset( $errors[''] ) ) {
            $errors[''] = 'U hebt een ongeldige provincie ingegeven.';
        }

        parent::initialize( $context, $parameters, $arguments, $errors );
    }

}
