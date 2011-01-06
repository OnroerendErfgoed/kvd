<?php
/**
 * @package KVD.util
 * @subpackage huisnummer
 * @version $Id: KVDutil_HuisnummerFacade.class.php 1 2007-10-05 13:16:16Z standadi $
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDUtil_HnrElement
 *  Een huisnummer element. Dit is de (abstracte) superklasse voor alle output van de 
 *  huisnummerlezer. Dit kan een huisnummer, huisnummerreeks, leesfout enz. zijn.
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDUtil_HnrElement{
	/**
	 * @var array array met alle data van een huisnummer.
	 */
	private $data;
	const HUISNR = 0;
	const BISN = 1;
	const BISL = 2;
	const BUSN = 3;
	const BUSL = 4;
	
	/**
	 * __construct
	 * @param integer huisnummer
	 * @param string bisnummer of bisletter
	 * @param string busnummer of busletter
	 * @param integer laatste huisnummer van de reeks
	 * @param string laatste bisnummer of bisletter van de reeks
	 * @param string laatste busnummer of busletter van de reeks
	 */
	public function __construct(
		$h1		, $bisn1 =-1, $bisl1 =-1, $busn1=-1,  $busl1=-1,
		$h2=-1, $bisn2 =-1, $bisl2 =-1, $busn2=-1,  $busl2=-1)
	{
		$this->data = array($h1, $bisn1, $bisl1, $busn1, $busl1, $h2, $bisn2, $bisl2, $busn2, $busl2);
	}
	
	/**
	 * getDatas
	 *  geeft de data array van dit huisnummer
	 * @return array
	 */
	public function getDatas()
	{
		return $this->data;
	}
	
	/**
	 * getData
	 *  geeft informatie van dit huisnummer
	 * @param integer op te vragen gegeven
	 * @return integer of string met data
	 */
	public function getData($i)
	{
		return $this->data[$i];
	}
	
	/**
	 * setData
	 */
	public function setData($i, $val)
	{
		$this->data[$i] = $val;
	}
	
	/**
	 * comparaTo
	 * @param KVDUtil_HnrElement el
	 * @return integer (-1 if $this < $el ; 0 if $this = $el ; 1 if $this > $el)
	 */
	public function compareTo($el) 
	{
		$i = 0;
		while(($i < 9) &&($this->data[$i] == $el->getData($i))){
			$i++;
		}
		if($this->data[$i] == $el->getData($i)) { return 0; }
		else if($this->data[$i] < $el->getData($i)) { return -1; }
		else { return 1; }
	}
	
	/**
	 * split
	 *  geeft een array met alle huisnummers die dit element
	 *  bevat.
	 * @return array een array met huisnummers
	 */
	abstract function split();
	
	/**
	 * isException
	 * @return bool
	 */
	abstract function isException();
	
	/**
	 * compare
	 * @param KVDUtil_HnrElement $el1
	 * @param KVDUtil_HnrElement $el2
	 * @return integer (-1 if $el1 < $el ; 0 if $el1 = $el ; 1 if $el1 > $el)
	 */
	static function compare($el1, $el2)
	{
		return $el1->compareTo($el2);
	}

}

/**
 * KVDUtil_HnrReadException
 *  Klasse voor een leesfout in de huisnummerlezer.
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrReadException extends KVDUtil_HnrElement{

	/**
	 * @var string
	 */
	private $error;
	/**
	 * @var string
	 */
	private $input;
	
	public function __construct($error, $input = "")
	{
		parent::__construct(-1);
		$this->error = $error;
		$this->input = $input;
	}
	
	public function isException()
	{
		return true;
	}
	
	public function setData($i,$val) {}

	public function split()
	{
		return array($this);
	}
	
	/**
	 * getMessage
	 * @return string error message
	 */
	public function getMessage()
	{
		return $this->error.": '".$this->input."'";
	}
	/**
	 * __toString
	 * string represrntatie van de input
	 */
	public function __toString()
	{
		return $this->input;
	}
}


/**
 * KVDUtil_HnrEnkelElement
 *  abstracte superklasse voor een huisnummer.
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDUtil_HnrEnkelElement extends KVDUtil_HnrElement{

	public function isException()
	{
		return false;
	}

	public function split()
	{
		return array($this);
	}
	/**
	 * getHuisnummer
	 * 
	 * @return integer het huisnummer van dit element
	 */
	public function getHuisnummer(){
		return $this->getData(KVDUtil_HnrElement::HUISNR);
	}
	/**
	 * setHuisnummer
	 * @param integer het nieuwe huisnummer van dit element
	 */
	public function setHuisnummer($nummer){
		$this->setData(KVDUtil_HnrElement::HUISNR , $nummer);
	}
}

