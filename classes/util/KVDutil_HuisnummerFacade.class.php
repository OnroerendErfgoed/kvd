<?php
/**
 * @package KVD.util
 * @subpackage Huisnummer
 * @version $Id: KVDutil_HuisnummerFacade.class.php 1 2007-10-05 13:16:16Z standadi $
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_HuisnummerFacade 
 *	Abstracte superklasse voor alle huisnummer notaties.
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

abstract class KVDUtil_HnrHuisnummerElement{
	/**
	 * compareTo
	 * Functie om een huisnummer te vergelijken met een ander nummer.
	 * @param KVDUtil_HnrHuisnummerElement een huisnummer
	 * @return integer 
	 *    -1 als dit nummer kleiner is, 
	 *     0 indien de nummers gelijk zijn,
	 *     1 indien dit nummer groteris
	 */
	public function compareTo($nummer){
		return KVDUtil_HnrCompare::compare($this, $nummer);
	}
}

/**
 * KVDUtil_HnrEnkelElement 
 *	Abstracte klasse voor huisnummers
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

abstract class KVDUtil_HnrEnkelElement extends KVDUtil_HnrHuisnummerElement{
	
	/**
	 * @var integer huisnummer
	 */
	protected $nummer;
	
	/**
	 * __construct
	 * @param integer het huisnummer
	 */
	public function __construct($nummer){
		$this->nummer= $nummer;
	}
	
	/**
	 * getHuisnummer
	 * 
	 * @return integer het huisnummer van dit element
	 */
	public function getHuisnummer(){
		return $this->nummer;
	}
	/**
	 * setNummer
	 * @param integer het nieuwe huisnummer van dit element
	 */
	public function setNummer($nummer){
		$this->nummer = $nummer;
	}
	/**
	 * split
	 * Deelt een huisnummerreeks (indien van toepassing) op in de individuele huisnummers.
	 * @return array - een array met dit element. 
	 */
	public function split(){
		return array($this);
	}
	
}

/**
 * KVDUtil_HnrHuisnummer 
 *	Een eenvoudig huisnummer, bijv: 13 of 15.
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */


class KVDUtil_HnrHuisnummer extends KVDUtil_HnrEnkelElement{
	/**
	 * __construct
	 * @param integer het huisnummer
	 */
	public function __construct($nummer){
		parent::__construct($nummer);
	}

	/**
	 * __toString
	 * @return string representatie van het huisnummer, bijv "3" of "21"
	 */
	public function __toString(){
		return "$this->nummer";
	}
	
}

/**
 * KVDUtil_HnrBiselement
 *	Klasse dat een enkel huisnummer met bisnummer voorstelt, bijv "3/1" of "21/5"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDUtil_HnrBiselement extends KVDUtil_HnrEnkelElement{
	/**
	 * @param integer het bisbnummer
	 */
	protected $bis;
	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer bisnummer
	 */
	public function __construct($huis, $bis){
		parent::__construct($huis);
		$this->bis = $bis;
	}
	/**
	 * __toString
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer()."/".$this->bis;
	}
	/**
	 * getBisnummer
	 * @return integer
	 */
	public function getBiselement(){
		return $this->bis;
	}

}

/**
 * KVDUtil_HnrBisnummer 
 *	Klasse dat een enkel huisnummer met bisnummer voorstelt, bijv "3/1" of "21/5"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDUtil_HnrBisnummer extends KVDUtil_HnrBiselement{

	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer bisnummer
	 */
	public function __construct($huis, $bis){
		parent::__construct($huis,$bis);
	}
	/**
	 * __toString
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer()."/".$this->bis;
	}
	/**
	 * getBisnummer
	 * @return integer
	 */
	public function getBisnummer(){
		return $this->bis;
	}

}

 
/**
 * KVDUtil_HnrBisnummer 
 *  Klasse dat een enkel huisnummer met busnummer voorstelt, bijv "3 bus 1" of "53 bus 5"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusnummer extends KVDUtil_HnrBiselement{
	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer busnummer
	 */
	public function __construct($huis, $bus){
		parent::__construct($huis,$bus);
	}
	/**
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->bis;
	}
	/**
	 * getBusnummer
	 * @return integer
	 */
	public function getBusnummer(){
		return $this->bis;
	}

}

