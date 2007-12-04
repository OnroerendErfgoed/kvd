

<?php


abstract class KVDUtil_HnrHuisnummerElement{

	abstract function accept($visitor);
	abstract function compareTo($nummer);

}

abstract class KVDUtil_HnrEnkelElement extends KVDUtil_HnrHuisnummerElement{
	
	protected $nummer;
	
	public function __construct($nummer){
		$this->nummer= $nummer;
	}
	
	public function getNummer(){
		return $this->nummer;
	}

	public function setNummer($nummer){
		$this->nummer = $nummer;
	}
	
	public function split(){
		return array($this);
	}
	
}




class KVDUtil_HnrHuisnummer extends KVDUtil_HnrEnkelElement{

	public function __construct($nummer){
		parent::__construct($nummer);
	}

	public function __toString(){
		return "$this->nummer";
	}
	public function accept($visitor){
		return $visitor->visitHuisnummer($this);
	}
	
	public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToHuisnummer($this));
	}
	
}

class KVDUtil_HnrBisnummer extends KVDUtil_HnrEnkelElement{

	private $bis;

	public function __construct($huis, $bis){
		parent::__construct($huis);
		$this->bis = $bis;
	}
	public function __toString(){
		return $this->getHuisnummer()."/".$this->bis;
	}
	
	public function getHuisnummer(){
		return $this->getNummer();
	}
	public function getBisnummer(){
		return $this->bis;
	}
	
	public function accept($visitor){
		return $visitor->visitBisnummer($this);
	}
	
	public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBisnummer($this));
	}

}


class KVDUtil_HnrBusnummer extends KVDUtil_HnrEnkelElement{
	private $bus;

	public function __construct($huis, $bus){
		parent::__construct($huis);
		$this->bus = $bus;
	}
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->bus;
	}
	public function getHuisnummer(){
		return $this->getNummer();
	}
	public function getBusnummer(){
		return $this->bus;
	}	
	public function accept($visitor){
		return $visitor->visitBusnummer($this);
	}
	public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBusnummer($this));
	}
}

class KVDUtil_HnrBusletter extends KVDUtil_HnrEnkelElement{
	private $bus;

	public function __construct($huis, $bus){
		parent::__construct($huis);
		$this->bus = $bus;
	}
	public function __toString(){
		return $this->getHuisnummer()." bus ".$this->bus;
	}
	public function getHuisnummer(){
		return $this->getNummer();
	}
	public function getBusletter(){
		return $this->bus;
	}	
	public function accept($visitor){
		return $visitor->visitBusnummer($this);
	}
	public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBusletter($this));
	}
}


class KVDUtil_HnrBisletter extends KVDUtil_HnrEnkelElement{

	private $bis;

	public function __construct($huis, $bis){
		parent::__construct($huis);
		$this->bis = $bis;
	}
	public function __toString(){
		return $this->getHuisnummer().$this->bis;
	}
		
	public function getHuisnummer(){
		return $this->getNummer();
	}
	public function getBisletter(){
		return $this->bis;
	}
	public function accept($visitor){
		return $visitor->visitBisletter($this);
	}
	public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBisletter($this));
	}

}






/**********************************************/

abstract class KVDUtil_HnrReeksElement extends KVDUtil_HnrHuisnummerElement{
	protected $begin;
	protected $einde;
	
	
	public function __construct($begin, $einde){
		$this->begin = $begin;
		$this->einde = $einde;
	}
	

	public function getBegin(){
		return $this->begin;
	}

	public function setBegin($begin){
		$this->begin = $begin;
	}
	
	public function getEinde(){
		return $this->einde;
	}
	public function setEinde($einde){
		return $this->einde = $einde;
	}
	
}


class KVDUtil_HnrHuisnummerReeks extends KVDUtil_HnrReeksElement{
	private $spring;

	public function __construct($begin, $einde, $spring = true){
		parent::__construct($begin,$einde);
		$this->spring = $spring;
		
	}
	public function __toString(){
		$diff = ($this->einde - $this->begin);
		if (($diff/2 == floor($diff/2)) && (!$this->spring))
			return $this->begin.", ".($this->begin+1)."-".$this->einde;
		return $this->begin.'-'.$this->einde;
	}
	