/**
 * KVDUtil_HnrHuisnummer 
 *	Een eenvoudig huisnummer, bijv: 13 of 15.
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
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
		return "".$this->getHuisnummer();
	}
}

/**
 * KVDUtil_HnrBiselement
 *	Klasse dat een enkel huisnummer met bisnummer voorstelt, bijv "3/1" of "21/5"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDUtil_HnrBiselement extends KVDUtil_HnrEnkelElement{

	protected $bisIndex;
	
	public function getBiselement() {
		return $this->getData($this->bisIndex);
	}
}

/**
 * KVDUtil_HnrBisnummer 
 *	Klasse dat een enkel huisnummer met bisnummer voorstelt, bijv "3/1" of "21/5"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
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
		$this->bisIndex = 1;
	}
	/**
	 * __toString
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer()."/".$this->getBiselement();
	}
	/**
	 * getBisnummer
	 * @return integer
	 */
	public function getBisnummer(){
		return $this->getBiselement();
	}

}

 
/**
 * KVDUtil_HnrBisnummer 
 *  Klasse dat een enkel huisnummer met busnummer voorstelt, bijv "3 bus 1" of "53 bus 5"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusnummer extends KVDUtil_HnrBiselement{
	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer busnummer
	 */
	public function __construct($huis, $bus){
		parent::__construct($huis,-1, -1, $bus);
		$this->bisIndex = 3;
	}
	/**
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->getBiselement();
	}
	/**
	 * getBusnummer
	 * @return integer
	 */
	public function getBusnummer(){
		return $this->getBiselement();
	}

}

/**
 * KVDUtil_HnrBusletter 
 *	Klasse dat een enkel huisnummer met busletter voorstelt, bijv "3 bus A" of "53 bus D"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusletter extends KVDUtil_HnrBiselement{
	/**
	 * __construct
	 * @param integer huisnummer
	 * @param string busletter
	 */
	public function __construct($huis, $bus){
		parent::__construct($huis,-1,-1,-1,$bus);
		$this->bisIndex = 4;
	}
	/**
	 * __toString
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->getBiselement();
	}
	/**
	 * getBusletter
	 * @return string
	 */
	public function getBusletter(){
		return $this->getBiselement();
	}

}

/**
 * KVDUtil_HnrBisletter 
 *	Klasse dat een enkel huisnummer met bisletter voorstelt, bijv "3A" of "53D"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBisletter extends KVDUtil_HnrBiselement{

	/**
	 * __construct
	 * @param integer huisnummer
	 * @param string bisletter
	 */
	public function __construct($huis, $bis){
		parent::__construct($huis,-1,$bis);
		$this->bisIndex = 2;
	}
	/**
	 * __toString
	 * @return string representatie van het nummer
	 */
	public function __toString(){
		return $this->getHuisnummer().$this->getBiselement();
	}
	/**
	 * getBisletter
	 * @return string
	 */
	public function getBisletter(){
		return $this->getBiselement();
	}

}


/**
 * KVDUtil_HnrReeksElement 
 *	Abstracte klasse voor alle reeksen (compacte notatie van een reeks) huisnummers
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDUtil_HnrReeksElement extends KVDUtil_HnrEnkelElement{

	/**
	 * @var integer
	 */
	protected $beginIndex;
	/**
	 * @var integer
	 */
	protected $eindeIndex;

	/**
	 * getBegin
	 * @return integer het begin nummer van deze reeks
	 */
	public function getBegin(){
		return $this->getData($this->beginIndex);
	}
	/**
	 * setBegin
	 * @param integer het begin nummer van deze reeks
	 */
	public function setBegin($val){
		return $this->setData($this->beginIndex, $val);
	}
	/**
	 * getEinde
	 * @return integer het laatste nummer van deze reeks
	 */	
	public function getEinde(){
		return $this->getData($this->eindeIndex);
	}
	/**
	 * setEinde
	 * @param integer het laatste nummer van deze reeks
	 */	
	public function setEinde($val){
		return $this->setData($this->eindeIndex, $val);
	}
	
}

