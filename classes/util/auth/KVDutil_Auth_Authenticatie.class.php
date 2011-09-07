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
 * Klasse die de authenticatie van een gebruiker, en het ophalen van zijn rollen afhandeld
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      29 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_Authenticator
{
    /**
     * Statusobject voor het afhandelen van functies terwijl de gebruiker is aangemeld
     *
     * @var KVDutil_Auth_Status
     */
    private $aangemeldStatus;

    /**
     * Statusobject voor het afhandelen van functies terwijl de gebruiker is afgemeld
     *
     * @var KVDutil_Auth_Status
     */
    private $afgemeldStatus;

    /**
     * Actieve status van het object
     * 
     * @var KVDutil_Auth_Status
     */
    private $status;

    /**
     * Object dat verkeer met datasource behandelt
     *
     * @var KVDutil_auth_IProvider
     */
    private $provider;

    /**
     * constructor
     * 
     * @param   KVDutil_auth_IProvider  Object dat verkeer met datasource behandelt.
     */
    public function __construct( KVDutil_auth_IProvider $provider)
    {
        $this->aangemeldStatus = new KVDutil_Auth_AangemeldStatus($this, $provider);
        $this->afgemeldStatus = new KVDutil_Auth_AfgemeldStatus($this, $provider);
        $this->status = $this->afgemeldStatus;
        $this->provider = $provider;
    }

    /**
     * Aanmelden met opgegeven gebruikersnaam en paswoord.
     * 
     * @param   string  $gebruikersnaam
     * @param   string  $paswoord
     */
    public function aanmelden($gebruikersnaam, $paswoord)
    {
        return $this->status->aanmelden($gebruikersnaam, $paswoord);
    }

    /**
     * Afmelden van de gebruiker.
     */
    public function afmelden()
    {
        return $this->status->afmelden();
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
     * Verander de actieve status van het object
     * 
     * @param   KVDutil_Auth_Status $status
     */
    public function setStatus(KVDutil_Auth_ILoggedStatus $status)
    {
        $this->status = $status;
    }

    /* --------------------------------------------------------------------------------------------
     * Getters voor het ophalen van de status instanties.
     *
     * Deze getters maken geen deel uit van de API. Ze worden gebruikt om de actieve status van
     * ons object aan te passen naar 1 van de eerder aangemaakte status instanties
     * Dit gebeurt telkens via de methode setState( getxStatus );
     * ------------------------------------------------------------------------------------------*/

    /**
     * Geef de aangemeldStatus instantie terug. Maakt geen deel van de API
     *
     * @return  KVDutil_Auth_AangemeldStatus
     */
    public function getAangemeldStatus()
    {
        return $this->aangemeldStatus;
    }

    /**
     * Geef de afgemeldStatus instantie terug. Maakt geen deel van de API
     *
     * @return  KVDutil_Auth_AfgemeldStatus
     */
    public function getAfgemeldStatus()
    {
        return $this->afgemeldStatus;
    }

}
?>