	public function isVolgReeks(){
		return !($this->spring);
	}
	public function isSpringReeks(){
		return ($this->spring);
	}
	public function setSprong($val){
		$this->spring = $val;
	}
	public function accept($visitor){
		return $visitor->visitHuisnummerReeks($this);
	}
	public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToHuisnummerReeks($this));
	}
	public function precedes($nummer){
		return $nummer->accept(new KVDUtil_HnrPrecedesToHuisnummerReeks($this));
	}
	public function compatibleTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompatibleToHuisnummerReeks($this));
	}
	
	public function add($nummer){
		$this->setEinde($nummer->getNummer());
	}
	
	public function split(){
		$r = array();
		$jump = ($this->isSpringReeks()) ? 2 : 1;
		for($i = $this->begin; $i<= $this->einde; $i += $jump){
			$r[] = new KVDUtil_HnrHuisnummer($i);
		}
		return $r;
	}
}

class KVDUtil_HnrBisnummerReeks extends KVDUtil_HnrReeksElement{
	private $huis;
	
	public function __construct($huis, $begin, $einde){
		parent::__construct($begin,$einde);
		$this->huis = $huis;
	}
	public function __toString(){
		return $this->huis."/".$this->begin."-".$this->einde;
	}	
	
	public function getHuisnummer(){
		return $this->huis;
	}
	
	public function setHuisnummer($huis){
		$this->huis = $huis;
	}
		public function accept($visitor){
		return $visitor->visitBisnummerReeks($this);
	}
		public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBisnummerReeks($this));
	}
		public function precedes($nummer){
		return $nummer->accept(new KVDUtil_HnrPrecedesToBisnummerReeks($this));
	}
		public function compatibleTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompatibleToBisnummerReeks($this));
	}
		public function add($nummer){
		$this->setEinde($nummer->getBisnummer());
	}
	public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBisnummer($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

class KVDUtil_HnrBusnummerReeks extends KVDUtil_HnrReeksElement{
	private $huis;
	
	public function __construct($huis, $begin, $einde){
		parent::__construct($begin,$einde);
		$this->huis = $huis;
	}
	public function __toString(){
		return $this->huis." bus ".$this->begin."-".$this->einde;
	}	
		public function getHuisnummer(){
		return $this->huis;
	}
	
	public function setHuisnummer($huis){
		$this->huis = $huis;
	}
		public function accept($visitor){
		return $visitor->visitBusnummerReeks($this);
	}
		public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBusnummerReeks($this));
	}
		public function precedes($nummer){
		return $nummer->accept(new KVDUtil_HnrPrecedesToBusnummerReeks($this));
	}
		public function compatibleTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompatibleToBusnummerReeks($this));
	}
			public function add($nummer){
		$this->setEinde($nummer->getBusnummer());
	}
		public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBusnummer($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

class KVDUtil_HnrBusletterReeks extends KVDUtil_HnrReeksElement{
	private $huis;
	
	public function __construct($huis, $begin, $einde){
		parent::__construct($begin,$einde);
		$this->huis = $huis;
	}
	public function __toString(){
		return $this->huis." bus ".$this->begin."-".$this->einde;
	}	
		public function getHuisnummer(){
		return $this->huis;
	}
	
	public function setHuisnummer($huis){
		$this->huis = $huis;
	}
		public function accept($visitor){
		return $visitor->visitBusletterReeks($this);
	}
		public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBusletterReeks($this));
	}
		public function precedes($nummer){
		return $nummer->accept(new KVDUtil_HnrPrecedesToBusletterReeks($this));
	}
		public function compatibleTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompatibleToBusletterReeks($this));
	}
			public function add($nummer){
		$this->setEinde($nummer->getBusletter());
	}
		public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBusletter($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

class KVDUtil_HnrBisletterReeks extends KVDUtil_HnrReeksElement{
	private $huis;

	public function __construct($huis, $begin, $einde){
		parent::__construct($begin,$einde);
		$this->huis = $huis;
	}

	public function __toString(){
		return $this->huis.$this->begin."-".$this->einde;
	}	
	public function getHuisnummer(){
		return $this->huis;
	}
	
	public function setHuisnummer($huis){
		$this->huis = $huis;
	}
		public function accept($visitor){
		return $visitor->visitBisletterReeks($this);
	}
		public function compareTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompareToBisletterReeks($this));
	}
	
	public function precedes($nummer){
		return $nummer->accept(new KVDUtil_HnrPrecedesToBisletterReeks($this));
	}
		public function compatibleTo($nummer){
		return $nummer->accept(new KVDUtil_HnrCompatibleToBisletterReeks($this));
	}
			public function add($nummer){
		$this->setEinde($nummer->getBisletter());
	}
		public function split(){
		$r = array();
		for($i = $this->begin; $i<= $this->einde; $i++){
			$r[] = new KVDUtil_HnrBisletter($this->getHuisnummer(), $i);
		}
		return $r;
	}
}

