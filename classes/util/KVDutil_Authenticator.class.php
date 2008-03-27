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
		$status->logIn($this, $gebruiker, $paswoord);
	}
	/**
	 * logOut
	 *	Indien een gebruiker is ingelogd, uitloggen.
	 */
	public function logOut()
	{
		$status->logOut($this);
	}
	/**
	 * getNaam
	 *  geeft de naam van de ingelogde gebruiker.
	 * @return string de naam van de gebruiker.
	 */
	public function getNaam()
	{
		return $status->getNaam($this);
	}
	/**
	 * getEmail
	 *  geeft het email adres van de gebruiker.
	 * @return string het email adres.
	 */
	public function getEmail()
	{
		return $status->getEmail($this);
	}

	/**
	 * isAuthenticated
	 *  geeft weer of een gebruiker is ingelogd.
	 * @return boolean
	 */
	public function isAuthenticated()
	{	
		return $status->isAuthenticated($this);		
	}
	/**
	 * getRollenVoorApplicatie
	 *  geeft een array van rollen voor deze gebruiker weer.
	 * @return array met 1 string per rol.
	 */
	public function getRollenVoorApplicatie($applicatie)
	{
		return $status->getRollenVoorApplicatie($this, $applicatie);
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
	
	
	private function checkPaswoord($paswoord, $check)
	{
		return $paswoord = $this->encrypt($check);
	}
	
	
	/**
	 * @todo
	 */
	private function encrypt($woord)
	{
		return $word;
	}
	

	/**
	 * @todo
	 */
	public function logIn(KVDutil_Authenticator $auth, $gebruiker, $paswoord)
	{	
		$gebruiker = null;
		$auth->changeState(new LoggedIn($gebruiker));
	}
	public function logOut(KVDutil_Authenticator $auth)
	{	
		// ok
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
	
	public function __construct($gebruiker)
	{
		$this->gebruiker = $gebruiker;
	}


	public function logIn(KVDutil_Authenticator $auth, $gebruiker, $paswoord)
	{
		$this->logOut($auth);
		$auth->logIn($gebruiker, $paswoord);
	}
	public function logOut(KVDutil_Authenticator $auth)
	{	
		$auth->changeState(new LoggedOut());
	}
	
	public function getNaam(KVDutil_Authenticator $auth)
	{	
		return $gebruiker->getNaam();
	}
	
	public function getEmail(KVDutil_Authenticator $auth)
	{	
		return $gebruiker->getEmail();
	}
	
	public function isAuthenticated(KVDutil_Authenticator $auth)
	{	
		return true;
	}
	
	public function getRollenVoorApplicatie(KVDutil_Authenticator $auth, $applicatie)
	{
		return $gebruiker->getRollenVoorApplicatie();
	}
	
}



?>