/**
 * KVDUtil_HnrHuisnummerReeks 
 *  Een reeks van huisnummers.
 *  bijv "33, 35, 37" -> "33-37"
 *  bijv "33, 34, 35, 36" -> "33-36"
 *  bijv "32, 33, 34, 35, 36"-> "32, 33-36"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
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
		parent::__construct($begin,-1, -1,-1,-1,$einde);
		$this->beginIndex = 0;
		$this->eindeIndex = 5;
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
		$diff = ($this->getEinde() - $this->getBegin());
		if (($diff%2 == 0) && (!$this->spring))
			return $this->getBegin().", ".($this->getBegin()+1)."-".$this->getEinde();
		return $this->getBegin().'-'.$this->getEinde();
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
		for($i = $this->getBegin(); $i<= $this->getEinde(); $i += $jump){
			$r[] = new KVDUtil_HnrHuisnummer($i);
		}
		return $r;
	}
}


/**
 * KVDUtil_HnrBisnummerReeks 
 *  Een reeks van bisnummers.
 *  bijv "33/1, 32/2, 33/3" -> "33/1-3"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBisnummerReeks extends KVDUtil_HnrReeksElement{

	/**
	 * @param integer huis het huisnummer
	 * @param integer begin het eerste bisnummer van de reeks
	 * @param integer einde het laatste bisnummer van de reeks
	 */
	public function __construct($huis, $begin, $einde){
		parent::__construct($huis, $begin,-1, -1,-1, $huis, $einde);
		$this->beginIndex = 1;
		$this->eindeIndex = 6;
	}
	/**
	 * __toString
	 * @return string de string representatie van de reeks
	 */ 
	public function __toString(){
		return $this->getHuisnummer()."/".$this->getBegin()."-".$this->getEinde();
	}

	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */
	public function split(){
		$r = array();
		for($i = $this->getBegin(); $i<= $this->getEinde(); $i++){
			$r[] = new KVDUtil_HnrBisnummer($this->getHuisnummer(), $i);
		}
		return $r;
	}
}


/**
 * KVDUtil_HnrBisletterReeks 
 *  Een reeks van bisletters.
 *  bijv "33A, 32B, 33C" -> "33A-C"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBisletterReeks extends KVDUtil_HnrReeksElement{

	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer het eerste nummer van de reeks
	 * @param integer het laatste nummer van de reeks
	 */	
	public function __construct($huis, $begin, $einde){
		parent::__construct($huis,-1, $begin,-1,-1,$huis,-1, $einde);
		$this->beginIndex = 2;
		$this->eindeIndex = 7;
	}
	/**
	 * __toString
	 * @return Een string representatie van de bisletter reeks, bijv: 13A-C.
	 */
	public function __toString(){
		return $this->getHuisnummer().$this->getBegin()."-".$this->getEinde();
	}	
	
	
	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */	
	public function split(){
		$r = array();
		for($i = $this->getBegin(); $i<= $this->getEinde(); $i++){
			$r[] = new KVDUtil_HnrBisletter($this->getHuisnummer(), $i);
		}
		return $r;
	}
}




