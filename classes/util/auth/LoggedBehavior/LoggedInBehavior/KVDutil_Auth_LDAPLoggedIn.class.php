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
 * Deze class zorgt voor afhandeling van de taken van de authenticator in LoggedIn Status
 * In dit geval voor de database LDAP
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_LDAPLoggedIn extends KVDutil_Auth_LoggedIn
{
    /*
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie
     *
     * @param   Applicatie  $applicatie
     * @return  KVDutil_Auth_RolCollectie
     */
    private function findRollenByApplicatie( $applicatie )
    {
        $results = array();

        $searchbase = 'ou='.$applicatie->getId().',ou=productie,ou=groups,dc=vioe,dc=be';

        $filter = Net_LDAP2_Filter::create( 'uniqueMember', 'contains', 
                'uid='.$this->gebruiker->getGebruikersnaam().',ou=people,dc=vioe,dc=be');
        
        $options = array(
            'scope' => 'sub',
            'attributes' => array(
                'cn',
                'description'
                )
            );

        //Voer zoekactie uit op boven meegegeven searchbase met de opgegeven options en filters
        $search = $this->authenticator->getDatabaseConnection()->search( $searchbase, $filter, $options);
        if (Net_LDAP2::isError($search)) {
            throw new Exception( $search->getMessage() );
        }

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


    /**
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie naam
     * In onze LDAP server vertegenwoordigd het veld description de volledige naam van de applicatie
     *
     * @param   string  $applicatieNaam
     * @return  KVDutil_Auth_RolCollectie   $rollen
     */
    private function findRollenByApplicatieNaam( $applicatieNaam )
    {
        $results = array();

        $searchbase = 'ou='.$applicatieNaam.',ou=productie,ou=groups,dc=vioe,dc=be';

        $filter = Net_LDAP2_Filter::create( 'uniqueMember', 'contains',
                'uid='.$this->gebruiker->getGebruikersnaam().',ou=people,dc=vioe,dc=be');

        $options = array(
            'scope' => 'sub',
            'attributes' => array(
                'cn',
                'description'
                )
            );

        //Voer zoekactie uit op boven meegegeven searchbase met de opgegeven options en filters
        $search = $this->authenticator->getDatabaseConnection()->search( $searchbase, $filter, $options);
        if (Net_LDAP2::isError($search)) {
            throw new Exception( $search->getMessage() );
        }

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

    /**
     *
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie beschrijving
     * In onze LDAP server vertegenwoordigd het veld description de volledige naam van de applicatie
     *
     * @param   string                      $applicatieBeschrijving
     * @return  KVDutil_Auth_RolCollectie   $rollen
     */
    /*
     * 
     * Deze functie is voorlopig nog afgesloten. Indien we ze willen activeren moet ze via de
     * superclass aangeroepen worden via een public methode en deze code uncommenten
     *
     *
    private function findRollenByApplicatieBeschrijving( $applicatieBeschrijving )
    {
        $filter = Net_LDAP2_Filter::create( 'description', 'contains', $applicatieBeschrijving);

        $searchbase = 'ou=productie,ou=groups,dc=vioe,dc=be';

        $options = array(
            'scope' => 'one',
            'attributes' => array(
                'cn',
                'description'
                )
            );

        //Voer zoekactie uit op de searchbase met opgegeven options, met de beschrijving als filter
        $search = $this->authenticator->getDatabaseConnection()->search( $searchbase, $filter, $options);
        if (Net_LDAP2::isError($search)) {
            throw new Exception( $search->getMessage() );
        }

        //Er wordt gezocht naar ID's die de beschrijving matchen.
        foreach ( $entries as $entry) {
            $results = $entry->getValue( $this->id, 'single');
        }

        if( count( $results ) == 0 ){
            return new KVDutil_Auth_RolCollectie( array() );
        }

        if( count( $results) > 1){
            throw new Exception('Zoekactie op basis van een applicatienaam (beschrijving) gaf meerdere resultaten (aantal: '
                    .$results.' resultaten). Er kon geen uniek ID opgehaald worden.' );
        }

        return $this->findRollenByApplicatieNaam( $results[0]);
    }
    */
}
?>