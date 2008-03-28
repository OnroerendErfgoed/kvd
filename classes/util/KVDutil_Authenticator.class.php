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
	 * logIn
	 *	log in met gegeven gebruiker en paswoord.
	 * @param string gebruiker de gebruikersnaam
	 * @param string paswoord het paswoord.
	 */
	public function logIn($gebruiker, $paswoord)
	{
		return $this->status->logIn($this, $gebruiker, $paswoord);
	}
	
	/**
	 * logOut
	 *	Indien een gebruiker is ingelogd, uitloggen.
	 */
	public function logOut()
	{
		 return $this->status->logOut($this);
	}
	
	/**
	 * getNaam
	 *  geeft de naam van de ingelogde gebruiker.
	 * @return string de naam van de gebruiker.
	 */
	public function getNaam()
	{
		return $this->status->getNaam($this);
	}
	
	/**
	 * getEmail
	 *  geeft het email adres van de gebruiker.
	 * @return string het email adres.
	 */
	public function getEmail()
	{
		return $this->status->getEmail($this);
	}

	/**
	 * isAuthenticated
	 *  geeft weer of een gebruiker is ingelogd.
	 * @return boolean
	 */
	public function isAuthenticated()
	{	
		return $this->status->isAuthenticated($this);		
	}
	
	/**
	 * getRollenVoorApplicatie
	 *  geeft een array van rollen voor deze gebruiker weer.
	 * @return array met 1 string per rol.
	 */
	public function getRollenVoorApplicatie($applicatie)
	{
		return $this->status->getRollenVoorApplicatie($this, $applicatie);
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
 *  Abstracte klasse die een interface biedt voor statusobjecten van
 *  de Authenticator.
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 1.0.0
 */
abstract class LoggedStatus{

	protected $db;
	
	public function __contruct($db)
	{
		$this->db = $db;
	}

	abstract function logIn(KVDutil_Authenticator $auth, $gebruiker, $paswoord);
	abstract function logOut(KVDutil_Authenticator $auth);
	abstract function getNaam(KVDutil_Authenticator $auth);
	abstract function getEmail(KVDutil_Authenticator $auth);
	abstract function isAuthenticated(KVDutil_Authenticator $auth);
	abstract function getRollenVoorApplicatie(KVDutil_Authenticator $auth, $applicatie);
}


/**
 * LoggedOut
 *  State-klasse voor een Authenticator wanneer er geen gebruiker is ingelogd.
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 1.0.0
 */
class LoggedOut extends LoggedStatus{
	
	
	private $db;
	
	private final $userquery = "SELECT * FROM gebruiker WHERE username = ? AND paswoord = ?";
	private final $encryptquery = "SELECT PASSWORD(?)";
	
	
	public function __construct($database)
	{
		parent::__construct($database);
	}
	

	/**
	 * @todo
	 */
	private function encrypt($woord)
	{
		$col = "PASSWORD('".addslashes($woord)."')";
		$stmt = $db->prepare($this->encryptquery);
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
		$stmt = $db->prepare($this->userquery);
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
	public function logIn(KVDutil_Authenticator $auth, $gebruiker, $paswoord)
	{	
		$pass = $this->encrypt($paswoord);
		$gebruiker = $this->getGebruiker($gebruiker, $pass);
		if(!$gebruiker) {
			return false;
		} else {
			$auth->changeState(new LoggedIn($this->db, $gebruiker));
			return true;
		}
	}
	public function logOut(KVDutil_Authenticator $auth)
	{
		return true;
	}
	
	public function getNaam(KVDutil_Authenticator $auth)
	{	
		return null;
	}
	
	public function getEmail(KVDutil_Authenticator $auth)
	{	
		return null;
	}
	
	public function isAuthenticated(KVDutil_Authenticator $auth)
	{	
		return false;
	}
	
	public function getRollenVoorApplicatie(KVDutil_Authenticator $auth, $applicatie)
	{
		return array();
	}
	
}

/**
 * LoggedIn
 *  State-klasse voor een Authenticator wanneer er een gebruiker is ingelogd.
 * @package KVD.util
 * @subpackage authenticate
 * @author Dieter Standaert <dieter.standaert@eds.com>
 * @since 1.0.0
 */
class LoggedIn extends LoggedStatus{
	
	private $gebruiker;
	
	public function __construct($database, $gebruiker)
	{
			parent::__construct($database);
		$this->gebruiker = $gebruiker;
	}

	public function logIn(KVDutil_Authenticator $auth, $gebruiker, $paswoord)
	{
		$this->logOut($auth);
		return $auth->logIn($gebruiker, $paswoord);
	}

	public function logOut(KVDutil_Authenticator $auth)
	{	
		$auth->changeState(new LoggedOut($this->db));
	}

	public function getNaam(KVDutil_Authenticator $auth)
	{	
		return $this->gebruiker->naam;
	}

	public function getEmail(KVDutil_Authenticator $auth)
	{
		return $this->gebruiker->email;
	}

	public function isAuthenticated(KVDutil_Authenticator $auth)
	{	
		return true;
	}

	public function getRollenVoorApplicatie(KVDutil_Authenticator $auth, $applicatie)
	{
		return $this->gebruiker->getRollenVoorApplicatie();
	}

}



?>