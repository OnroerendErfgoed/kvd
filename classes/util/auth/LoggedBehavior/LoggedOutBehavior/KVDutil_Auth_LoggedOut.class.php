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
 * Abstracte klasse, die als basis dient voor alle LoggedOut klassen, specifiek per Database Management Systeem
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDutil_Auth_LoggedOut implements KVDutil_Auth_LoggedStatus
{
    /*
     * KVDutil_Auth_Authenticator
     */
    protected $authenticator;
    
    /*
     * constructor
     *
     * @param   KVDutil_Auth_Authenticator  $authenticator
     */
    public function __construct(KVDutil_Auth_Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    /**
     * Probeer de gebruiker aan te melden. Indien het lukt, verandert het status naar aangemeld.
     *
     * @param   string  $gebruikersnaam
     * @param   string  $paswoord
     * @return  boolean Geeft aan of het aanmelden gelukt is
     */
    public function logIn($gebruikersnaam, $paswoord)
    {
        if( $this->doLogin($gebruikersnaam, $paswoord)) {
            $this->authenticator->setState( $this->authenticator->getLogInState());
            $this->authenticator->getLoggedInState()->setGebruiker(
                    $this->doLogin($gebruikersnaam, $paswoord) );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Aan de hand van een gebruikersnaam en een paswoord halen we de gebruiker op.
     *
     * @param   string                  $gebruikersnaam
     * @param   string                  $paswoord
     * @return  KVDutil_Auth_Gebruiker  gebruikerobject als gebruiker werd aangemeld
     */
    abstract private function doLogin($gebruikersnaam, $paswoord){
        throw new Exception('De methode doLogin werd nog niet geïmplementeerd voor '. get_class( $this ) );
    }

    /**
     * Gebruiker is reeds afgemeld. Er moet niets gebeuren maar confirmeren dat gebruiker afgemeld is.
     *
     * @return  boolean true
     */
    public function logOut()
    {
        return true;
    }

    /**
     * Geef het gebruiker object terug
     *
     * @return KVDutil_Auth_NullGebruiker
     */
    public function getGebruiker()
    {
        return KVDutil_Auth_Gebruiker::newNull();
    }

    /**
     * Geef aan dat de gebruiker niet is aangemeld.
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return false;
    }

    /**
     * De gebruiker is niet aangemeld, we geven een lege array terug
     *
     * @param   Applicatie                  $applicatie
     * @return  KVDutil_Auth_RolCollectie   $rollen
     */
    public function getRollenVoorApplicatie($applicatie)
    {
        return new KVDutil_Auth_RolCollectie( array() );
    }

    /**
     * De gebruiker is niet aangemeld, we geven een lege array terug
     *
     * @param   string  $applicatieNaam
     * @return  KVDutil_Auth_RolCollectie   $rollen
     */
    public function getRollenVoorApplicatieNaam($applicatieNaam)
    {
        return new KVDutil_Auth_RolCollectie( array() );
    }
}
?>