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
 * Deze Provider handelt het verkeer met een PDO datasource af
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      31 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_PDOProvider implements KVDutil_Auth_IProvider
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
     * @param   string 
     * @param   string $paswoord
     * @return  KVDutil_Auth_Gebruiker
     */
    public function aanmelden($gebruikersnaam, $paswoord)
    {
        $pass = $this->encrypt($paswoord);
		$gebruiker = $this->getGebruiker($gebruikersnaam, $pass);
		if(!$gebruiker) {
			return false;
		} else {
			return $gebruiker;
		}
    }

    private function encrypt($woord)
	{
		$col = "PASSWORD('".addslashes($woord)."')";
		$stmt = $this->db->prepare($this->getEncryptPasswordStatement());
		$stmt->bindParam(1, $woord);
		$stmt->execute();
		if($stmt->rowCount() != 1) {
			return false;
		} else {
			$row = $stmt->fetch( PDO::FETCH_OBJ );
			return $row ->$col;
		}
	}

    private function getGebruiker($gebruikersnaam, $paswoord)
	{
		$stmt = $this->db->prepare($this->getGebruikerStatement());
		$stmt->bindParam(1, $gebruikersnaam);
		$stmt->bindParam(1, $this->encrypt($paswoord));
		$stmt->execute();
		if($stmt->rowCount() != 1) {
			return false;
		} else {
            $row = $stmt->fetch( PDO::FETCH_OBJ );
            //ID is hier gelijk aan gebruikersnaam.
            //Meer informatie hieromtrent in constructor van KVDutil_Auth_Gebruiker
            return new KVDutil_Auth_Gebruiker(
                            $this,
                            $gebruikersnaam,
                            $gebruikersnaam,
                            $paswoord,
                            $row->voornaam,
                            $row->familienaam,
                            $row->email,
                            $row->telefoon,
                            new KVDutil_Auth_RolCollectie( array())
                        );
		}
	}

    private function getGebruikerStatement()
	{
		return "SELECT * FROM gebruiker WHERE username = ? AND paswoord = ?";
	}

	private function getEncryptPasswordStatement()
	{
		return"SELECT PASSWORD(?)";
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
        $stmt = $this->db->prepare($this->getRollenForGebruikerApplicatieStatement());
		$stmt->bindParam(1, $this->gebruiker->id);
		$stmt->bindParam(2, $applicatie_naam);
		$stmt->execute();
		$rollen = array();
		while($rol = $stmt->fetch( PDO::FETCH_OBJ )) {
            //ID is hier gelijk aan rolnaam.
            //Meer informatie hieromtrent in constructor van KVDutil_Auth_Rol
			$rollen[] = new KVDutil_Auth_Rol(
                                $rol->naam,
                                $rol->naam,
                                $rol->beschrijving
                            );
		}
        //De array met objecten wordt in een KVDdom_DomainObjectCollection geplaatst.
        return new KVDutil_Auth_RolCollectie( $rollen );
    }

    /*
     * Haal de rollen van de huidige gebruiker op voor een opgegeven applicatie
     *
     * @param   KVDutil_Auth_Gebruiker      $gebruiker
     * @param   Object  Een Applicatie-object dat de methode getId() bevat
     *                  Id: 'ou=applicatie_id,ou=productie,ou=groups,dc=vioe,dc=be'
     * @return  KVDutil_AuthRolCollectie    $rollen
     */
    public function getRollenVoorApplicatie( KVDutil_Auth_Gebruiker $gebruiker, $applicatie)
    {
        $stmt = $this->db->prepare($this->getRollenForGebruikerApplicatieIdStatement());
		$stmt->bindParam(1, $this->gebruiker->id);
		$stmt->bindParam(2, $applicatie->getId());
		$stmt->execute();
		$rollen = array();
		while($rol = $stmt->fetch( PDO::FETCH_OBJ )) {
            //ID is hier gelijk aan rolnaam.
            //Meer informatie hieromtrent in constructor van KVDutil_Auth_Rol
			$rollen[] = new KVDutil_Auth_Rol(
                                $rol->naam,
                                $rol->naam,
                                $rol->beschrijving
                            );
		}

        //De array met objecten wordt in een KVDdom_DomainObjectCollection geplaatst.
        return new KVDutil_Auth_RolCollectie( $rollen );
    }

    private function getRollenForGebruikerApplicatieStatement()
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

	private function getRollenForGebruikerApplicatieIdStatement()
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