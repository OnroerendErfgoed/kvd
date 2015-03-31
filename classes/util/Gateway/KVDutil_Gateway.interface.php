<?php
/**
 * @package    KVD.util
 * @subpackage gateway
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDutil_Gateway
 *
 * @package    KVD.util
 * @subpackage gateway
 * @since      jan 2006
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDutil_Gateway {

    /**
     * Maak een nieuwe gateway aan.
     *
     * De constructor aanvaardt een associatieve array met de nodige
     * connectie parameters ( wsdl, username, paswoord, etc..)
     * @param array $parameters
     */
    public function __construct ( $parameters = array( ) );
}
?>
