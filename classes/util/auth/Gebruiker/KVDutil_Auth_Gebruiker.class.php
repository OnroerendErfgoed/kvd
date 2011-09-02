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
 * DomainObject voor Gebruikers
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      30 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_Gebruiker implements KVDdom_Gebruiker, KVDdom_Nullable
{
    /**
     * Loadstate const: geeft aan dat de basis data (=alles wat in construct geladen wordt) van het object geladen is
     */
    const LS_BASIS = 1;

    /**
     * Loadstate const: geeft aan dat de rollen geladen zijn.
     */
    const LS_ROLLEN = 2;

    /**
     * loadState: Duidt aan in welke data in het object is geladen
     *
     * @var integer
     */
    protected $loadState;

    /*
     * KVDutil_Auth_IProvider
     */
    protected $provider;

    /**
     * Id nummer of unieke string id van het domain-object
     * @var string
     */
    protected $id;

    /**
     * gebruikersnaam
     *
     * @var string
     */
    protected $gebruikersnaam;

    /**
     * wachtwoord van de gebruiker, in gencrypteerde vorm.
     *
     * @var string
     */
    protected $wachtwoord;

    /**
     * voornaam
     *
     * @var string
     */
    protected $voornaam;

    /**
     * familienaam
     *
     * @var string
     */
    protected $familienaam;

    /**
     * email
     *
     * @var string
     */
    protected $email;

    /**
     * telefoon
     *
     * @var string
     */
    protected $telefoon;

    /**
     * rollen voor deze gebruiker
     *
     * @var KVDdom_DomainObjectCollection
     */
    protected $rollen;

    /**
     * __construct
     * ID en gebruikersnaam kunnen dezelfde zijn. Toch is dit niet altijd zo.
     * In LDAP zal het ID attribuut gebruikt worden om de DN bij te houden.
     * Volledige DN is nodig om transacties met de ldap datasource mogelijk te maken
     *
     * @param KVDutil_Auth_IProvider $provider
     * @param string Unieke naam van gebruiker, DN of gebruikersnaam
     * @param string $gebruikersnaam
     * @param string $wachtwoord
     * @param string $voornaam
     * @param string $familienaam
     * @param string $email
     * @param string $telefoon
     * @return void
     */
    public function __construct ( KVDutil_Auth_IProvider $provider, $id, $gebruikersnaam, $wachtwoord ='', $voornaam ='', $familienaam ='',
            $email ='', $telefoon ='' )
    {
        $this->id = $id;
        $this->provider = $provider;
        $this->gebruikersnaam = $gebruikersnaam;
        $this->wachtwoord = $wachtwoord;
        $this->voornaam = $voornaam;
        $this->familienaam = $familienaam;
        $this->email = $email;
        $this->telefoon = $telefoon;
        $this->loadState = $this->setLoadState(self::LS_BASIS);
    }

    /**
     * Geeft het Id nummer of Id string van dit object terug.
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * getGebruikersnaam
     *
     * @return string
     */
    public function getGebruikersnaam( )
    {
        return $this->gebruikersnaam;
    }

    /**
     * geëncrypteerd wachtwoord van de gebruiker opvragen
     *
     * @return string
     */
    public function getWachtwoord()
    {
        return $this->wachtwoord;
    }

    /**
     * getVoornaam
     *
     * @return string
     */
    public function getVoornaam( )
    {
        return $this->voornaam;
    }

    /**
     * getFamilienaam
     *
     * @return string
     */
    public function getFamilienaam( )
    {
        return $this->familienaam;
    }

    /**
     * getEmail
     *
     * @return string
     */
    public function getEmail( )
    {
        return $this->email;
    }

    /**
     * telefoonnummer van gebruiker
     *
     * @return string
     */
    public function getTelefoon()
    {
        return $this->telefoon;
    }

    /**
     * getOmschrijving
     *
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->voornaam . ' ' . $this->familienaam;
    }

    /**
     * Geef het type van een DomainObject terug. Onder andere nodig om de (@link KVDdom_DataMapper) te kunnen vinden.
     * @return string
     */
    public function getClass()
    {
        return get_class( $this );
    }
    
    /**
     * __toString
     *
     * @return string
     */
    public function __toString( )
    {
        return $this->getOmschrijving( );
    }

    /**
     * Controleert of de rollen voor deze gebruiker geladen zijn.
     * 
     * @return  boolean
     */
    public function checkRollen()
    {
        if($this->isLoadState(self::LS_ROLLEN)){
            return true;
        }
        return false;
    }

    /**
     * Geef de rollen van de huidige gebruiker terug voor de opgegeven applicatie
     *
     * @param  Applicatie  $applicatie
     * @return KVDutil_Auth_RolCollectie   $rollen
     */
    public function getRollenVoorApplicatie($applicatie)
    {
        if( !$this->checkRollen() ){
            $this->rollen = $this->provider->getRollenVoorApplicatie( $this, $applicatie);
            $this->loadState = $this->setLoadState(self::LS_ROLLEN);
        }
        return $this->rollen->getImmutableCollection();
    }

    /**
     * Geef de rollen van de huidige gebruiker terug voor de opgegeven applicatie naam
     *
     * @param  string  $applicatieNaam
     * @return KVDutil_Auth_RolCollectie  $rollen
     */
    public function getRollenVoorApplicatieNaam($applicatieNaam)
    {
        if( !$this->checkRollen() ){
            $this->rollen = $this->provider->getRollenVoorApplicatieNaam( $this, $applicatie);
            $this->loadState = $this->setLoadState(self::LS_ROLLEN);
        }
        return $this->rollen->getImmutableCollection();
    }

    /**
     * isLoadState. Controleerd via Bitwise Operators welke informatie geladen is van het object.
     *
     * @param   integer $state
     * @return  boolean
     */
    private function isLoadState( $state )
    {
        return ( bool ) ( $state & $this->loadState );
    }

    /**
     * setLoadState. Geef aan welke informatie van het object geladen is.
     *
     * @param   integer $state
     * @return  void
     */
    private function setLoadState( $state )
    {
        if ( !( $this->loadState & $state ) ) {
            $this->loadState += $state;
        }
    }

    /**
     * isNull
     *
     * @return boolean false
     */
    public function isNull()
    {
        return false;
    }

    /**
     * newNull
     *
     * @return KVDutil_AUTH_Gebruiker
     */
    public static function newNull( )
    {
        return new KVDutil_AUTH_NullGebruiker( );
    }
}

/**
 * Leeg domainObject van een gebruiker
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      16 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_AUTH_NullGebruiker extends KVDutil_AUTH_Gebruiker {

    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->id = null;
        $this->gebruikersnaam = 'anoniem';
        $this->voornaam = 'Anonieme';
        $this->familienaam = 'Gebruiker';
        $this->email = null;
    }

    /**
     * isNull
     *
     * @return boolean true
     */
    public function isNull()
    {
        return true;
    }

    /**
     * Geef het type van een DomainObject terug. Onder andere nodig om de (@link KVDdom_DataMapper) te kunnen vinden.
     *
     * @return string
     */
    public function getClass()
    {
        return "KVDutil_AUTH_Gebruiker";
    }

    /*
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie
     *
     * @param   Applicatie  $applicatie
     * @return  KVDutil_Auth_RolCollectie
     */
    public function getRollenVoorApplicatie($applicatie)
    {
        return new KVDutil_Auth_RolCollectie( array() );
    }

    /**
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie naam
     * In onze LDAP server vertegenwoordigd het veld description de volledige naam van de applicatie
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