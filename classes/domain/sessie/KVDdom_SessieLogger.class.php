<?php
/**
 * KVDdom_SessieLogger
 * 
 * Deze class doet dienst als interface en een soort nullLogger tegelijk.
 * @package KVD.dom
 * @subpackage Sessie
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */

/**
 * KVDdom_SessieLogger
 * 
 * Deze class doet dienst als interface en een soort nullLogger tegelijk.
 * @package KVD.dom
 * @subpackage Sessie
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @since 9 aug 2006
 */
class KVDdom_SessieLogger
{
    /**
     * @param string $message.
     * @param boolean $debug Moet de message enkel gelogd worden onder debug omstandigheden ( standaard ) of altijd?
     * @return boolean Werd de data gelogd?
     */
    public function log ( $message , $debug = true )
    {
        return false;
    }
}
?>
