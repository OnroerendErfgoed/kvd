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
 * Deze class zorgt voor afhandeling van de taken van de authenticator in LoggedOut Status
 * In dit geval voor de database LDAP
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_LDAPLoggedOut extends KVDutil_Auth_LoggedOut
{
    /*
     * Deze functie zal controle uitvoeren of de combinatie
     * gebruikersnaam-paswoord in de databank te vinden is.
     * Indien authenticatie gelukt is, wordt er op basis van de parameters een Gebruiker aangemaakt
     *
     * @param   string  $gebruikersnaam
     * @param   string  $paswoord
     * @return  KVDutil_Auth_Gebruiker  gebruikerobject als gebruiker werd aangemeld
     */
    private function doLogin($gebruikersnaam, $paswoord){
        //FASE 1: We gaan proberen binden met de ldap.
        //Lukt de bind niet, dan bestaat ofwel de gebruikersnaam niet, ofwel is er een verkeerd wachtwoord opgegeven.
        $ldap = $this->authenticator->getDatabaseConnection( );

        $bindname = 'uid='.$gebruikersnaam.',ou=people,dc=vioe,dc=be';

        //Controleer of gebruiker kan met opgegeven paswoorden met de ldap kan binden
        $res = $ldap->bind( $bindname, $paswoord );
        if (Net_LDAP2::isError($res)) {
            return false;
        }

        //FASE 2: Bind is gelukt, we gaan gebruiker ophalen
        //Haal de entry op uit de ldap
        $entry = $ldap->getEntry( $bindname );

        //Indien het object niet bestaat in ldap, geven alsnog op dat de login mislukt is
        if (Net_LDAP2::isError($entry)){
            return false;
        }

        $gebruiker = new KVDutil_Auth_Gebruiker(
                            $gebruikersnaam,
                            $paswoord,
                            $entry->getValue('givenName', 'single'),
                            $entry->getValue('sn', 'single'),
                            $entry->getValue('mail', 'single'),
                            $entry->getValue('telephoneNumber', 'single'),
                            new KVDutil_Auth_RolCollectie( array())
                        );
        return $gebruiker;
    }
}
?>