/********************************************************************************/



abstract class  KVDUtil_HnrVisitor{
	abstract function visitHuisnummer($huis);
	abstract function visitBisnummer($bis);
	abstract function visitBusnummer($bus);
	abstract function visitBusletter($bus);
	abstract function visitBisletter($bis);
	
	abstract function visitHuisnummerReeks($huis);
	abstract function visitBisnummerReeks($bis);
	abstract function visitBusnummerReeks($bus);
	abstract function visitBusletterReeks($bus);
	abstract function visitBisletterReeks($bis);
}
abstract class KVDUtil_HnrCompare extends KVDUtil_HnrVisitor{
	public function compareOne($a,$b){
		if($a == $b) {return 0;}
		else if($a > $b) {return 1;}
		else {return -1;}
	}
	public function compareTwo($a1, $a2, $b1, $b2){
		$c = $this->compareOne($a1,$b1) ;
		if($c == 0) return $this->compareOne($a2,$b2);
		else return $c;
	}
	public function compareThree($a1, $a2, $a3, $b1, $b2, $b3){
		$c = $this->compareTwo($a1,$a2,$b1, $b2) ;
		if($c == 0) return $this->compareOne($a3,$b3);
		else return $c;
	}
}


class KVDUtil_HnrCompareToHuisnummer extends KVDUtil_HnrCompare{
	protected $huis;
	public function __construct($huis){
		$this->huis = $huis;
	}
	
	public function visitHuisnummer($huis) {
		return $this->compareOne($this->huis->getNummer(),$huis->getNummer());
	}
	public function visitBisnummer($bis){
		$c = $this->compareOne($this->huis->getNummer(),$bis->getNummer());
		if($c == 0) return -1;
		else return $c;
	}
	public function visitBusnummer($bus){
		$c = $this->compareOne($this->huis->getNummer(),$bus->getNummer());
		if($c == 0) return -1;
		else return $c;
	}
	public function visitBusletter($bus){
		$c = $this->compareOne($this->huis->getNummer(),$bus->getNummer());
		if($c == 0) return -1;
		else return $c;
	}

	public function visitBisletter($bis){
		$c = $this->compareOne($this->huis->getNummer(),$bis->getNummer());
		if($c == 0) return -1;
		else return $c;
	}
	public function visitHuisnummerReeks($reeks){
		return $this->compareOne($this->huis->getNummer(),$reeks->getBegin());
	}
	public function visitBisnummerReeks($reeks){
		return $this->compareOne($this->huis->getNummer(),$reeks->getHuisnummer());
	}
	public function visitBusnummerReeks($reeks){
		return $this->compareOne($this->huis->getNummer(),$reeks->getHuisnummer());
	}
	public function visitBusletterReeks($reeks){
		return $this->compareOne($this->huis->getNummer(),$reeks->getHuisnummer());
	}
	public function visitBisletterReeks($reeks){
		return $this->compareOne($this->huis->getNummer(),$reeks->getHuisnummer());
	}
}

class KVDUtil_HnrCompareToBisnummer extends KVDUtil_HnrCompareToHuisnummer{
	public function visitHuisnummer($huis) {
		$c = $this->compareOne($this->huis->getNummer(),$huis->getNummer());
		if($c == 0) return 1;
		else return $c;
	}
	public function visitBisnummer($bis){ 
		return $this->compareTwo($this->huis->getHuisnummer(),$this->huis->getBisnummer(),	
															$bis->getHuisnummer(),	$bis->getBisnummer());
	}
	public function visitBisnummerReeks($reeks){
		return $this->compareTwo($this->huis->getNummer(),$this->huis->getBisnummer(),	
															$reeks->getHuisnummer(),	$bis->getBegin());
	}
}
class KVDUtil_HnrCompareToBusnummer extends KVDUtil_HnrCompareToHuisnummer{
	public function visitBusnummer($bis){ 
		return $this->compareTwo($this->huis->getNummer(),$this->huis->getBusnummer(),	
															$bis->getNummer(),	$bis->getBusnummer());
	}
	public function visitBusnummerReeks($reeks){
		return $this->compareTwo($this->huis->getNummer(),$this->huis->getBusnummer(),	
															$reeks->getHuisnummer(),	$bis->getBegin());
	}
}

