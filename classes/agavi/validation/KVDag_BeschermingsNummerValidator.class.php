<?php
/**
 * @package    KVD.agavi
 * @subpackage validation
 * @version    $Id$
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Validator die controleert of iets een geldig Melanie beschermingsnummer is.
 * 
 * @package    KVD.agavi
 * @subpackage validation
 * @since      20 aug 2008
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_BeschermingsNummerValidator extends AgaviRegexValidator
{
    /**
     * initialize 
     * 
     * @param AgaviContext $context 
     * @param array        $parameters 
     * @param array        $arguments 
     * @param array        $errors 
     * @return void
     */
    public function initialize( AgaviContext $context, 
                                array $parameters = array(), 
                                array $arguments = array(), 
                                array $errors = array() )
    {
        $parameters['pattern'] = '/^O[BWOLA][0-9]{6}$/';
        $parameters['match'] = true;

        if ( !isset( $errors[''] ) ) {
            $errors[''] = 'U hebt een ongeldig beschermingsnummer opgegeven. 
            Een geldig nummer bestaat uit de hoofdletter O, gevolgd door een lettercode 
            voor de provincie en een getal van 6 cijfers';
        }

        parent::initialize( $context, $parameters, $arguments, $errors );
    }
}
