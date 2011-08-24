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
 * Deze class zorgt voor afhandeling van de taken van de authenticator in LoggedOut Status
 * In dit geval voor de database PostgreSQL
 *
 * @package    KVD.util
 * @subpackage auth
 * @since      12 aug 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_Auth_PostgreSQLLoggedOut extends KVDutil_Auth_LoggedOut
{
    /*
     * Deze functie zal controle uitvoeren of de combinatie
     * gebruikersnaam-paswoord in de databank te vinden is.
     * Indien authenticatie gelukt is, wordt er op basis van de parameters een Gebruiker aangemaakt
     *
     * @param   string  $gebruikersnaam
     * @param   string  $paswoord
     * @return  KVDutil_Auth_Gebruiker  gebruikerobject als gebruiker werd aangemeld
     */
    private function doLogin($gebruikersnaam, $paswoord){
        $pass = $this->encrypt($paswoord);
		$row = $this->getGebruikerRow($gebruikersnaam, $pass);
        $gebruiker = new KVDutil_Auth_Gebruiker(
                            $gebruikersnaam,
                            $paswoord,
                            $row->voornaam?$row->voornaam:'',
                            $row->familienaam?$row->familienaam:'',
                            $row->email?$row->email:'',
                            $row->telephone?$row->telephone:'',
                            new KVDutil_Auth_RolCollectie( array())
                        );
		if(!$gebruiker) {
			return false;
		}
        return $gebruiker;
    }

    protected function getGebruikerStatement()
	{
		return 'SELECT * FROM gebruiker WHERE username = ? AND paswoord = ?';
	}

	protected function getEncryptPasswordStatement()
	{
		return 'SELECT PASSWORD(?)';
	}

    private function encrypt($woord)
	{
		$col = "PASSWORD('".addslashes($woord)."')";
		$stmt = $this->authenticator->getDatabaseConnection( )->prepare($this->getEncryptPasswordStatement());
		$stmt->bindParam(1, $woord);
		$stmt->execute();
		if($stmt->rowCount() != 1) {
			return false;
		} else {
			$row = $stmt->fetch( PDO::FETCH_OBJ );
			return $row ->$col;
		}
	}

	private function getGebruikerRow($gebruiker, $paswoord)
	{
		$stmt = $this->authenticator->getDatabaseConnection( )->prepare($this->getGebruikerStatement());
		$stmt->bindParam(1, $gebruiker);
		$stmt->bindParam(1, $this->encrypt($paswoord));
		$stmt->execute();
		if($stmt->rowCount() != 1) {
			return false;
		} else {
			$row = $stmt->fetch( PDO::FETCH_OBJ );
			return $row;
		}
	}
}
?>