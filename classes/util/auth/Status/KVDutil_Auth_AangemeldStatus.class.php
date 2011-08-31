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
 * Klasse waarnaar de KVDutil_Auth_Authenticatie class naar doordelegeert voor de afhandeling van 
 * de methoden uit dat object wanneer de actieve status 'aangemeld' is.
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      29 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_AangemeldStatus implements KVDutil_Auth_IStatus
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
    public function aanmelden($gebruikersnaam, $paswoord)
    {
        $this->afmelden();
        return $this->authenticator->aanmelden($gebruikersnaam, $paswoord);
    }

    /**
     * De authenticatie klasse verandert naar status afgemeld
     */
    public function afmelden()
    {
        $this->gebruiker = KVDutil_Auth_Gebruiker::newNull();
        $this->authenticator->setStatus( $this->authenticator->getAfgemeldStatus() );
        return true;
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
    public function isAangemeld()
    {
        return true;
    }

}
?>