/**
 * KVDUtil_HnrBusnummerReeks 
 *  Een reeks van busnummers.
 *  bijv "33 bus 1, 32 bus 2, 33 bus 3" -> "33 bus 1-3"
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusnummerReeks extends KVDUtil_HnrReeksElement{

	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer het eerste nummer van de reeks
	 * @param integer het laatste nummer van de reeks
	 */	
	public function __construct($huis, $begin, $einde){
		parent::__construct($huis,-1, -1, $begin,-1, $huis,-1, -1, $einde);
		$this->beginIndex = 3;
		$this->eindeIndex = 8;
	}
	
	/**
	 * __toString
	 * @return Een string representatie van de busnummer reeks, bijv: 13 bus 1-3.
	 */
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->getBegin()."-".$this->getEinde();
	}	

	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */	
	public function split(){
		$r = array();
		for($i = $this->getBegin(); $i<= $this->getEinde(); $i++){
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
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDUtil_HnrBusletterReeks extends KVDUtil_HnrReeksElement{
	/**
	 * __construct
	 * @param integer huisnummer
	 * @param integer het eerste nummer van de reeks
	 * @param integer het laatste nummer van de reeks
	 */		
	public function __construct($huis, $begin, $einde){
		parent::__construct($huis,-1,-1,-1, $begin,$huis,-1,-1,-1,$einde);
		$this->beginIndex = 4;
		$this->eindeIndex = 9;
	}
	/**
	 * __toString
	 * @return Een string representatie van de busnummer reeks, bijv: 13 bus 1-3.
	 */
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->getBegin()."-".$this->getEinde();
	}	
	/**
	 * split
	 * @return array een array met de individuele bisnummers van deze reeks
	 */	
	public function split(){
		$r = array();
		for($i = $this->getBegin(); $i<= $this->getEinde(); $i++){
			$r[] = new KVDUtil_HnrBusletter($this->getHuisnummer(), $i);
		}
		return $r;
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
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_SequenceReader{
	
	/**
	 * @var array array van te verzamelen elementen.
	 */	
	private $input;

	/**
	 * @var integer positie in de input array.
	 */	
	private $pos;

	/**
	 * @var array de verzamelde elementen
	 */	
	private $result;

	public function __construct(){

	}
	
	
	/**
	 * readSpringReeks
	 *  Leest een reeks van huisnummers, die telkens een nummer overslaan.
	 * @param KVDUtil_HnrHuisnummerReeks de reeks tot nu toe
	 * @return KVDUtil_HnrHuisnummerReeks de volledige reeks
	 */	
	private function readSpringReeks($reeks){
		while(($this->next() == "KVDUtil_HnrHuisnummer")&&($this->content()->getHuisnummer() == ($reeks->getEinde() +2)))
			$reeks->setEinde($reeks->getEinde() +2);
		return $reeks;
	}
	/**
	 * readVolgReeks
	 *  Leest een reeks van huisnummers, waar de nummers elkaar opvolgen.
	 * @param KVDUtil_HnrHuisnummerReeks de reeks tot nu toe
	 * @return KVDUtil_HnrHuisnummerReeks de volledige reeks
	 */	
	private function readVolgReeks($reeks){
		while(($this->next() == "KVDUtil_HnrHuisnummer")&&($this->content()->getHuisnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($reeks->getEinde() +1);
		return $reeks;
	}
	/**
	 * readHuisnummerReeks
	 *  Leest een reeks van huisnummers
	 * @param KVDUtil_HnrHuisnummer eerste element van de reeks
	 * @return KVDUtil_HnrHuisnummerReeks de volledige reeks
	 */		
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

	/**
	 * readBisnummerReeks
	 *  Leest een reeks van bisnummers
	 * @param KVDUtil_HnrBisnummer eerste nummer van de reeks
	 * @return KVDUtil_HnrBisnummerReeks de volledige reeks
	 */		
	private function readBisnummerReeks($bisnummer){
		$reeks = new KVDUtil_HnrBisnummerReeks($bisnummer->getHuisnummer(), $bisnummer->getBisnummer(), $bisnummer->getBisnummer());
		while(($this->next() == "KVDUtil_HnrBisnummer")&&($this->content()->getBisnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($reeks->getEinde() +1);
		if($reeks->getBegin() == $reeks->getEinde()) return $bisnummer;
		else return $reeks;
	}

	/**
	 * readBusnummerReeks
	 *  Leest een reeks van busnummers
	 * @param KVDUtil_HnrBusnummer eerste nummer van de reeks
	 * @return KVDUtil_HnrBusnummerReeks de volledige reeks
	 */			
	private function readBusnummerReeks($busnummer){;
		$reeks = new KVDUtil_HnrBusnummerReeks($busnummer->getHuisnummer(), $busnummer->getBusnummer(), $busnummer->getBusnummer());
		while(($this->next() == "KVDUtil_HnrBusnummer")&&($this->content()->getBusnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($reeks->getEinde() +1);
		if($reeks->getBegin() == $reeks->getEinde()) return $busnummer;
		else return $reeks;
	}

	/**
	 * readBusletterReeks
	 *  Leest een reeks van busletters
	 * @param KVDUtil_HnrBusletter eerste nummer van de reeks
	 * @return KVDUtil_HnrBusletterReeks de volledige reeks
	 */	
		private function readBusletterReeks($busletter){;
		$reeks = new KVDUtil_HnrBusletterReeks($busletter->getHuisletter(), $busletter->getBusletter(), $busletter->getBusletter());
		$einde = $reeks->getEinde();
		while(($this->next() == "KVDUtil_HnrBusletter")&&($this->content()->getBusletter() == ++$einde))
			$reeks->setEinde($einde);
		if($reeks->getBegin() == $reeks->getEinde()) return $busletter;
		else return $reeks;
	}
	/**
	 * readBisletterReeks
	 *  Leest een reeks van bisletters
	 * @param KVDUtil_HnrBisletter eerste nummer van de reeks
	 * @return KVDUtil_HnrBisletterReeks de volledige reeks
	 */	
	private function readBisletterReeks($bisletter){
	
		$reeks = new KVDUtil_HnrBisletterReeks($bisletter->getHuisnummer(), $bisletter->getBisletter(), $bisletter->getBisletter());
		$einde = $reeks->getEinde();
		while(($this->next() == "KVDUtil_HnrBisletter")&&($this->content()->getBisletter() == (++$einde)))
			$reeks->setEinde($einde);
		if($reeks->getBegin() == $reeks->getEinde()) return $bisletter;
		else return $reeks;
	}
	
	private function skip(){
		$element = $this->content();
		$this->next();
		return $element;
	}
	/**
	 * readReeks
	 *  Leest een reeks van huisnummers, ongeacht hun type, uit de input array.
	 * @return KVDUtil_HnrReeksElement de volledige reeks
	 */	
	public function readReeks(){
		switch($this->current()) {
			case "KVDUtil_HnrHuisnummer": return $this->readHuisnummerReeks($this->content());
			case "KVDUtil_HnrBisnummer": return $this->readBisnummerReeks($this->content());
			case "KVDUtil_HnrBusnummer": return $this->readBusnummerReeks($this->content());
			case "KVDUtil_HnrBusletter": return $this->readBusletterReeks($this->content());
			case "KVDUtil_HnrBisletter": return $this->readBisletterReeks($this->content());
			case "KVDUtil_HnrReadException": return $this->skip();
			case "": return null;
			default: throw new Exception("Invalid type: ".$this->current()." is of type: '".get_class($this->current())."'");
		}
	}
	/**
	 * read
	 *  Leest een array van te verzamelen elementen in.
	 * @param array de input array
	 * @return KVDUtil_HnrReeksElement de volledige reeks
	 */	
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
	/**
	 * next
	 *  geeft het volgende element in de input array terug
	 * @return KVDUtil_HnrElement
	 */		
	 private function next(){
		$this->pos++;
		return $this->current();
	}
	/**
	 * current
	 *  geeft het huidige element in de input array terug
	 * @return KVDUtil_HnrElement
	 */			
	private function current(){
		if ($this->pos >= sizeof($this->input)) return "";
		else return get_class($this->input[$this->pos]);
	}
	/**
	 * current
	 *  geeft de inhoud van huidige element in de input array terug
	 * @return string
	 */		
	private function content(){
		return $this->input[$this->pos];
	}
	/**
	 * store
	 *  slaat het gevormde reeks element op.
	 * @param het resultaat
	 */		
	private function store($content){
		$this->result[] = $content;
	}
}



/**
 * KVDutil_HnrReader 
 *  Klasse die een reeks huisnummers inleest. Bijvoorbeeld:
 *		"23 bus 5, 23 bus 6" -> array (Busnummer "23 bus 5", Busnummer "23 B-6")
 *		"23", "24 bus 2" -> array (Huisnummer "23", Busnummer "24 bus 2")
 *		"25-27" -> array(Huisnummerreeks "25, 26-27")
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_HnrReader{

	/**
	 * [ ]* staat voor een willekeurig aantal spaties. Dit wordt tussen elk element gezet om overtollige of afwezige spaties op te vangen.
	 * (\d+)  staat voor een nummer.
	 * [/|_] staat voor één '/' of '_'
	 * (\w+) staat voor een woord: aantal letters of cijfers.
	 */


	/**
	 * huisnummer syntax:
	 * "<nummer>"
	 * @var string 
	 */
	const huis = "#^[ ]*(\d+)[ ]*$#";
	/**
	 * bisnummer syntax:
	 * " <nummer>  <'/' of '_'>  <nummer> "
	 * @var string  
	 */
	const bisn = "#^[ ]*(\d+)[ ]*[/|_][ ]*(\d+)[ ]*$#";
	/**
	 * busletter syntax:
	 * " <nummer>  <woord> "
	 * @var string  
	 */
	const bisl = "#^[ ]*(\d+)[ ]*(\w+)[ ]*$#";
	/**
	 * busnummer syntax:
	 * " <nummer>  'bus' <nummer> " 
	 * @var string  
	 */
	const busn = "#^[ ]*(\d+)[ ]*bus[ ]*(\d+)[ ]*$#i";
	/**
	 * busletter syntax:
	 * " <nummer>  'bus' <woord> "
	 * @var string  
	 */
	const busl = "#^[ ]*(\d+)[ ]*bus[ ]*(\w+)[ ]*$#i";
	/**
	 * huisnummerreeks syntax:
	 * " <nummer>  '-'  <nummer> "
	 * @var string  
	 */	
	const huis_r = "#^[ ]*(\d+)[ ]*-[ ]*(\d+)[ ]*$#";
	/**
	 * bisnummerreeks syntax:
	 * " <nummer>  <'/' of '_'>  <nummer>  '-' <nummer> "
	 * @var string  
	 */	
	const bisn_r = "#^[ ]*(\d+)[ ]*[/|_][ ]*(\d+)[ ]*-[ ]*(\d+)[ ]*$#";
	/**
	 * bisletterreeks syntax:
	 * " <nummer>  <'/' of '_'>  <nummer>  '-' <nummer> "
	 * @var string  
	 */	
	const bisl_r = "#^[ ]*(\d+)[ ]*(\w+)[ ]*-[ ]*(\w+)[ ]*#i";
	/**
	 * busnummerreeks syntax:
	 * " <nummer>  'bus'  <nummer>  '-' <nummer> "
	 * @var string  
	 */
	const busn_r = "#^[ ]*(\d+)[ ]*bus[ ]*(\d+)[ ]*-[ ]*(\d+)[ ]*#";
	/**
	 * busletterreeks syntax:
	 * " <nummer>  'bus'  <woord>  '-' <woord> "
	 * @var string  
	 */
	const busl_r = "#^[ ]*(\d+)[ ]*bus[ ]*(\w+)[ ]*-[ ]*(\w+)[ ]*#i";

	/**
	 * @var integer
	 */
	const ERR_EXCEPTIONS = 0;
	/**
	 * @var integer
	 */
	const ERR_IGNORE_INVALID_INPUT = 1;
	/**
	 * @var integer
	 */
	const ERR_REMOVE_INVALID_INPUT = 2;
	
	

	/**
	 * readNummer
	 *  leest een huisnummer. Dit kan een eenvoudig huisnummer, bisnummer, reeks enz zijn.
	 * @param string input
	 * @return KVDUtil_HnrElement met het huisnummer of de fout indien de input incorrect is.
	 */	
	static function readNummer($input)
	{
		if(preg_match(KVDutil_HnrReader::huis, $input, $matches)) { return new KVDUtil_HnrHuisnummer($matches[1]); } 
		elseif(preg_match(KVDutil_HnrReader::busn, $input, $matches)) { return new KVDUtil_HnrBusnummer($matches[1], $matches[2]); } 
		elseif(preg_match(KVDutil_HnrReader::busl, $input, $matches)) {	return new KVDUtil_HnrBusletter($matches[1], $matches[2]); }
		elseif(preg_match(KVDutil_HnrReader::bisn, $input, $matches)) {
			return new KVDUtil_HnrBisnummer($matches[1], $matches[2]); }
		elseif(preg_match(KVDutil_HnrReader::bisl, $input, $matches)) {	return new KVDUtil_HnrBisletter($matches[1], $matches[2]); }
		elseif(preg_match(KVDutil_HnrReader::huis_r, $input, $matches)){ 
			return new KVDUtil_HnrHuisnummerReeks($matches[1], $matches[2], (((int)$matches[1] - (int)$matches[2])%2 ==0));}
		elseif(preg_match(KVDutil_HnrReader::busn_r, $input, $matches)){ return new KVDUtil_HnrBusnummerReeks($matches[1], $matches[2], $matches[3]);}
		elseif(preg_match(KVDutil_HnrReader::busl_r, $input, $matches)){ return new KVDUtil_HnrBusletterReeks($matches[1], $matches[2], $matches[3]);}
		elseif(preg_match(KVDutil_HnrReader::bisn_r, $input, $matches)){ return new KVDUtil_HnrBisnummerReeks($matches[1], $matches[2], $matches[3]);}
		elseif(preg_match(KVDutil_HnrReader::bisl_r, $input, $matches)){	return new KVDUtil_HnrBisletterReeks($matches[1], $matches[2], $matches[3]);}
		else return new KVDUtil_HnrReadException("Could not parse/understand", $input);
	}
		
	/**
	 * readString
	 *  leest een string met een lijst van huisnummers en geeft een array met huisnummerobjecten terug.
	 * @param string input
	 * @param integer flag voor error handling.
	 * @return array
	 */
	static function readString($input, $flag = 1)
	{
		return KVDutil_HnrReader::readArray(explode(",", $input), $flag);
	}
	
	
	static function handleException($exception, &$results = array(), $flag = 1)
	{
		switch($flag) {
			case (KVDutil_HnrReader::ERR_EXCEPTIONS): throw new Exception($exception->getMessage()); break;
			case (KVDutil_HnrReader::ERR_IGNORE_INVALID_INPUT): $results[] = $exception; return $results; break;
			case (KVDutil_HnrReader::ERR_REMOVE_INVALID_INPUT): return $results; break;
			default: throw new Exception("Invalid flag for KVDutil_HnrReader. Given ".$flag);
		}
	}
	/**
	 * readString
	 *  leest een array met huisnummerstring in en geeft een array met huisnummerobjecten terug.
	 * @param string input
	 * @param integer flag voor error handling.
	 * @return array
	 */	
	static function readArray($inputs, $flag = 1)
	{
		$result = array();
		foreach($inputs as $input) {
			$element = KVDutil_HnrReader::readNummer($input); 
			if($element->isException()) { KVDutil_HnrReader::handleException($element, $result, $flag); }
			else 
				$result[] = $element;	
		}
		return $result;
	}
}




/**
 * KVDutil_HnrSpeedSplitter 
 *  Klasse die in een string met huisnummers, de reeksen opsplitst.
 * @package KVD.util
 * @subpackage huisnummer
 * @since september 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_HnrSpeedSplitter{

	/**
	 * [ ]* staat voor een willekeurig aantal spaties. Dit wordt tussen elk element gezet om overtollige of afwezige spaties op te vangen.
	 * (\d+)  staat voor een nummer.
	 * [/|_] staat voor één '/' of '_'
	 * (\w+) staat voor een woord: aantal letters of cijfers.
	 */


	/**
	 * huisnummerreeks syntax:
	 * "<nummer> '-' <nummer>"
	 * @var string 
	 */	
	const reeks = "#,[ ]*([\d]+)[ ]*-[ ]*([\d]+)#";
	
	/**
	 * bisnummerreeks syntax:
	 * " <nummer>  <'/' of '_'>  <nummer>  '-' <nummer> "
	 * @var string  
	 */	
	const bisnr = "#,[ ]*(\d+)[ ]*[/|_][ ]*(\d+)[ ]*-[ ]*(\d+)#";
	 
	/**
	 * bisletterreeks syntax:
	 * " <nummer>  <'/' of '_'>  <letters>  '-' <letters> "
	 * @var string  
	 */	
	const bislr = "#,[ ]*(\d+)[ ]*([a-zA-Z]+)[ ]*-[ ]*(\w+)#";

	/**
	 * busreeks syntax:
	 * " <nummer>  'bus'  <nummer>  '-' <nummer of letter> "
	 * @var string  
	 */
	const busnr = "#,[ ]*(\d+)[ ]*[bus][ ]*(\w+)[ ]*-[ ]*(\w+)#";


	/**
	 * splitHuisnummers
	 * @param array met begin huisnummer en einde huisnummer
	 * @return string met de gesplitste huisnummerreeks
	 */
	static function splitHuisnummers($match)
	{
		$res = "";
		$jump = (((int)$match[1] - (int)$match[2])%2 ==0)? 2 : 1;
		for($i = (int)$match[1]; $i<= (int)$match[2]; $i+=$jump) {
			$res .= ", ".$i;
		}
		return $res;
	}
	/**
	 * splitBisnummer
	 * @param array met huisnummer, begin bisnummer en einde bisnummer
	 * @return string met de gesplitste reeks
	 */
	static function splitBisnummer($match)
	{
		$res = "";
		for($i = $match[2]; $i<= $match[3]; $i++) {
			$res .= ", ".$match[1]."/".$i;
		}
		return $res;

	}
	/**
	 * splitBisnummer
	 * @param array met huisnummer, begin busnummer en einde busnummer
	 * @return string met de gesplitste reeks
	 */
	static function splitBusnummer($match)
	{
		$res = "";
		for($i = $match[2]; $i<= $match[3]; $i++) {
			$res .= ", ".$match[1]." bus ".$i;
		}
		return $res;

	}
	/**
	 * splitBisletter
	 * @param array met huisnummer, begin bisletter en einde bisletter
	 * @return string met de gesplitste reeks
	 */
	static function splitBisLetter($match)
	{
		$res = "";
		for($i = $match[2]; $i<= $match[3]; $i++) {
			$res .= ", ".$match[1].$i;
		}
		return $res;

	}
	/**
	 * split
	 * @param string inputstring met huisnummer(s)
	 * @return string met uitgewerkte huisnummerreeksen
	 */
	static function split($input) 
	{
		$pos = 0;
		$input = preg_replace_callback(KVDutil_HnrSpeedSplitter::busnr, array("KVDutil_HnrSpeedSplitter", "splitBusnummer"), $input);
		$input = preg_replace_callback(KVDutil_HnrSpeedSplitter::bisnr, array("KVDutil_HnrSpeedSplitter", "splitBisnummer"), $input);
		$input = preg_replace_callback(KVDutil_HnrSpeedSplitter::bislr, array("KVDutil_HnrSpeedSplitter", "splitBisLetter"), $input);
		$input = preg_replace_callback(KVDutil_HnrSpeedSplitter::reeks, array("KVDutil_HnrSpeedSplitter", "splitHuisnummers"), $input);
		return explode(", ", $input);
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
 * @subpackage huisnummer
 * @since 5 dec 2007
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@eds.com>  
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDutil_HuisnummerFacade {

	/**
	 * @var KVDutil_HnrReader
	 */
	private $reader;
	
	/**
	 * @var KVDutil_HnrSequenceReader
	 */
	private $sequencer;
	
	/**
	 * @var integer
	 */
	private $flag;
	
	/**
	 * __construct
	 * @param integer flag voor errorhandling
	 */
	public function __construct($flag = 1)
	{
		$this->flag = $flag;
		$this->sequencer = new KVDutil_SequenceReader();
		$this->reader = new KVDutil_HnrReader($flag);
	}
		
	/**
	 * stringToNummers
	 * @param string met huisnummers/huisnummerreeksen
	 * @return array met huisnummerobjecten
	 */
	public function stringToNummers($input)
	{
		return $this->reader->readString($input, $this->flag);
	}

	/**
	 * nummersToString
	 * @param array met huisnummerobjecten
	 * @return string met de huisnummers
	 */
	public function nummersToString($inputs)
	{
		$result = "";
		foreach($inputs as $input) $result.=", $input";
		return substr($result, 2);
	}

	/**
	 * sortNummers
	 * @param array (by reference!!) met huisnummerobjecten
	 * @return array met gesorteerde huisnummerobjecten
	 */
	public function sortNummers(&$inputs)
	{
		usort( $inputs ,array("KVDUtil_HnrElement", "compare"));
		return $inputs;
	}
	
	/**
	 * splitNummers
	 * @param array met huisnummerobjecten
	 * @return array met gespliste huisnummerobjecten
	 */
	public function splitNummers($inputs)
	{
		$result = array();
		foreach($inputs as $input)
			foreach($input->split() as $el)
				$result[] = $el;
		return $result;
	}

	/**
	 * mergeNummers
	 * @param array met huisnummerobjecten
	 * @param boolean geeft weer of de lijst eerst gesorteerd moet worden.
	 * @return array met samen gevoegde huisnummerobjecten (reeksen waar het kan)
	 */
	public function mergeNummers($inputs, $sort = true)
	{
		if($sort) {
			$this->sortNummers($inputs);
		}
		return $this->sequencer->read($inputs);
	}
	
	/**
	 * split
	 * @param string met huisnummers (en reeksen)
	 * @return array met individuele huisnummerobjecten
	 */
	public function split($input)
	{
		return $this->splitNummers($this->stringToNummers($input));
	}
	
	/**
	 * speedySplits
	 * @param string inputstring met huisnummers
	 * @return string met huisnummers, waarbij reeksen opgedeeld zijn in hun individuele nummers.
	 */
	public function speedySplit($input)
	{
		return KVDutil_HnrSpeedSplitter::split($input);
	}
	
	
	
	/**
	 * separateEven
	 *
	 */
	public function separateEven($input)
	{
        $even = array();
        $oneven = array();
        foreach($input as $nummer) {
            if(($nummer->getData(0))&1){
                $oneven[] = $nummer;
            } else {
                $even[] = $nummer;
            }
        }
        return array("even"=>$even, "oneven"=>$oneven);
	}
	
	/**
	 * separateMerge
	 * @param string inputstring met huisnummers
	 * @return string met samengevoegde huisnummers tot reeksen, met even en oneven nummers gescheiden
	 */
	public function separateMerge($input) {
		$reeksen = $this->stringToNummers($input);
		$nummers = $this->splitNummers($reeksen);
		$separate = $this->separateEven($nummers);
		$even = $this->mergeNummers($separate["even"]);
		$oneven = $this->mergeNummers($separate["oneven"]);
		return $this->sortNummers(array_merge($even, $oneven));
	}
	
	/**
	 * straightMerge
	 * @param string inputstring met huisnummers
	 * @return string met samengevoegde huisnummers tot reeksen
	 */
	public function straightMerge($input)
	{
		$reeksen = $this->stringToNummers($input);
		$nummers = $this->splitNummers($reeksen);
		return $this->mergeNummers($nummers);
	}
	
	/**
	 * merge
	 * @param string inputstring met huisnummers
	 * @param boolean seperate geeft aan of even en onever al dan niet gescheiden blijven.
	 * @return string met samengevoegde huisnummers tot reeksen
	 */
	public function merge($input, $separate = true)
	{
        if($separate) { 
            return $this->separateMerge($input);
        } else {
            return $this->straightMerge($input);
        }
	}
}

?>
