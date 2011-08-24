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
 * Abstracte klasse, die als basis dient voor een klasse per specifiek Database Management Systeem
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDutil_Auth_Authenticator
{
    /**
     * Statusobject voor het afhandelen van functies terwijl de gebruiker is aangemeld
     *
     * @var KVDutil_Auth_ILoggedStatus
     */
    protected $loggedInState;

    /**
     * Statusobject voor het afhandelen van functies terwijl de gebruiker is afgemeld
     *
     * @var KVDutil_Auth_ILoggedStatus
     */
    protected $loggedOutState;

    /**
     * Huidige status van het object
     * 
     * @var KVDutil_Auth_ILoggedStatus
     */
    private $status;

    /**
     * Een connectie naar de databank
     */
    private $databaseConnection;

    /**
     * constructor
     * 
     * @param   $databaseConnection   een databank connectie.
     */
    public function __construct($databaseConnection)
    {
        $this->status = $this->loggedOutState;
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Log in met gegeven gebruiker en paswoord.
     * 
     * @param   string  $gebruikersnaam
     * @param   string  $paswoord
     */
    public function logIn($gebruikersnaam, $paswoord)
    {
        return $this->status->logIn($gebruikersnaam, $paswoord);
    }

    /**
     * Indien een gebruiker is ingelogd, uitloggen.
     */
    public function logOut()
    {
        return $this->status->logOut();
    }

    /**
     * Geeft het gebruikersobject terug.
     *
     * @return  KVDutil_Auth_Gebruiker
     */
    public function getGebruiker()
    {
        return $this->status->getGebruiker();
    }

    /**
     * Geeft weer of een gebruiker is aangemeld.
     * 
     * @return  boolean
     */
    public function isAuthenticated()
    {
        return $this->status->isAuthenticated();
    }

    /**
     * Geeft een array van rollen voor deze gebruiker weer.
     *
     * @param   Applicatie  $applicatie
     * @return  KVDutil_Auth_RolCollectie
     */
    public function getRollenVoorApplicatie($applicatie)
    {
        return $this->status->getRollenVoorApplicatie($applicatie);
    }

    /**
     * Geeft een array van rollen voor deze gebruiker weer.
     *
     * @param   string  $applicatieNaam
     * @return  KVDutil_Auth_RolCollectie
     */
    public function getRollenVoorApplicatieNaam($applicatieNaam)
    {
        return $this->status->getRollenVoorApplicatieNaam($applicatieNaam);
    }

    /**
     * Stel de huidige status van het object in
     * 
     * @param   KVDutil_Auth_ILoggedStatus  $status
     */
    public function setState(KVDutil_Auth_ILoggedStatus $status)
    {
        $this->status = $status;
    }

    /**
     * Geef instantie van de databaseconnectie
     *
     * @return  databaseconnectie
     */
    public function getDatabaseConnection()
    {
        return $this->databaseConnection;
    }

    /**
     * Geef instantie van de LoggedInState
     *
     * @return  KVDutil_Auth_ILoggedStatus
     */
    public function getLoggedInState()
    {
        return $this->loggedInState;
    }

    /**
     * Geef instantie van de LoggedOutState
     *
     * @return  KVDutil_Auth_ILoggedStatus
     */
    public function getLoggedOutState()
    {
        return $this->loggedOutState;
    }

}
?>