class KVDUtil_HnrCompareToBusletter extends KVDUtil_HnrCompareToHuisnummer{
	public function visitBusletter($bus){ 
		return $this->compareTwo($this->huis->getNummer(),$this->huis->getBusletter(),	
															$bis->getNummer(),	$bis->getBusletter());
	}
	public function visitBusletterReeks($reeks){
		return $this->compareTwo($this->huis->getNummer(),$this->huis->getBusletter(),	
															$reeks->getHuisnummer(),	$bis->getBegin());
	}
}
class KVDUtil_HnrCompareToBisletter extends KVDUtil_HnrCompareToHuisnummer{
	public function visitBisletter($bis){ 
		return $this->compareTwo($this->huis->getNummer(),$this->huis->getBisletter(),	
															$bis->getNummer(),	$bis->getBisletter());
	}
	public function visitBisletterReeks($reeks){
		return $this->compareTwo($this->huis->getNummer(),$this->huis->getBisletter(),	
															$reeks->getHuisnummer(),	$bis->getBegin());
	}
}



class KVDUtil_HnrCompareToHuisnummerReeks extends KVDUtil_HnrCompare{
	protected $huis;
	public function __construct($huis){
		$this->huis = $huis;
	}
	public function visitHuisnummer($huis){
		return $this->compareOne($this->huis->getBegin(), $huis->getNummer());
	}
	public function visitBisnummer($bis){
		return $this->compareOne($this->huis->getBegin(), $bis->getNummer());
	}
	public function visitBusnummer($bus){
		return $this->compareOne($this->huis->getBegin(), $bus->getNummer());
	}
	public function visitBusletter($bus){
		return $this->compareOne($this->huis->getBegin(), $bus->getNummer());
	}
	public function visitBisletter($bis){
		return $this->compareOne($this->huis->getBegin(), $bis->getNummer());
	}
	public function visitHuisnummerReeks($huis){
		return $this->compareTwo($this->huis->getBegin(), $this->huis->getEinde(),
			$huis->getBegin(), $huis->getEinde());
	}
	public function visitBisnummerReeks($bis){
		return $this->compareOne($this->huis->getBegin(), $bis->getHuisnummer());
	}
	public function visitBusnummerReeks($bus){
		return $this->compareOne($this->huis->getBegin(), $bus->getHuisnummer());
	}
	public function visitBusletterReeks($bus){
		return $this->compareOne($this->huis->getBegin(), $bus->getHuisnummer());
	}
	public function visitBisletterReeks($bis){
		return $this->compareOne($this->huis->getBegin(), $bis->getHuisnummer());
	}
}

class KVDUtil_HnrCompareToBisnummerReeks extends KVDUtil_HnrCompare{
	protected $huis;
	public function __construct($huis){
		$this->huis = $huis;
	}
	public function visitHuisnummer($huis){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getNummer());
	}
	public function visitBisnummer($bis){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getNummer());
	}
	public function visitBusnummer($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getNummer());
	}
	public function visitBusletter($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getNummer());
	}
	public function visitBisletter($bis){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getNummer());
	}
	
	public function visitHuisnummerReeks($huis){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getBegin());
	}
	public function visitBisnummerReeks($bis){
		return $this->compareThree(
						$this->huis->getHuisnummer(), 
						$this->huis->getBegin(), 
						$this->huis->getEinde(), 
						$huis->getHuisnummer(), 
						$huis->getBegin(), 
						$huis->getEinde() 
						);
	}
	public function visitBusnummerReeks($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $bus->getHuisnummer());
	}
	public function visitBusletterReeks($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $bus->getHuisnummer());
	}
	public function visitBisletterReeks($bis){
		return $this->compareOne($this->huis->getHuisnummer(), $bis->getHuisnummer());
	}

}
class KVDUtil_HnrCompareToBusnummerReeks extends KVDUtil_HnrCompareToBisnummerReeks{
	public function visitBisnummerReeks($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $bus->getHuisnummer());
	}
	public function visitBusnummerReeks($bis){
		return $this->compareThree(
						$this->huis->getHuisnummer(), 
						$this->huis->getBegin(), 
						$this->huis->getEinde(), 
						$bis->getHuisnummer(), 
						$bis->getBegin(), 
						$bis->getEinde() 
						);
	}
}

