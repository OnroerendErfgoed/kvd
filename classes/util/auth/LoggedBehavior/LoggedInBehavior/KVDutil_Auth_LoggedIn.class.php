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
 * Abstracte klasse, die als basis dient voor alle LoggedIn klassen, specifiek per Database Management Systeem
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDutil_Auth_LoggedIn implements KVDutil_Auth_LoggedStatus
{
    /*
     * KVDutil_Auth_Authenticator
     */
    protected $authenticator;

    /*
     * KVDutil_Auth_Gebruiker
     */
    private $gebruiker;

    /*
     * constructor
     *
     * @param   KVDutil_Auth_Authenticator  $authenticator
     */
    public function __construct(KVDutil_Auth_Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
        $this->gebruiker = new KVDutil_Auth_Gebruiker();
    }

    /**
     * De authenticatie klasse verandert naar status afgemeld.
     * Vervolgens zal de authenticator de gebruiker opnieuw aanmelden.
     * 
     * @param string $gebruikersnaam
     * @param string $paswoord
     */
    public function logIn($gebruikersnaam, $paswoord)
    {
        $this->logOut();
        return $this->authenticator->logIn($gebruikersnaam, $paswoord);
    }

    /**
     * De authenticatie klasse verandert naar status afgemeld
     */
    public function logOut()
    {
        $this->authenticator->setState( $this->authenticator->getLogoutState() );
    }

    /**
     * Geef het gebruiker object terug
     *
     * @return KVDutil_Auth_Gebruiker
     */
    public function getGebruiker()
    {
        return $this->gebruiker;
    }

    /**
     * Verander het gebruikersobject.
     *
     * @param   KVDutil_Auth_Gebruiker
     * @return  void
     */
    public function setGebruiker( KVDutil_Auth_Gebruiker $gebruiker )
    {
        $this->gebruiker = $gebruiker;
    }

    /**
     * Geef aan dat de gebruiker is aangemeld
     *
     * @return boolean
     */
    public function isAuthenticated()
    {
        return true;
    }

    /**
     * Geef de rollen van de huidige gebruiker terug voor het opgegeven applicatie id
     *
     * @param  Applicatie  $applicatie
     * @return KVDutil_Auth_RolCollectie   $rollen
     */
    public function getRollenVoorApplicatie($applicatie)
    {
        if( !$this->gebruiker->isLoadState( KVDutil_Auth_Gebruiker::LS_ROLLEN ) ){
            $this->gebruiker->setRollen($this->findRollenByApplicatie($applicatie));
        }
        return $this->gebruiker->getRollen();
    }

    /**
     * Geef de rollen van de huidige gebruiker terug voor het opgegeven applicatie naam
     *
     * @param  string  $applicatieNaam
     * @return KVDutil_Auth_RolCollectie  $rollen
     */
    public function getRollenVoorApplicatieNaam($applicatieNaam)
    {
        if( !$this->gebruiker->isLoadState( KVDutil_Auth_Gebruiker::LS_ROLLEN ) ){
            $this->gebruiker->setRollen($this->findRollenByApplicatieNaam($applicatieNaam));
        }
        return $this->gebruiker->getRollen();
    }
    
    /**
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie id
     *
     * @param   Appliatie                       $applicatie
     * @return  KVDutil_Auth_RolCollectie       $rollen
     */
    abstract public function findRollenByApplicatie($applicatie)
    {
        throw new Exception('De methode findRollenByApplicatie werd nog niet geïmplementeerd voor '. get_class( $this ) );
    }

    /**
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie naam
     *
     * @param   string  $applicatieNaam
     * @return  KVDutil_Auth_RolCollectie   $rollen
     */
    abstract public function findRollenByApplicatieNaam($applicatieNaam)
    {
        throw new Exception( 'De methode findRollenByApplicatieNaam werd nog niet geïmplementeerd voor '. get_class( $this ));
    }
}
?>