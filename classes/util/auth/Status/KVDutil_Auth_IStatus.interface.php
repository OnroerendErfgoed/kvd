<?php
/**
 * @package    KVD.util
 * @subpackage auth
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Interface die in compositie gebruikt wordt met de KVDutil_Auth_Authenticatie.
 * Het vertegenwoordigd een status die ofwel aangemeld, ofwel afgemeld is.
 * Het delegeert de opdrachten vd Authenticator door naar de juiste status klasse.
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      29 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDutil_Auth_IStatus
{    
    /**
     * @param string $gebruikersnaam
     * @param string $paswoord
     */
    public function aanmelden($gebruikersnaam, $paswoord);

    /**
     * @return  boolean true als het logout is gelukt
     */
    public function afmelden();

    /**
     * @return  KVDutil_Auth_Gebruiker  Het gebruikersobject
     */
    public function getGebruiker();
    
    /**
     * @return  boolean Geeft aan of de gebruiker is aangemeld
     */
    public function isAangemeld();
}
?>