class KVDUtil_HnrCompareToBusletterReeks extends KVDUtil_HnrCompareToBisnummerReeks{
	public function visitBisnummerReeks($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getHuisnummer());
	}
	public function visitBusletterReeks($bus){
		return $this->compareThree(
						$this->huis->getHuisnummer(), 
						$this->huis->getBegin(), 
						$this->huis->getEinde(), 
						$bus->getHuisnummer(), 
						$bus->getBegin(), 
						$bus->getEinde() 
						);
	}
}

class KVDUtil_HnrCompareToBisletterReeks extends KVDUtil_HnrCompareToBisnummerReeks{
	public function visitBisnummerReeks($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $bus->getHuisnummer());
	}
	public function visitBisletterReeks($bis){
		return $this->compareThree(
						$this->huis->getHuisnummer(), 
						$this->huis->getBegin(), 
						$this->huis->getEinde(), 
						$bis->getHuisnummer(), 
						$bis->getBegin(), 
						$bis->getEinde() 
						);
	}
}

class KVDUtil_HnrCompareHuisnummers extends KVDUtil_HnrVisitor{
	private $nummer;
	public function __construct($nummer){
		$this->nummer = $nummer;
	}
	public function visitHuisnummer($huis){ 
		return $this->nummer->accept(new KVDUtil_HnrCompareToHuisnummer($huis));
	}
	public function visitBisnummer($bis){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBisnummer($bis));
	}
	public function visitBusnummer($bus){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBusnummer($bus));
	}
	public function visitBusletter($bus){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBusletter($bus));
	}
	public function visitBisletter($bis){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBisletter($bis));
	}
	public function visitHuisnummerReeks($huis){
		return $this->nummer->accept(new KVDUtil_HnrCompareToHuisnummerReeks($huis));
	}
	public function visitBisnummerReeks($bis){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBisnummerReeks($bis));
	}
	public function visitBusnummerReeks($bus){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBusnummerReeks($bus));
	}
	public function visitBusletterReeks($bus){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBusletterReeks($bus));
	}
	public function visitBisletterReeks($bis){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBisletterReeks($bis));
	}
}

/**************************************************************/

