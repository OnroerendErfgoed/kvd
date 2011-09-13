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
 * Deze Provider handelt data uit een array af
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      2 sep 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_ArrayProvider implements KVDutil_Auth_IProvider
{
    /**
     * array met data
     */
    private $connectie;
    
    /**
     * parameters
     */
    private $parameters;

    /**
     * constructor
     *
     * @param   $connectie  array met data
     * @param   $veldnamen  array met benamingen van LDAP velden
     *                      Lijst van array-keys:
     *                      <ul>
     *                          <li>voornaam</li>
     *                          <li>familienaam</li>
     *                          <li>email</li>
     *                          <li>telefoon</li>
     *                          <li>rol_naam</li>
     *                          <li>rol_beschrijving</li>
     *                          <li>rollen</li>
     *                      </ul>
     */
    public function __construct($connectie, $parameters = array())
    {
        $this->connectie = $connectie;
        $this->parameters = $parameters;
        $this->initialize();      
    }
    
    /*
     * Parameter-array vullen.
     */
    private function initialize()
    {
        $defaultWaarden = array(
            'wachtwoord'=>'paswoord',
            'voornaam'=>'voornaam',
            'familienaam'=>'familienaam',
            'email'=>'mail',
            'telefoon'=>'telefoon',
            'rol_naam'=>'naam',
            'rol_beschrijving'=>'beschrijving',
            'rollen'=>'rollen'
            );
        foreach( $defaultWaarden as $veld=>$default){
            $this->laadLegeParameters( $veld, $default);
        }
    }

    /**
     * Controleert of alle parameters aanwezig zijn in de door de consturctor doorgegeven parameters array
     * Indien niet, wordt de default waarde geladen
     *
     * @param   string      $veld
     * @param   string      default waarde
     * @return  void
     */
    private function laadLegeParameters( $veld, $default)
    {
        if ( !isset($this->parameters[$veld])){
            $this->parameters[$veld] = $default;
        }
    }

    /**
     * @param   string gebruikersnaam
     * @param   string $paswoord
     * @return  KVDutil_Auth_Gebruiker
     */
    public function aanmelden($gebruikersnaam, $paswoord)
    {
        if (array_key_exists($gebruikersnaam, $this->connectie) &&
                $this->connectie[$gebruikersnaam][$this->parameters['wachtwoord']] == $paswoord ) {
            $gebruiker = $this->connectie[$gebruikersnaam];
            return new KVDutil_Auth_Gebruiker(
                            $this,
                            $gebruikersnaam,
                            $gebruikersnaam,
                            $paswoord,
                            $gebruiker[$this->parameters['voornaam']],
                            $gebruiker[$this->parameters['familienaam']],
                            $gebruiker[$this->parameters['email']],
                            $gebruiker[$this->parameters['telefoon']],
                            new KVDutil_Auth_RolCollectie( array())
                        );
        }
        return false;
    }

    /**
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie naam
     *
     * @param   KVDutil_Auth_Gebruiker      $gebruiker
     * @param   string                      $applicatieNaam
     *                                      structuur: 'ou='.$applicatieNaam.',ou=productie,ou=groups,dc=vioe,dc=be'
     * @return  KVDutil_AuthRolCollectie    $rollen
     */
    public function getRollenVoorApplicatieNaam( KVDutil_Auth_Gebruiker $gebruiker, $applicatieNaam)
    {
        $results = array();

        foreach ( $this->connectie[$gebruiker->getId()][$this->parameters['rollen']][$applicatieNaam] as $rol=>$roldata){
            $results[] = new KVDutil_Auth_Rol(
                                $rol,
                                $roldata[$this->parameters['rol_naam']],
                                $roldata[$this->parameters['rol_beschrijving']]
                            );
        }
        
        return new KVDutil_Auth_RolCollectie( $results );
    }

    /*
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie
     *
     * @param   KVDutil_Auth_Gebruiker      $gebruiker
     * @param   Object  Een Applicatie-object dat de methode getId() bevat,
     *                  om dan op basis van het applicatieId de rollen op te halen
     *                  Id: 'ou=applicatie_id,ou=productie,ou=groups,dc=vioe,dc=be'
     * @return  KVDutil_AuthRolCollectie    $rollen
     */
    public function getRollenVoorApplicatie( KVDutil_Auth_Gebruiker $gebruiker, $applicatie)
    {
        return $this->getRollenVoorApplicatieNaam( $gebruiker, $applicatie->getId());
    }
}
?>