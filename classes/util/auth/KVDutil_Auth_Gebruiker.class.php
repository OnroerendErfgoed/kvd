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
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_Gebruiker extends KVDdom_DomainObject implements KVDdom_Gebruiker, KVDdom_Nullable
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
     *
     * @param string $gebruikersnaam
     * @param string $wachtwoord
     * @param string $voornaam
     * @param string $familienaam
     * @param string $email
     * @param string $telefoon
     * @return void
     */
    public function __construct ( $gebruikersnaam, $wachtwoord ='', $voornaam ='', $familienaam ='',
            $email ='', $telefoon ='' )
    {
        parent::__construct( $gebruikersnaam );
        $this->gebruikersnaam = $gebruikersnaam;
        $this->wachtwoord = $wachtwoord;
        $this->voornaam = $voornaam;
        $this->familienaam = $familienaam;
        $this->email = $email;
        $this->telefoon = $telefoon;
        $this->loadState = self::LS_BASIS;
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
     * Stel de rollen voor deze gebruiker in.
     *
     * @param KVDutil_Auth_RolCollectie
     * @return void
     */
    public function setRollen($rollen)
    {
        $this->rollen = $rollen;
        $this->setLoadState( self::LS_ROLLEN );
    }

    /**
     * Controleert of de rollen voor deze gebruiker geladen zijn.
     * Indien niet, wordt er een Exception opgeworpen.
     *
     * Wanneer we deze class als superclass gebruiken voor een Gebruiker class in een applicatie,
     * kunnen we deze methode overschrijven, zodanig de mapper de rollen ophaalt in deze applicatie
     * {{{
         $mapper = $this->_sessie->getMapper("AUTHdo_Rol");
         $this->rollen = $mapper->findByGebruiker($this);
         $this->setLoadState( self::LS_ROLLEN );
     * }}}
     *
     * @throws  Exception   Indien de rollen niet geladen zijn
     * @return  void
     */
    public function checkRollen()
    {
        if(!$this->isLoadState(self::LS_ROLLEN)){
            throw new Exception('De rollen van de gebruiker zijn nog niet geladen.
                Gelieve de methoden getRollenVoorApplicatie of getRollenVoorApplicatieNaam te
                gebruiken om deze voor de huidige gebruiker te laden');
        }
    }

    /**
     * geeft de rollen voor deze gebruiker
     *
     * @return KVDutil_Auth_RolCollectie
     */
    public function getRollen()
    {
        $this->checkRollen();
        return $this->rollen->getImmutableCollection();
    }

    /**
     * isLoadState. Controleerd via Bitwise Operators welke informatie geladen is van het object.
     *
     * @param   integer $state
     * @return  boolean
     */
    public function isLoadState( $state )
    {
        return ( bool ) ( $state & $this->loadState );
    }

    /**
     * setLoadState. Geef aan welke informatie van het object geladen is.
     *
     * @param   integer $state
     * @return  void
     */
    public function setLoadState( $state )
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
        $this->rollen = new KVDutil_Auth_RolCollectie(array());
        $this->loadState = self::LS_ROLLEN;
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
}
?>