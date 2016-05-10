<?php
/**
 * @package    KVD.agavi
 * @subpackage validation
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Validator die controleert of iets een geldig Melanie of Bredero beschermingsnummer is.
 *
 * @package    KVD.agavi
 * @subpackage validation
 * @since      20 aug 2008
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
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
        $parameters['pattern'] =
            '/^(O|D)[BWOLA][0-9]{6}$|^4\.0[0-9]{1}[0-9]?\/[0-9]{5}\/[0-9]+\.[0-9]+$/';
        $parameters['match'] = true;

        if ( !isset( $errors[''] ) ) {
            $errors[''] = 'U hebt een ongeldig beschermingsnummer opgegeven.
            Een geldig Melanie nummer bestaat uit de hoofdletter O, gevolgd door een lettercode
            voor de provincie en een getal van 6 cijfers.
            Een geldig Bredero nummer bestaat uit een categorie, gevolgd door een schuine streep,
            een niscode, nog een schuine streep en een deeldossiernummer (met punt).';
        }

        parent::initialize( $context, $parameters, $arguments, $errors );
    }
}