/**
 * KVDUtil_HnrBusletter 
 *	Klasse dat een enkel huisnummer met busletter voorstelt, bijv "3 bus A" of "53 bus D"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusletter extends KVDUtil_HnrBiselement{
	/**
	 * __construct
	 * @param integer huisnummer
	 * @param string busletter
	 */
	public function __construct($huis, $bus){
		parent::__construct($huis,$bus);
	}
	/**
	 * __toString
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->bis;
	}
	/**
	 * getBusletter
	 * @return string
	 */
	public function getBusletter(){
		return $this->bis;
	}

}

/**
 * KVDUtil_HnrBisletter 
 *	Klasse dat een enkel huisnummer met bisletter voorstelt, bijv "3A" of "53D"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBisletter extends KVDUtil_HnrBiselement{

	/**
	 * __construct
	 * @param integer huisnummer
	 * @param string bisletter
	 */
	public function __construct($huis, $bis){
		parent::__construct($huis,$bis);
	}
	/**
	 * __toString
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer().$this->bis;
	}
	/**
	 * getBisletter
	 * @return string
	 */
	public function getBisletter(){
		return $this->bis;
	}

}


/**
 * KVDUtil_HnrReeksElement 
 *	Abstracte klasse voor alle reeksen (compacte notatie van een reeks) huisnummers
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */


abstract class KVDUtil_HnrReeksElement extends KVDUtil_HnrHuisnummerElement{

	/**
	 * @var integer
	 */
	protected $begin;
	
	/**
	 * @var integer
	 */
	protected $einde;
	
	
	/**
	 * __construct
	 * @param integer
	 * @param integer
	 */
	public function __construct($begin, $einde){
		$this->begin = $begin;
		$this->einde = $einde;
	}
	
	/**
	 * getBegin
	 * @return integer het begin nummer van deze reeks
	 */
	public function getBegin(){
		return $this->begin;
	}
	/**
	 * setBegin
	 * @param integer het begin nummer van deze reeks
	 */
	public function setBegin($begin){
		$this->begin = $begin;
	}
	/**
	 * getEinde
	 * @return integer het laatste nummer van deze reeks
	 */	
	public function getEinde(){
		return $this->einde;
	}
	/**
	 * setEinde
	 * @param integer het laatste nummer van deze reeks
	 */	
	public function setEinde($einde){
		return $this->einde = $einde;
	}
	
}

/**
 * KVDUtil_HnrHuisnummerReeks 
 *  Een reeks van huisnummers.
 *  bijv "33, 35, 37" -> "33-37"
 *  bijv "33, 34, 35, 36" -> "33-36"
 *  bijv "32, 33, 34, 35, 36"-> "32, 33-36"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDUtil_HnrHuisnummerReeks extends KVDUtil_HnrReeksElement{
	/**
	 * @var boolean geeft weer of de huisnummers in deze reeks elke opvolgen
	 *  bijvoorbeeld:  35-38
	 * 	of telkens 1 nummer overslaan, zoals bij 35-37.
	 */
	private $spring;

	/**
	 * __construct
	 * @param int begin eerste huisnummer van de reeks
	 * @param int einde laatste huisnummer van de reeks
	 * @param boolean spring geeft weer of de reeks telkens een huisnummer overslaat.
	 */
	public function __construct($begin, $einde, $spring = true){
		parent::__construct($begin,$einde);
		$this->spring = $spring;
		
	}
	/**
	 * __toString
	 * Geeft een string representatie van de reeks weer.
	 * bijv "33, 35, 37" -> "33-37"
	 * bijv "33, 34, 35, 36" -> "33-36"
	 * bijv "32, 33, 34, 35, 36"-> "32, 33-36"
	 * @return string representatie van deze reeks
	 */
	public function __toString(){
		$diff = ($this->einde - $this->begin);
		if (($diff%2 == 0) && (!$this->spring))
			return $this->begin.", ".($this->begin+1)."-".$this->einde;
		return $this->begin.'-'.$this->einde;
	}
	
	/**
	 * isVolgReeks
	 * Geeft weer of de nummers in deze reeks elkaar opvolgend of telkens een
	 *  nummer overslaan
	 *  bijv "33, 35, 37" -> false
	 *  bijv "33, 34, 35, 36" -> true
	 *  bijv "32, 33, 34, 35, 36"-> true
	 * @return boolean of de nummers elkaar opvolgen
	 */

	public function isVolgReeks(){
		return !($this->spring);
	}
	/**
	 * isSpringReeks
	 * Geeft weer of de nummers in deze reeks elkaar opvolgend of telkens een
	 *  nummer overslaan
	 *  bijv "33, 35, 37" -> true
	 *  bijv "33, 34, 35, 36" -> false
	 *  bijv "32, 33, 34, 35, 36"-> false
	 * @return boolean of de nummers in de rij telkens 1 nummer overslaan
	 */	
	public function isSpringReeks(){
		return ($this->spring);
	}
	
	/**
	 * setSprong
	 * @param boolean of de rij telkens een nummer overslaat (true) of niet (false).
	 */
	public function setSprong($val){
		$this->spring = $val;
	}

	/**
	 * @return array een array met de individuele huisnummers van deze reeks
	 */
	public function split(){
		$r = array();
		$jump = ($this->isSpringReeks()) ? 2 : 1;
		for($i = $this->begin; $i<= $this->einde; $i += $jump){
			$r[] = new KVDUtil_HnrHuisnummer($i);
		}
		return $r;
	}
}


