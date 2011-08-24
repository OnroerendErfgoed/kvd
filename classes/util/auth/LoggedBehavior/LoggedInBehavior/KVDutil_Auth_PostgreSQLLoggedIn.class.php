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
 * In dit geval voor de database PostgreSQL
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_PostgreSQLLoggedIn extends KVDutil_Auth_LoggedIn
{
    /*
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie
     *
     * @param   Applicatie  $applicatie
     * @return  KVDutil_Auth_RolCollectie
     */
    private function findRollenByApplicatie( $applicatie )
    {
        $stmt = $this->authenticator->getDatabaseConnection()->prepare(
                $this->getRollenForGebruikerApplicatieIdStatement());
		$stmt->bindParam(1, $this->authenticator->getGebruiker()->getGebruikersnaam());
		$stmt->bindParam(2, $applicatie->getId());
		$stmt->execute();
		$rollen = array();
		while($rol = $stmt->fetch( PDO::FETCH_OBJ )) {
			$rollen[] = new KVDutil_Auth_Rol(
                                $rol->id,
                                $rol->naam,
                                $rol->beschrijving
                            );
		}
        //De array met objecten wordt in een KVDdom_DomainObjectCollection geplaatst.
		return new KVDutil_Auth_RolCollectie( $rollen );
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
        $stmt = $this->authenticator->getDatabaseConnection()->prepare(
                $this->getRollenForGebruikerApplicatieStatement());
		$stmt->bindParam(1, $this->authenticator->getGebruiker()->getGebruikersnaam());
		$stmt->bindParam(2, $applicatie_naam);
		$stmt->execute();
		$rollen = array();
		while($rol = $stmt->fetch( PDO::FETCH_OBJ )) {
			$rollen[] = new KVDutil_Auth_Rol(
                                $rol->id,
                                $rol->naam,
                                $rol->beschrijving
                            );
		}
        //De array met objecten wordt in een KVDdom_DomainObjectCollection geplaatst.
		return new KVDutil_Auth_RolCollectie( $rollen );
    }

    /*
     * @return  SQL statement
     */
    protected function getRollenForGebruikerApplicatieStatement()
	{
		return
		"SELECT rol.id, rol.naam, rol.beschrijving".
		" FROM rol, applicatie, gebruikerrol".
		" WHERE rol.applicatie_id = applicatie.id".
		" AND gebruikerrol.rol_id = rol.id".
		" AND gebruikerrol.startdatum < ".date("Y-m-d").
		" AND gebruikerrol.einddatum >= ".date("Y-m-d").
		" AND gebruikerrol.gebruiker_id = ?".
		" AND applicatie.naam = ?";
	}

    /*
     * @return  SQL statement
     */
	protected function getRollenForGebruikerApplicatieIdStatement()
	{
		return
		"SELECT rol.id, rol.naam, rol.beschrijving".
		" FROM rol, gebruikerrol".
		" WHERE gebruikerrol.rol_id = rol.id".
		" AND gebruikerrol.startdatum < ".date("Y-m-d").
		" AND gebruikerrol.einddatum >= ".date("Y-m-d").
		" AND gebruikerrol.gebruiker_id = ?".
		" AND rol.applicatie_id = ?";
	}
}
?>