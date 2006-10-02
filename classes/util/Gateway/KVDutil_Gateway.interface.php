<?php
/**
 * @package KVD.util
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_Gateway 
 * 
 * @package KVD.util
 * @since jan 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDutil_Gateway {
    /**
     * De constructor aanvaardt een associatieve array met de nodige connectie parameters ( wsdl, username, paswoord, etc..)
     * @param array $parameters
     */
    public function __construct ( $parameters );
}
?>