class HuisnummerReader{
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
			$this->type = HuisnummerReader::bus;
		else $this->type = HuisnummerReader::word;
		return $this->type;
	}
	
	public function next(){
		$this->pos++;
		if($this->pos >= $this->size) $this->type = HuisnummerReader::eof;
		else if(preg_match('/[\D]+/', $this->getContent()) == 0) $this->type = HuisnummerReader::nummer;
		else if(preg_match('/[\W]+/', $this->getContent()) == 0) $this->readBus();
		else if($this->getContent() == "/") $this->type = HuisnummerReader::slash;
		else if($this->getContent() == ",") $this->type = HuisnummerReader::comma;
		else if($this->getContent() == "-") $this->type = HuisnummerReader::min;
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


class HuisnummerParser{
	private $reader;
	
	public function __construct(){
		$this->reader = new HuisnummerReader();
	}

	private function readElement($type){
		if($this->reader->next() == $type) return $this->reader->getContent();
		else return $this->errorCode($this->reader->getType(),$type);
	}

	private function readHuisnummerReeks($begin){
		$einde = $this->readElement(HuisnummerReader::nummer);
		$this->reader->next();
		$spring = ((($begin - $einde)/2) == floor(($begin - $einde)/2));
		return new KVDUtil_HnrHuisnummerReeks($begin, $einde, $spring );	
	}
	
	private function readBisnummerReeks($huis, $begin){
		$einde = $this->readElement(HuisnummerReader::nummer);
		$this->reader->next();
		return new KVDUtil_HnrBisnummerReeks($huis, $begin, $einde);
	}
	
	private function readBisletterReeks($huis, $begin){
		$einde = $this->readElement(HuisnummerReader::word);
		$this->reader->next();
		return new KVDUtil_HnrBisletterReeks($huis, $begin, $einde);
	
	}
	
	private function readBusnummerReeks($huis, $begin){
		$einde = $this->readElement(HuisnummerReader::nummer);
		$this->reader->next();
		return new KVDUtil_HnrBusnummerReeks($huis, $begin, $einde);
	
	}
	private function readBusletterReeks($huis, $begin){
		$einde = $this->readElement(HuisnummerReader::word);
		$this->reader->next();
		return new KVDUtil_HnrBusnummerReeks($huis, $begin, $einde);
	}
	
	private function readBisnummer($huisnr){
		$bisnr = $this->readElement(HuisnummerReader::nummer);
		if($this->reader->next() == HuisnummerReader::min)
			return $this->readBisnummerReeks($huisnr, $bisnr);
		else return new KVDUtil_HnrBisnummer($huisnr, $bisnr);	
	
	}
	private function readBisletter($huis, $bis){
		if($this->reader->next() == HuisnummerReader::min) 
			return $this->readBisletterReeks($huis, $bis);
		else return new KVDUtil_HnrBisletter($huis, $bis);	
	
	}
	private function readBusnummer($huis, $nummer){
		if($this->reader->next() == HuisnummerReader::min)
			return $this->readBusnummerReeks($huis, $nummer);
		else return new KVDUtil_HnrBusnummer($huis, $nummer);
	}
	private function readBusletter($huis, $letter){
		if($this->reader->next() == HuisnummerReader::min)
			return $this->readBusletterReeks($huis, $letter);
		else return new KVDUtil_HnrBusnummer($huis, $letter);
	}
	
	private function readBus($huis){
		$type = $this->reader->next();
		$bus = $this->reader->getContent();
		switch($type) {
			case HuisnummerReader::nummer: return $this->readBusnummer($huis, $bus);
			case HuisnummerReader::word: return $this->readBusletter($huis, $bus);
			default: return $this->error("Word or Number Expected, given ".$this->reader->typeText($type));
		}
	}
	
	private function readHuisnummerLijst($exp){
		$lijst = array();
		$lijst[] = $exp;
		while($this->reader->getType() == HuisnummerReader::comma) {
			$nummer = $this->readElement(HuisnummerReader::nummer);
			$lijst[] = $this->readHuisnummer($nummer);
		}
		if($this->reader->getType() == HuisnummerReader::eof) return $lijst;
		else return $this->error("Invalid separator, comma expected");
	}

	private function readHuisnummer($exp){
		switch($this->reader->next()) {
			case HuisnummerReader::min: return $this->readHuisnummerReeks($exp);
			case HuisnummerReader::slash: return $this->readBisnummer($exp);
			case HuisnummerReader::bus: return $this->readBus($exp);
			case HuisnummerReader::word: return $this->readBisletter($exp, $this->reader->getContent());
			default: return new KVDUtil_HnrHuisnummer($exp);
		}
	}
	
	private function readHuisnummerExpressie(){
		$nummer = $this->readElement(HuisnummerReader::nummer);
		$huisnummer = $this->readHuisnummer($nummer);
		switch($this->reader->getType()) {
			case HuisnummerReader::comma: return $this->readHuisnummerLijst($huisnummer);break;
			case HuisnummerReader::eof: return array($huisnummer);break;
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

class SequenceReader{
	

	private $input;
	private $pos;
	
	private $result;

	public function __construct(){

	}
	private function readSpringReeks($reeks){
		while(($this->next() == "KVDUtil_HnrHuisnummer")&&($this->content()->getNummer() == ($reeks->getEinde() +2)))
			$reeks->setEinde($reeks->getEinde() +2);
		return $reeks;
	}
	private function readVolgReeks($reeks){
		while(($this->next() == "KVDUtil_HnrHuisnummer")&&($this->content()->getNummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($reeks->getEinde() +1);
		return $reeks;
	}
	
	private function readHuisnummerReeks($huisnummer){
		$reeks = new KVDUtil_HnrHuisnummerReeks($huisnummer->getNummer(),$huisnummer->getNummer());
		if($this->next() != "KVDUtil_HnrHuisnummer") return $huisnummer;
		$nummer = $this->content()->getNummer();
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


/***********************************************/

class KVDutil_HuisnummerFacade {


	public $compares;

	private $parser;
	private $sequencer;
	
	public function __construct(){
		$this->parser = new HuisnummerParser();
		$this->sequencer = new SequenceReader();
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