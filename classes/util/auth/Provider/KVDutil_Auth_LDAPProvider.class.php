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
 * Deze Provider handelt het verkeer met een LDAP datasource af
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      29 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_LDAPProvider implements KVDutil_Auth_IProvider
{
    /**
     * connectie naar een LDAP databank
     */
    private $connectie;

    /**
     * constructor
     *
     * @param   $connectie  connectie naar een LDAP databank.
     */
    public function __construct($connectie)
    {
        $this->connectie = $connectie;
    }
    
    /**
     * @param   string User DN (voorbeeld: uid=goessebr,ou=people,dc=vioe,dc=be)
     * @param   string $paswoord
     * @return  KVDutil_Auth_Gebruiker
     */
    public function aanmelden($gebruikersnaam, $paswoord)
    {
        //FASE 1: We gaan proberen binden met de ldap.
        //Lukt de bind niet, dan bestaat ofwel de gebruikersnaam niet, ofwel is er een verkeerd wachtwoord opgegeven.
        $res = $this->connectie->bind( $gebruikersnaam, $paswoord );
        if (Net_LDAP2::isError($res)) {
            return false;
        }

        //FASE 2: Bind is gelukt, we gaan gebruiker ophalen
        //Haal de entry op uit de ldap
        $entry = $this->connectie->getEntry( $gebruikersnaam );

        //Indien het object niet bestaat in ldap, geven alsnog op dat de login mislukt is
        if (Net_LDAP2::isError($entry)){
            return false;
        }

        $gebruiker = new KVDutil_Auth_Gebruiker(
                            $this,
                            $id,
                            $entry->getValue('uid', 'single'),
                            $paswoord,
                            $entry->getValue('givenName', 'single'),
                            $entry->getValue('sn', 'single'),
                            $entry->getValue('mail', 'single'),
                            $entry->getValue('telephoneNumber', 'single'),
                            new KVDutil_Auth_RolCollectie( array())
                        );
        return $gebruiker;
    }

    /**
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie naam
     * In onze LDAP server vertegenwoordigd het veld description de volledige naam van de applicatie
     *
     * @param   KVDutil_Auth_Gebruiker      $gebruiker
     * @param   string                      $applicatieNaam
     *                                      structuur: 'ou='.$applicatieNaam.',ou=productie,ou=groups,dc=vioe,dc=be'
     * @return  KVDutil_AuthRolCollectie    $rollen
     */
    public function getRollenVoorApplicatieNaam( KVDutil_Auth_Gebruiker $gebruiker, $applicatieNaam)
    {
        $filter = Net_LDAP2_Filter::create( 'uniqueMember', 'contains', $gebruiker->getId());

        $options = array(
            'scope' => 'sub',
            'attributes' => array(
                'cn',
                'description'
                )
            );

        //Voer zoekactie uit op boven meegegeven searchbase met de opgegeven options en filters
        $search = $this->connectie->search( $applicatieNaam, $filter, $options);
        if (Net_LDAP2::isError($search)) {
            throw new Exception( $search->getMessage() );
        }

        $results = array();
        //objecten worden 1 voor 1 volledig geladen en in een array geplaatst.
        foreach ( $search as $dn=>$entry) {
            $results[$dn] = new KVDutil_Auth_Rol(
                                $dn,
                                $entry->getValue('cn', 'single'),
                                $entry->getValue('description', 'single')
                            );
        }

        //De array met objecten wordt in een KVDdom_DomainObjectCollection geplaatst.
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
        return $this->getRollenVoorApplicatieNaam( $gebruiker, $applicatie->getId() );
    }
}
?>