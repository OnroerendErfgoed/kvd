<?php

/**
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @version $Id$
 */
 
/** 
 * 
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 1.0.0
 */
class KVDutil_Authenticator
{

	/**
	 * @var status LoggedStatus 'state' van dit object.
	 */
	private $status;
	
	/**
	 * __construct
	 * @param databank een databank verbinding waar queries naar gestuurd kunnen worden.
	 */
	public function __construct($databank)
	{
		$this->status = new KVDutil_Auth_LoggedOut($this, $databank);
	}

	/**
	 * logIn
	 *	log in met gegeven gebruiker en paswoord.
	 * @param string gebruiker de gebruikersnaam
	 * @param string paswoord het paswoord.
	 */
	public function logIn($gebruiker, $paswoord)
	{
		return $this->status->logIn($gebruiker, $paswoord);
	}
	
	/**
	 * logOut
	 *	Indien een gebruiker is ingelogd, uitloggen.
	 */
	public function logOut()
	{
		 return $this->status->logOut();
	}
	
	/**
	 * getNaam
	 *  geeft de naam van de ingelogde gebruiker.
	 * @return string de naam van de gebruiker.
	 */
	public function getNaam()
	{
		return $this->status->getNaam();
	}
	
	/**
	 * getEmail
	 *  geeft het email adres van de gebruiker.
	 * @return string het email adres.
	 */
	public function getEmail()
	{
		return $this->status->getEmail();
	}

	/**
	 * isAuthenticated
	 *  geeft weer of een gebruiker is ingelogd.
	 * @return boolean
	 */
	public function isAuthenticated()
	{	
		return $this->status->isAuthenticated();		
	}
	
	/**
	 * getRollenVoorApplicatieId
	 *  geeft een array van rollen voor deze gebruiker weer.
	 * @return array met 1 string per rol.
	 */
	public function getRollenVoorApplicatieId($applicatie)
	{
		return $this->status->getRollenVoorApplicatie($applicatie);
	}
	
	/**
	 * getRollenVoorApplicatieNaam
	 *  geeft een array van rollen voor deze gebruiker weer.
	 * @return array met 1 string per rol.
	 */
	public function getRollenVoorApplicatieNaam($applicatie)
	{
		return $this->status->getRollenVoorApplicatieNaam($applicatie);
	}	
	/**
	 * changeState
	 *  veranderd de status van de authenticator
	 * @param LoggedStatus nieuwe status
	 */
	public function changeState(LoggedStatus $status)
	{
		$this->status = $status;
	}
    
}


/**
 * LoggedStatus
 *  De abstracte superklasse die een authenticatie status implementeert.
 *  Deze status kan een van volgende subklasses (met hun status) zijn:
 *   - LoggedOut: indien geen gebruiker is ingelogd
 *   - LoggedIn: indien een gebruiker is ingelogd
 *  
 *  Deze abstracte klasse biedt een interface voor deze status
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 1.0.0
 */
abstract class KVDutil_Auth_LoggedStatus{

	protected $db;
	protected $auth;
	
	abstract function logIn($gebruiker, $paswoord);
	abstract function logOut();
	abstract function getNaam();
	abstract function getEmail();
	abstract function isAuthenticated();
	abstract function getRollenVoorApplicatieNaam($applicatie_naam);
	abstract function getRollenVoorApplicatieId($applicatie);
}


/**
 * KVDutil_Auth_LoggedOut
 *  State-klasse wanneer er geen gebruiker is ingelogd.
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 1.0.0
 */
class KVDutil_Auth_LoggedOut extends KVDutil_Auth_LoggedStatus{

	
	public function __construct(KVDutil_Authenticator $auth, $db)
	{
		$this->auth = $auth;
		$this->db = $db;
	}
	

	protected function getGebruikerStatement()
	{
		return "SELECT * FROM gebruiker WHERE username = ? AND paswoord = ?";
	}
	
	protected function getEncryptPasswordStatement()
	{
		return"SELECT PASSWORD(?)";
	}
	
	/**
	 * @todo
	 */
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
		
	
	private function getGebruiker($gebruiker, $paswoord)
	{
		$stmt = $this->db->prepare($this->getGebruikerStatement());
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
	/**
	 * @todo
	 */
	public function logIn($gebruiker, $paswoord)
	{	
		$pass = $this->encrypt($paswoord);
		$gebruiker = $this->getGebruiker($gebruiker, $pass);
		if(!$gebruiker) {
			return false;
		} else {
			$auth->changeState(new KVDutil_Auth_LoggedIn($this->db, $this->auth, $gebruiker));
			return true;
		}
	}
	public function logOut()
	{
		return true;
	}
	
	public function getNaam()
	{	
		return null;
	}
	
	public function getEmail()
	{	
		return null;
	}
	
	public function isAuthenticated()
	{	
		return false;
	}
	
	public function getRollenVoorApplicatieId($applicatie)
	{
		return array();
	}
	public function getRollenVoorApplicatieNaam($applicatie_naam)
	{
		return array();
	}	
}

/**
 * KVDutil_Auth_LoggedIn
 *  State-klasse voor een Authenticator wanneer er een gebruiker is ingelogd.
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 1.0.0
 */
class KVDutil_Auth_LoggedIn extends KVDutil_Auth_LoggedStatus{
	
	private $gebruiker;
	
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

	
	public function __construct(KVDutil_Authenticator $auth, $database, $gebruiker)
	{

		$this->auth = $auth;
		$this->db = $database;
		$this->gebruiker = $gebruiker;
	}

	public function logIn($gebruiker, $paswoord)
	{
		$this->logOut($auth);
		return $this->auth->logIn($gebruiker, $paswoord);
	}

	public function logOut()
	{	
		$this->auth->changeState(new KVDutil_Auth_LoggedOut( $this->auth, $this->db));
	}

	public function getNaam()
	{	
		return $this->gebruiker->naam;
	}

	public function getEmail()
	{
		return $this->gebruiker->email;
	}

	public function isAuthenticated()
	{	
		return true;
	}
	
	public function getRollenVoorApplicatieId($applicatie)
	{
		$stmt = $this->db->prepare($this->getRollenForGebruikerApplicatieIdStatement());
		$stmt->bindParam(1, $this->gebruiker->id);
		$stmt->bindParam(2, $applicatie->getId());
		$stmt->execute();
		$rollen = array();
		while($rol = $stmt->fetch( PDO::FETCH_OBJ )) {
			$rollen[] = $rol->naam;
		}
		return $rollen;
	}

	public function getRollenVoorApplicatieNaam($applicatie_naam)
	{
		$stmt = $this->db->prepare($this->getRollenForGebruikerApplicatieStatement());
		$stmt->bindParam(1, $this->gebruiker->id);
		$stmt->bindParam(2, $applicatie_naam);
		$stmt->execute();
		$rollen = array();
		while($rol = $stmt->fetch( PDO::FETCH_OBJ )) {
			$rollen[] = $rol->naam;
		}
		return $rollen;
	}

}



?>