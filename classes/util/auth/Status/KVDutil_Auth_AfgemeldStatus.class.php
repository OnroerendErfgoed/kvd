<?php
/**
 * @package    KVD.util
 * @subpackage auth
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */

/**
 * Klasse waarnaar de KVDutil_Auth_Authenticatie class naar doordelegeert voor de afhandeling van
 * de methoden uit dat object wanneer de actieve status 'afgemeld' is.
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      29 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 */
class KVDutil_Auth_AfgemeldStatus implements KVDutil_Auth_IStatus
{
    /*
     * KVDutil_Auth_Authenticatie
     */
    protected $authenticatie;

    /*
     * KVDutil_Auth_IProvider
     */
    protected $provider;

    /*
     * constructor
     *
     * @param   KVDutil_Auth_Authenticatie  $authenticatie
     * @param   KVDutil_Auth_IProvider      $provider
     */
    public function __construct(KVDutil_Auth_Authenticatie $authenticatie,
            KVDutil_Auth_IProvider $provider)
    {
        $this->authenticatie = $authenticatie;
        $this->provider = $provider;
    }

    /**
     * Probeer de gebruiker aan te melden. Indien het lukt, verandert het status naar aangemeld.
     *
     * @param   string  $gebruikersnaam
     * @param   string  $paswoord
     * @return  boolean Geeft aan of het aanmelden gelukt is
     */
    public function aanmelden($gebruikersnaam, $paswoord)
    {
        if( $geb = $this->provider->aanmelden($gebruikersnaam, $paswoord)) {
            $this->authenticatie->setStatus( $this->authenticatie->getAangemeldStatus());
            $this->authenticatie->getAangemeldStatus()->setGebruiker( $geb );
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gebruiker is reeds afgemeld. Er moet niets gebeuren maar confirmeren dat gebruiker afgemeld is.
     *
     * @return  boolean true
     */
    public function afmelden()
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
    public function isAangemeld()
    {
        return false;
    }


}
?>
