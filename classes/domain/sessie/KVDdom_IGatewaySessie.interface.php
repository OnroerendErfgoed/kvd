<?php
/**
 * KVDdom_IGatwewaySessie 
 * 
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_IGatwewaySessie 
 * 
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_IGatewaySessie {

    /**
     * @param string $gateway Naam van de gevraagde gateway.
     * @return KVDutil_Gateway Een gateway naar een externe service.
     * @throws <b>LogicException</b> - Indien er geen manier is om aan een gateway te geraken..
     */
    public function getGateway ( $gateway );
}
?>