/**
 * KVDUtil_HnrBisReeks 
 *  Een reeks van bisnummers.
 *  bijv "33/1, 32/2, 33/3" -> "33/1-3"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDUtil_HnrBisReeks extends KVDUtil_HnrReeksElement{
	/**
	 * @var integer
	 */
	protected $huis;
	
	/**
	 * @param integer huis het huisnummer
	 * @param integer begin het eerste bisnummer van de reeks
	 * @param integer einde het laatste bisnummer van de reeks
	 */
	public function __construct($huis, $begin, $einde){
		parent::__construct($begin,$einde);
		$this->huis = $huis;
	}
	/**
	 * __toString
	 * @return string de string representatie van de reeks
	 */ 
	public function __toString(){
		return $this->huis."/".$this->begin."-".$this->einde;
	}	
	/**
	 * getHuisnummer
	 * @return integer
	 */
	public function getHuisnummer(){
		return $this->huis;
	}
	/**
	 * setHuisnummer
	 * @param integer
	 */
	public function setHuisnummer($huis){
		$this->huis = $huis;
	}

}



/**
 * KVDUtil_HnrBisnummerReeks 
 *  Een reeks van bisnummers.
 *  bijv "33/1, 32/2, 33/3" -> "33/1-3"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBisnummerReeks extends KVDUtil_HnrBisReeks{

	/**
	 * @param integer huis het huisnummer
	 * @param integer begin het eerste bisnummer van de reeks
	 * @param integer einde het laatste bisnummer van de reeks
	 */
	public function __construct($huis, $begin, $einde){
		parent::__construct($huis, $begin,$einde);
	}
	/**
	 * __toString
	 * @return string de string representatie van de reeks
	 */ 
	public function __toString(){
		return $this->huis."/".$this->begin."-".$this->einde;
	}	


	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */
	public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBisnummer($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

/**
 * KVDUtil_HnrBusnummerReeks 
 *  Een reeks van busnummers.
 *  bijv "33 bus 1, 32 bus 2, 33 bus 3" -> "33 bus 1-3"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusnummerReeks extends KVDUtil_HnrBisReeks{

	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer het eerste nummer van de reeks
	 * @param integer het laatste nummer van de reeks
	 */	
	public function __construct($huis, $begin, $einde){
		parent::__construct($huis, $begin,$einde);
	}
	
	/**
	 * __toString
	 * @return Een string representatie van de busnummer reeks, bijv: 13 bus 1-3.
	 */
	public function __toString(){
		return $this->huis." bus ".$this->begin."-".$this->einde;
	}	



	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */	
	public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBusnummer($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

/**
 * KVDUtil_HnrBusletterReeks 
 *  Een reeks van busletters.
 *  bijv "33 bus A, 32 bus B, 33 bus C" -> "33 bus A-C"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusletterReeks extends KVDUtil_HnrBisReeks{
	
	public function __construct($huis, $begin, $einde){
		parent::__construct($huis, $begin,$einde);
	}
	
	public function __toString(){
		return $this->huis." bus ".$this->begin."-".$this->einde;
	}	

	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */	
	public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBusletter($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

/**
 * KVDUtil_HnrBisletterReeks 
 *  Een reeks van bisletters.
 *  bijv "33A, 32B, 33C" -> "33A-C"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBisletterReeks extends KVDUtil_HnrBisReeks{


	public function __construct($huis, $begin, $einde){
		parent::__construct($huis, $begin,$einde);
	}

	public function __toString(){
		return $this->huis.$this->begin."-".$this->einde;
	}	
	
	
	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */	
	public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBisletter($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

/********************************************************************************/


/**
 * KVDUtil_HnrCompare 
 *  Abstracte klasse voor een Visitor om twee huisnummers te vergelijken.
 *  Dit is nodig voor een search.
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrCompare{
	public static function compareOne($a,$b){
		if($a == $b) {return 0;}
		else if($a > $b) {return 1;}
		else {return -1;}
	}
	public static function compareTwo($a1, $a2, $b1, $b2){
		$c = self::compareOne($a1,$b1) ;
		if($c == 0) return self::compareOne($a2,$b2);
		else return $c;
	}
	public static function compareThree($a1, $a2, $a3, $b1, $b2, $b3){
		$c = self::compareTwo($a1,$a2,$b1, $b2) ;
		if($c == 0) return self::compareOne($a3,$b3);
		else return $c;
	}
	
	public static function compareHuisToHuis($nr1, $nr2) {
		return self::compareOne($nr1->getHuisnummer(), $nr2->getHuisnummer());
	}
	
	public static function compareHuisnummerToHuisnummerReeks($nr, $reeks){
		return self::compareTwo(
			$nr->getHuisnummer(), $reeks->getHuisnummer(), 
			$nr->getBegin(), $reeks->getEinde());
	}


	public static function compareBisToBis($nr1, $nr2){
		return self::compareTwo($nr1->getHuisnummer(), $nr1->getBiselement(),$nr2->getHuisnummer(), $nr2->getBiselement());
	}
	public static function compareEnkelToEnkel($nr1, $nr2, $alternative){
		$val  = self::compareOne($nr1->getHuisnummer(), $nr2->getHuisnummer());
		if($val == 0) {
			return $alternative;
		} else {
			return $val;
		}
	}
	
	public static function compareEnkelToEnkelFirst($nr1,$nr2){
		return self::compareEnkelToEnkel($nr1,$nr2, -1);
	}

	public static function compareHuisnummerReeksToHuisnummerReeks($r1, $r2){
		return self::compareTwo($r1->getBegin(), $r1->getEinde(), $r2->getBegin(), $r2->getEinde());
	}

	public static function compareReeksen($r1, $r2){
		return self::compareThree(
				$r1->getHuisnummer(),
				$r1->getBegin(),
				$r1->getEinde(),
				$r2->getHuisnummer(),
				$r2->getBegin(),
				$r2->getEinde());
	}
	

	public static function compareBisElementToBisReeks($nr, $reeks){
		return self::compareThree(
			$nr->getHuisnummer(),
			$nr->getBiselement(),
			$nr->getBiselement(),
			$reeks->getHuisnummer(),
			$reeks->getBegin(),
			$reeks->getEinde());
	}
	
	public static $compareMap = array(
		'KVDUtil_HnrHuisnummer'=>array(
				'KVDUtil_HnrHuisnummer' => "compareHuisToHuis",
				'KVDUtil_HnrBisnummer' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusnummer' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBisletter' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletter' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBisnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBisletterReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletterReeks' => "compareEnkelToEnkelFirst"),
		'KVDUtil_HnrBisnummer' =>array(
				'KVDUtil_HnrBisnummer' => "compareBisToBis",
				'KVDUtil_HnrBusnummer' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBisletter' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletter' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBisnummerReeks' => "compareBisElementToBisReeks",
				'KVDUtil_HnrBusnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBisletterReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletterReeks' => "compareEnkelToEnkelFirst"),
		'KVDUtil_HnrBusnummer' =>array(
				'KVDUtil_HnrBusnummer' => "compareBisToBis",
				'KVDUtil_HnrBisletter' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletter' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBisnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusnummerReeks' => "compareBisElementToBisReeks",
				'KVDUtil_HnrBisletterReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletterReeks' => "compareEnkelToEnkelFirst"),				
		'KVDUtil_HnrBisletter' =>array(
				'KVDUtil_HnrBisletter' => "compareBisToBis",
				'KVDUtil_HnrBusletter' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBisnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBisletterReeks' => "compareBisElementToBisReeks",
				'KVDUtil_HnrBusletterReeks' => "compareEnkelToEnkelFirst"),
		'KVDUtil_HnrBusletter' =>array(
				'KVDUtil_HnrBusletter' => "compareBisToBis",
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBisnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBisletterReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletterReeks' => "compareBisElementToBisReeks"),
		'KVDUtil_HnrHuisnummerReeks' =>array(
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerReeksToHuisnummerReeks"),
		'KVDUtil_HnrBisnummerReeks' =>array(
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBisnummerReeks' => "compareReeksen",
				'KVDUtil_HnrBusnummerReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBisletterReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletterReeks' => "compareEnkelToEnkelFirst"),
		'KVDUtil_HnrBusnummerReeks' =>array(
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBusnummerReeks' => "compareReeksen",
				'KVDUtil_HnrBisletterReeks' => "compareEnkelToEnkelFirst",
				'KVDUtil_HnrBusletterReeks' => "compareEnkelToEnkelFirst"),
		'KVDUtil_HnrBisletterReeks' =>array(
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBisletterReeks' => "compareReeksen",
				'KVDUtil_HnrBusletterReeks' => "compareEnkelToEnkelFirst"),
		'KVDUtil_HnrBusletterReeks' =>array(
				'KVDUtil_HnrHuisnummerReeks' => "compareHuisnummerToHuisnummerReeks",
				'KVDUtil_HnrBusletterReeks' => "compareReeksen"));


	public static function compare($element1, $element2){
		$class1 = get_class($element1);
		$class2 = get_class($element2);
		if ( isset ( self::$compareMap[$class1][$class2] ) ) {
				$function = self::$compareMap[$class1][$class2];
				return self::$function( $element1, $element2); 
		} else if ( isset ( self::$compareMap[$class2][$class1] ) ) {
				$function = self::$compareMap[$class2][$class1];
				return - self::$function( $element2, $element1); 
		} else {
			throw new Exception ( 'Combinatie niet gekend!');
		}
	}
}


/**
 * KVDutil_HuisnummerReader 
 *  Klasse die uit een string, de informatie kan halen die relevant is voor huisnummers.
 *  Bijvoorbeeld: '23 bus 5 blabla' zal resulteren in 
 *		"23"->nummer, 
 *		"bus"->bus, 
 *		"5"->nummer, 
 *		"blabla"->woord
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_HuisnummerReader{
	const nummer = 0;
	const min = 1;
	const slash = 2;
	const bus = 3;
	const eof = 4;
	const word = 5;
	const comma = 6;
	
	public function typeText($type){
		switch($type) {
			case 0: return "Nummer";
			case 1: return "Reeks"; 
			case 2: return "Bisnummer";
			case 3: return "Busnummer";
			case 4: return "Einde";
			case 5: return "Woord";
			case 6: return "Comma";
			default: break;
		}
		return "";
	}

	public function __construct(){
		
	}

	private $input = array();
	private $pos = -1;
	private $type = null;
	private $size = 0;
	
	public function setInput($input){
		$result = array();
		preg_match_all("/(\d+)|(\/)|(,)|(-)|((?i:bus))|(\w+)/", $input, $result);
		$this->input = $result[0];
		$this->size = count($this->input);
		$this->pos = -1;
	}


	private function readBus(){
		if(strcasecmp("bus",$this->getContent()) == 0)
			$this->type = KVDutil_HuisnummerReader::bus;
		else $this->type = KVDutil_HuisnummerReader::word;
		return $this->type;
	}
	
	public function next(){
		$this->pos++;
		if($this->pos >= $this->size) $this->type = KVDutil_HuisnummerReader::eof;
		else if(preg_match('/[\D]+/', $this->getContent()) == 0) $this->type = KVDutil_HuisnummerReader::nummer;
		else if(preg_match('/[\W]+/', $this->getContent()) == 0) $this->readBus();
		else if($this->getContent() == "/") $this->type = KVDutil_HuisnummerReader::slash;
		else if($this->getContent() == ",") $this->type = KVDutil_HuisnummerReader::comma;
		else if($this->getContent() == "-") $this->type = KVDutil_HuisnummerReader::min;
		else $this->error("Syntax error: ".$this->getContent());
		return $this->type;
	}
	public function getType(){
		return $this->type;
	}
	public function getContent(){
		return $this->input[$this->pos];
	}
	
	public function printout(){
		print_r($this->input);
	}
	
	private function error($msg){
		throw new Exception($msg);
	}
	
	public function getPos(){
		return $this->pos;
	}

}

/**
 * KVDutil_HuisnummerParser 
 *  Klasse die uit een reeks van huisnummer data, de huisnummer- 
 *	of huisnummerreeksobjecten bouwt. Bijvoorbeeld:
 *		"23", "bus", "5" -> Busnummer
 *		"blabla" -> error
 *		"25-27" -> Huisnummerreeks
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_HuisnummerParser{
	private $reader;
	
	public function __construct(){
		$this->reader = new KVDutil_HuisnummerReader();
	}

	private function readElement($type){
		if($this->reader->next() == $type) return $this->reader->getContent();
		else return $this->errorCode($this->reader->getType(),$type);
	}

	private function readHuisnummerReeks($begin){
		$einde = $this->readElement(KVDutil_HuisnummerReader::nummer);
		$this->reader->next();
		$spring = ((($begin - $einde)/2) == floor(($begin - $einde)/2));
		return new KVDUtil_HnrHuisnummerReeks($begin, $einde, $spring );	
	}
	
	private function readBisnummerReeks($huis, $begin){
		$einde = $this->readElement(KVDutil_HuisnummerReader::nummer);
		$this->reader->next();
		return new KVDUtil_HnrBisnummerReeks($huis, $begin, $einde);
	}
	
	private function readBisletterReeks($huis, $begin){
		$einde = $this->readElement(KVDutil_HuisnummerReader::word);
		$this->reader->next();
		return new KVDUtil_HnrBisletterReeks($huis, $begin, $einde);
	
	}
	
	private function readBusnummerReeks($huis, $begin){
		$einde = $this->readElement(KVDutil_HuisnummerReader::nummer);
		$this->reader->next();
		return new KVDUtil_HnrBusnummerReeks($huis, $begin, $einde);
	
	}
	private function readBusletterReeks($huis, $begin){
		$einde = $this->readElement(KVDutil_HuisnummerReader::word);
		$this->reader->next();
		return new KVDUtil_HnrBusnummerReeks($huis, $begin, $einde);
	}
	
	private function readBisnummer($huisnr){
		$bisnr = $this->readElement(KVDutil_HuisnummerReader::nummer);
		if($this->reader->next() == KVDutil_HuisnummerReader::min)
			return $this->readBisnummerReeks($huisnr, $bisnr);
		else return new KVDUtil_HnrBisnummer($huisnr, $bisnr);	
	
	}
	private function readBisletter($huis, $bis){
		if($this->reader->next() == KVDutil_HuisnummerReader::min) 
			return $this->readBisletterReeks($huis, $bis);
		else return new KVDUtil_HnrBisletter($huis, $bis);	
	
	}
	private function readBusnummer($huis, $nummer){
		if($this->reader->next() == KVDutil_HuisnummerReader::min)
			return $this->readBusnummerReeks($huis, $nummer);
		else return new KVDUtil_HnrBusnummer($huis, $nummer);
	}
	private function readBusletter($huis, $letter){
		if($this->reader->next() == KVDutil_HuisnummerReader::min)
			return $this->readBusletterReeks($huis, $letter);
		else return new KVDUtil_HnrBusnummer($huis, $letter);
	}
	
	private function readBus($huis){
		$type = $this->reader->next();
		$bus = $this->reader->getContent();
		switch($type) {
			case KVDutil_HuisnummerReader::nummer: return $this->readBusnummer($huis, $bus);
			case KVDutil_HuisnummerReader::word: return $this->readBusletter($huis, $bus);
			default: return $this->error("Word or Number Expected, given ".$this->reader->typeText($type));
		}
	}
	
	private function readHuisnummerLijst($exp){
		$lijst = array();
		$lijst[] = $exp;
		while($this->reader->getType() == KVDutil_HuisnummerReader::comma) {
			$nummer = $this->readElement(KVDutil_HuisnummerReader::nummer);
			$lijst[] = $this->readHuisnummer($nummer);
		}
		if($this->reader->getType() == KVDutil_HuisnummerReader::eof) return $lijst;
		else return $this->error("Invalid separator, comma expected");
	}

	private function readHuisnummer($exp){
		switch($this->reader->next()) {
			case KVDutil_HuisnummerReader::min: return $this->readHuisnummerReeks($exp);
			case KVDutil_HuisnummerReader::slash: return $this->readBisnummer($exp);
			case KVDutil_HuisnummerReader::bus: return $this->readBus($exp);
			case KVDutil_HuisnummerReader::word: return $this->readBisletter($exp, $this->reader->getContent());
			default: return new KVDUtil_HnrHuisnummer($exp);
		}
	}
	
	private function readHuisnummerExpressie(){
		$nummer = $this->readElement(KVDutil_HuisnummerReader::nummer);
		$huisnummer = $this->readHuisnummer($nummer);
		switch($this->reader->getType()) {
			case KVDutil_HuisnummerReader::comma: return $this->readHuisnummerLijst($huisnummer);break;
			case KVDutil_HuisnummerReader::eof: return array($huisnummer);break;
			default: $this->error("Separator expected");
		}
	}
	
	public function parse($input){
		$this->reader->setInput($input);
		 return $this->readHuisnummerExpressie();
	}
	
	private function errorCode($given, $expected){
		$this->error($this->reader->typeText($given)."($given) given, ".$this->reader->typeText($expected)."($expected) expected.");
	}
	private function error($msg){
		$m = "Parse error:".$msg.", at ".$this->reader->getContent()."(".$this->reader->getPos().")";
		throw new Exception($m);
	}
}

/**
 * KVDutil_SequenceReader 
 *  Klasse die een reeks van huisnummers kan interpreteren en deze rij kan
 *	samenvatten. Bijvoorbeeld:
 *		"23 bus 5, 23 bus 6" -> Busnummerreeks "23 bus 5-6"
 *		"23", "24 bus 2" -> Huisnummer "23", Busnummer "24 bus 2"
 *		"25", "26", "27" -> Huisnummerreeks "25, 26-27"
 * @package KVD.util
 * @subpackage Huisnummer
 * @since september 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_SequenceReader{
	

	private $input;
	private $pos;
	
	private $result;

	public function __construct(){

	}
	private function readSpringReeks($reeks){
		while(($this->next() == "KVDUtil_HnrHuisnummer")&&($this->content()->getHuisnummer() == ($reeks->getEinde() +2)))
			$reeks->setEinde($reeks->getEinde() +2);
		return $reeks;
	}
	private function readVolgReeks($reeks){
		while(($this->next() == "KVDUtil_HnrHuisnummer")&&($this->content()->getHuisnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($reeks->getEinde() +1);
		return $reeks;
	}
	
	private function readHuisnummerReeks($huisnummer){
		$reeks = new KVDUtil_HnrHuisnummerReeks($huisnummer->getHuisnummer(),$huisnummer->getHuisnummer());
		if($this->next() != "KVDUtil_HnrHuisnummer") return $huisnummer;
		$nummer = $this->content()->getHuisnummer();
		if($nummer == ($reeks->getEinde()+1)) {
			$reeks->setSprong(false);
			$reeks->setEinde($nummer);
			return $this->readVolgReeks($reeks);
		}
		if ($nummer == ($reeks->getEinde()+2)) {
			$reeks->setEinde($nummer);
			return $this->readSpringReeks($reeks);
		}
		return $huisnummer;
	}
	
	private function readBisnummerReeks($bisnummer){
		$reeks = new KVDUtil_HnrBisnummerReeks($bisnummer->getHuisnummer(), $bisnummer->getBisnummer(), $bisnummer->getBisnummer());
		while(($this->next() == "KVDUtil_HnrBisnummer")&&($this->content()->getBisnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($reeks->getEinde() +1);
		if($reeks->getBegin() == $reeks->getEinde()) return $bisnummer;
		else return $reeks;
	}
	
	private function readBusnummerReeks($busnummer){;
		$reeks = new KVDUtil_HnrBusnummerReeks($busnummer->getHuisnummer(), $busnummer->getBusnummer(), $busnummer->getBusnummer());
		while(($this->next() == "KVDUtil_HnrBusnummer")&&($this->content()->getBusnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($reeks->getEinde() +1);
		if($reeks->getBegin() == $reeks->getEinde()) return $busnummer;
		else return $reeks;
	}
		private function readBusletterReeks($busletter){;
		$reeks = new KVDUtil_HnrBusletterReeks($busletter->getHuisletter(), $busletter->getBusletter(), $busletter->getBusletter());
		$einde = $reeks->getEinde();
		while(($this->next() == "KVDUtil_HnrBusletter")&&($this->content()->getBusletter() == ++$einde))
			$reeks->setEinde($einde);
		if($reeks->getBegin() == $reeks->getEinde()) return $busletter;
		else return $reeks;
	}
	
	private function readBisletterReeks($bisletter){
	
		$reeks = new KVDUtil_HnrBisletterReeks($bisletter->getHuisnummer(), $bisletter->getBisletter(), $bisletter->getBisletter());
		$einde = $reeks->getEinde();
		while(($this->next() == "KVDUtil_HnrBisletter")&&($this->content()->getBisletter() == (++$einde)))
			$reeks->setEinde($einde);
		if($reeks->getBegin() == $reeks->getEinde()) return $bisletter;
		else return $reeks;
	}
	
	public function readReeks(){
		switch($this->current()) {
			case "KVDUtil_HnrHuisnummer": return $this->readHuisnummerReeks($this->content());
			case "KVDUtil_HnrBisnummer": return $this->readBisnummerReeks($this->content());
			case "KVDUtil_HnrBusnummer": return $this->readBusnummerReeks($this->content());
			case "KVDUtil_HnrBusletter": return $this->readBusletterReeks($this->content());
			case "KVDUtil_HnrBisletter": return $this->readBisletterReeks($this->content());
			case "": return null;
			default: throw new Exception("Invalid type");
		}
	}
	
	public function read($in){
		$this->input = $in;
		$this->pos = 0;
		$this->result = array();
		while($this->current() != "") {
		 $r = $this->readReeks();
			$this->store($r);
		}
		return $this->result;
	}
	
	private function next(){
		$this->pos++;
		return $this->current();
	}
	
	private function current(){
		if ($this->pos >= sizeof($this->input)) return "";
		else return get_class($this->input[$this->pos]);
	}
	
	private function content(){
		return $this->input[$this->pos];
	}
	
	private function store($content){
		$this->result[] = $content;
	}
}

/**
 * KVDutil_HuisnummerFacade 
 * 
 * Deze class dient om huisnummerlabels uit te splitsen naar de indivduele labels of van
 * individuele labels terug samen te voegen naar een compactere notatie bv.:
 * <code>
 *  $facade = new KVDutil_HuisnummerFacade( );
 *  $huisnummers = $facade->split( '15-21' );
 *  echo $huisnummers[0]; // 15
 *  echo $huisnummers[1]; // 17
 *  echo $huisnummers[2]; // 19
 *  echo $huisnummers[3]; // 21
 *  $reeksen = $facade->merge($huisnummers);
 *  echo $reeksen[0]; // 15-21
 * </code>
 * @package KVD.util
 * @subpackage Huisnummer
 * @since 5 dec 2007
 * @copyright 2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */


class KVDutil_HuisnummerFacade {


	public $compares;

	private $parser;
	private $sequencer;
	
	public function __construct(){
		$this->parser = new KVDutil_HuisnummerParser();
		$this->sequencer = new KVDutil_SequenceReader();
		$this->compares = array();
	}
	
	
public function swap(&$input, $p1, $p2){
		$v = $input[$p1];
		$input[$p1] = $input[$p2];
		$input[$p2] = $v;
	}



	public function QuickHuisSort(&$input, $s, $e){

				if(abs($e -$s) < 2) {return $input;}
		
		$pos = $s;
		$pivot = $input[$s];
				
		
		for($i = $s+1; $i<$e; $i++){
			$c = $input[$i]->compareTo($pivot);
			$this->compares[] = array($input[$i], $pivot, $c);
			$this->compares[] = array($pivot, $input[$i], $pivot->compareTo($input[$i]));
			if( $c == -1) {
				$this->swap($input, $pos, $i);
				$pos++;
				$this->swap($input, $pos, $i);				
			}
		}
		
		$this->QuickHuisSort($input, $s, $pos);
		$this->QuickHuisSort($input, $pos+1, $e);
	}

	public function read($input){
		return $this->parser->parse($input);
	}
	
	public function sort(&$inputs){
		$this->QuickHuisSort($inputs, 0, sizeof($inputs));
	} 
	
	public function mergeArray($inputs){
		$this->sort($inputs);
		return $this->sequencer->read($inputs);
	}
	
	public function splitArray($inputs){
		$result = array();
		foreach($inputs as $input) 
			foreach($input->split() as $element)
				$result[] = $element;
		return $result;
	}
	
	public function toArray($input){
		return $this->read($input);
	}
	public function toString($inputs){
		$result = "";
		foreach($inputs as $input) $result.=", $input";
		return substr($result, 2);
	}
	
	public function split($input){
		$result = $this->read($input);
		$r = $this->splitArray($result);
		$this->sort($r);
		return $r;
	}
	
	public function merge($inputs){
		$result = $this->split($inputs);
		$merges = $this->mergeArray($result);
		return $merges;
	}
}


?>