
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
	
}




class KVDUtil_HnrHuisnummer extends KVDUtil_HnrEnkelElement{

	public function __construct($nummer){
		parent::__construct($nummer);
	}

	public function __toString(){
		return $this->nummer;
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
		return $bis;
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
	
	public function __toString(){
		return $this->begin.'-'.$this->einde;
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
	public function __construct($begin, $einde){
		parent::__construct($begin,$einde);
	}
	public function isEven(){
		return false;
	}
	
	public function isOneven(){
		return false;
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
}

class KVDUtil_HnrBisletterReeks extends KVDUtil_HnrReeksElement{
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
}

/********************************************************************************/



abstract class  KVDUtil_HnrVisitor{
	abstract function visitHuisnummer($huis);
	abstract function visitBisnummer($bis);
	abstract function visitBusnummer($bus);
	abstract function visitBisletter($bis);
	
	abstract function visitHuisnummerReeks($huis);
	abstract function visitBisnummerReeks($bis);
	abstract function visitBusnummerReeks($bus);
	abstract function visitBisletterReeks($bis);
}
abstract class KVDUtil_HnrCompare extends KVDUtil_HnrVisitor{
	public function compareOne($a,$b){
		if($a == $b) {echo "$a == $b \n"; return 0;}
		else if($a > $b) {echo "$a > $b \n"; return 1;}
		else {echo "$a < $b \n"; return -1;}
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
	private $huis;
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
	public function visitBisletterReeks($bis){
		return $this->compareOne($this->huis->getBegin(), $bis->getHuisnummer());
	}
}
class KVDUtil_HnrCompareToBisnummerReeks extends KVDUtil_HnrCompare{
	private $huis;
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
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getHuisnummer());
	}
	public function visitBisletterReeks($bis){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getHuisnummer());
	}

}
class KVDUtil_HnrCompareToBusnummerReeks extends KVDUtil_HnrCompareToBisnummerReeks{
	public function visitBisnummerReeks($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getHuisnummer());
	}
	public function visitBusnummerReeks($bis){
		return $this->compareThree(
						$this->huis->getHuisnummer(), 
						$this->huis->getBegin(), 
						$this->huis->getEinde(), 
						$huis->getHuisnummer(), 
						$huis->getBegin(), 
						$huis->getEinde() 
						);
	}
}

class KVDUtil_HnrCompareToBisletterReeks extends KVDUtil_HnrCompareToBisnummerReeks{
	public function visitBisnummerReeks($bus){
		return $this->compareOne($this->huis->getHuisnummer(), $huis->getHuisnummer());
	}
	public function visitBisletterReeks($bis){
		return $this->compareThree(
						$this->huis->getHuisnummer(), 
						$this->huis->getBegin(), 
						$this->huis->getEinde(), 
						$huis->getHuisnummer(), 
						$huis->getBegin(), 
						$huis->getEinde() 
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
	public function visitBisletterReeks($bis){
		return $this->nummer->accept(new KVDUtil_HnrCompareToBisletterReeks($bis));
	}
}


class KVDUtil_HnrCompatible extends KVDUtil_HnrVisitor{
	public function visitHuisnummer($huis){ return false; }
	public function visitBisnummer($bis){ return false; }
	public function visitBusnummer($bus){ return false; }
	public function visitBisletter($bis){ return false; }
	
	public function visitHuisnummerReeks($huis){ return false; }
	public function visitBisnummerReeks($bis){ return false; }
	public function visitBusnummerReeks($bus){ return false; }
	public function visitBisletterReeks($bis){ return false; }
}


class KVDUtil_HnrCompatibleToHuisnummerReeks extends KVDUtil_HnrCompatible{
	public function visitHuisnummer($huis){ return true; }
	public function visitHuisnummerReeks($huis){ return true; }
}
class KVDUtil_HnrCompatibleToBisnummerReeks extends KVDUtil_HnrCompatible{
	public function visitBisnummer($huis){ return true; }
	public function visitBisnummerReeks($huis){ return true; }
}
class KVDUtil_HnrCompatibleToBusnummerReeks extends KVDUtil_HnrCompatible{
	public function visitBusnummer($huis){ return true; }
	public function visitBusnummerReeks($huis){ return true; }
}
class KVDUtil_HnrCompatibleToBisletterReeks extends KVDUtil_HnrCompatible{
	public function visitBisletter($huis){ return true; }
	public function visitBisleterReeks($huis){ return true; }
}


class KVDUtil_HnrPrecedes extends KVDUtil_HnrVisitor{
	public function visitHuisnummer($huis){ return false; }
	public function visitBisnummer($bis){ return false; }
	public function visitBusnummer($bus){ return false; }
	public function visitBisletter($bis){ return false; }
	
	public function visitHuisnummerReeks($huis){ return false; }
	public function visitBisnummerReeks($bis){ return false; }
	public function visitBusnummerReeks($bus){ return false; }
	public function visitBisletterReeks($bis){ return false; }
	
	private $alphabet = "abcdefghijklmnopqrstuvwxyz";

	public function letterPrecedes($a,$b){
	return ((stripos($this->alphabet, $a)+1) == stripos($this->alphabet, $b));
}
}

class KVDUtil_HnrPrecedesToHuisnummerReeks extends KVDUtil_HnrPrecedes{
	private $reeks;
	public function __construct($reeks){
		$this->reeks = $reeks;
	}
	public function visitHuisnummer($huis){ 
		return ($this->reeks->getEinde()+1 == $huis->getNummer());
	}
	public function visitHuisnummerReeks($huis){ 
		return ($this->reeks->getEinde()+1 == $huis->getBegin());
	}
}
class KVDUtil_HnrPrecedesToBisnummerReeks extends KVDUtil_HnrPrecedes{
	private $reeks;
	public function __construct($reeks){
		$this->reeks = $reeks;
	}
	public function visitBisnummer($huis){
		return ( ($this->reeks->getHuisnummer() == $huis->getHuisnummer()) &&
						($this->reeks->getEinde()+1 == $huis->getBisnummer()));
	}
	public function visitBisnummerReeks($huis){ 
		return ( ($this->reeks->getHuisnummer() == $huis->getHuisnummer()) &&
						($this->reeks->getEinde()+1 == $huis->getBegin()));
	}
}
class KVDUtil_HnrPrecedesToBusnummerReeks extends KVDUtil_HnrPrecedes{
	private $reeks;
	public function __construct($reeks){
		$this->reeks = $reeks;
	}
	public function visitBusnummer($huis){
		return ( ($this->reeks->getHuisnummer() == $huis->getHuisnummer()) &&
						($this->reeks->getEinde()+1 == $huis->getBusnummer()));
	}
	public function visitBusnummerReeks($huis){ 
		return ( ($this->reeks->getHuisnummer() == $huis->getHuisnummer()) &&
						($this->reeks->getEinde()+1 == $huis->getBegin()));
	}
}
class KVDUtil_HnrPrecedesToBisletterReeks extends KVDUtil_HnrPrecedes{
	private $reeks;
	public function __construct($reeks){
		$this->reeks = $reeks;
	}
	public function visitBisletter($huis){
		return ( ($this->reeks->getHuisnummer() == $huis->getHuisnummer()) &&
						($this->reeks->getEinde()+1 == $huis->getBisletter()));
	}
	public function visitBisletterReeks($huis){ 
		return ( ($this->reeks->getHuisnummer() == $huis->getHuisnummer()) &&
						($this->reeks->getEinde()+1 == $huis->getBegin()));
	}
}



class KVDUtil_HnrBuildReeks extends KVDUtil_HnrVisitor{
	public function visitHuisnummer($huis){ return new KVDUtil_HnrHuisnummerReeks($huis->getNummer(), $huis->getNummer()); }
	public function visitBisnummer($bis){ 
		return new KVDUtil_HnrBisnummerReeks($bis->getHuisnummer(), $bis->getBisnummer(), $bis->getBisnummer()); 
	}
	public function visitBusnummer($bus){ 
		return new KVDUtil_HnrBusnummerReeks($bus->getHuisnummer(), $bus->getBusnummer(), $bus->getBusnummer()); 
	}
	public function visitBisletter($bis){ 
		return new KVDUtil_HnrBisletterReeks($bis->getHuisnummer(), $bis->getBisletter(), $bis->getBisletter()); 
	}
	
	public function visitHuisnummerReeks($reeks){ return $reeks;}
	public function visitBisnummerReeks($reeks){ return $reeks;}
	public function visitBusnummerReeks($reeks){ return $reeks;}
	public function visitBisletterReeks($reeks){ return $reeks;}
}







?><?php


class HuisnummerReader{
	const nummer = 0;
	const min = 1;
	const slash = 2;
	const bus = 3;
	const eof = 4;
	const word = 5;
	const comma = 6;

	public function __construct(){
		
	}

	private $input = array();
	private $pos = -1;
	private $type = null;
	private $size = 0;
	
	public function setInput($input){
		$this->input = preg_split("/[\s]+|(,)|(-)|(\/)|((?i:bus))/", $input, -1, PREG_SPLIT_DELIM_CAPTURE+PREG_SPLIT_NO_EMPTY);
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
		else return $this->error($type." expected, given ".$this->reader->getType());
	}

	private function readHuisnummerReeks($begin){
		$einde = $this->readElement(HuisnummerReader::nummer);
		$this->reader->next();
		return new KVDUtil_HnrHuisnummerReeks($begin, $einde);	
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
		
	private function readBusnummer($huis){
		$bisnr = $this->readElement(HuisnummerReader::nummer);
		if($this->reader->next() == HuisnummerReader::min)
			return $this->readBusnummerReeks($huis, $bisnr);
		else return new KVDUtil_HnrBusnummer($huis, $bisnr);	
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
			case HuisnummerReader::bus: return $this->readBusnummer($exp);
			case HuisnummerReader::word: return $this->readBisletter($exp, $this->reader->getContent());
			default: return new KVDUtil_HnrHuisnummer($exp);
		}
	}
	
	private function readHuisnummerExpressie(){
		$nummer = $this->readElement(HuisnummerReader::nummer);
		$huisnummer = $this->readHuisnummer($nummer);
		switch($this->reader->getType()) {
			case HuisnummerReader::comma: return $this->readHuisnummerLijst($huisnummer);break;
			case HuisnummerReader::eof: return $huisnummer;break;
			default: $this->error("Separator expected");
		}
	}
	
	public function parse($input){
		$this->reader->setInput($input);
		 return $this->readHuisnummerExpressie();
	}
	
	private function error($msg){
		$m = "Parse error:".$msg.", at ".$this->reader->getContent()."(".$this->reader->getPos().")";
		throw new Exception($m);
	}
}

?>

<?php



class HuisnummerFacade {


	public $compares;

	private $parser;
	
	public function __construct(){
		$this->parser = new HuisnummerParser();
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



	public function accumulate($arr){
		$reeksen = array();
		$reeks = $arr[0]->accept(new KVDUtil_HnrBuildReeks());
		for($i=1; $i<sizeof($arr); $i++){
			if(($reeks->precedes($arr[$i])) && ($reeks->compatibleTo($arr[$i]))){
				$reeks->add($arr[$i]);
			} else {
				$reeksen[] = $reeks;
				$reeks = $arr[$i]->accept(new KVDUtil_HnrBuildReeks());
			}
		}
		$reeksen[] = $reeks;
		return $reeksen;
	}




	public function read($input){
		return $this->parser->parse($input);
	}
	
	public function sort(&$inputs){
		$this->QuickHuisSort($inputs, 0, sizeof($inputs));
	} 
}








class SequenceReader{
	

	private $input;
	private $pos;
	
	private $result;

	public function __construct(){

	}
	private function readSpringReeks($reeks){
		$c = $this->next();
		$content = $this->content();
		while(($c == "KVDUtil_HnrHuisnummer")&&($content->getHuisnummer() == ($reeks->getEinde() +2)))
			$reeks->setEinde($content->getHuisnummer());
	}
	private function readVolgReeks($reeks){
		$c = $this->next();
		$content = $this->content();
		while(($c == "KVDUtil_HnrHuisnummer")&&($content->getHuisnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($content->getHuisnummer());
	}
	
	private function readHuisnummerReeks($huisnummer){
		$reeks = new HuisnummerReeks($huisnummer->getNummer(),$huisnummer->getNummer());
		if($this->next() != "KVDUtil_HnrHuisnummer") return $reeks;
		$nummer = $this->content()->getNummer();
		if($nummer == ($reeks->getEinde()+1)) {
			$reeks->setEinde($nummer->getNummer());
			return readVolgReeks($reeks);
		}
		if ($nummer == ($reeks->getEinde()+2)) {
			$reeks->setEinde($nummer->getNummer());
			return readSpringReeks($reeks);
		}
		return $reeks;
	}
	
	private function readBisnummerReeks($bisnummer){

		$content = $this->content();
		$reeks = new KVDUtil_HnrBisnummerReeks($bisnummer->getHuisnummer(), $bisnummer->getBisnummer(), $bisnummer->getBisnummer());
		while(($this->next() == "KVDUtil_HnrBisnummer")&&($this->content->getBisnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($content->getBisnummer());
	}
	
	private function readBusnummerReeks($busnummer){
			$c = $this->next();
		$content = $this->content();
		$reeks = new KVDUtil_HnrBusnummerReeks($bisnummer->getHuisnummer(), $bisnummer->getBusnummer(), $bisnummer->getBusnummer());
		while(($this->next() == "KVDUtil_HnrBusnummer")&&($this->content->getBusnummer() == ($reeks->getEinde() +1)))
			$reeks->setEinde($content->getBusnummer());
	}
	
	private function readBisletterReeks($bisletter){
	
	}
	
	public function readReeks(){
		switch($this->current()) {
			case "KVDUtil_HnrHuisnummer": return readHuisnummerReeks($this->content());
			case "KVDUtil_HnrBisnummer": return readBisnummerReeks($this->content());
			case "KVDUtil_HnrBusnummer": return readBusnummerReeks($this->content());
			case "KVDUtil_HnrBisletter": return readBisletterReeks($this->content());
			case "": return null;
			default: throw new Exception("Invalid type");
		}
	}
	
	public function read($in){
		$this->input = $in;
		$this->pos = 0;
		$this->result = array();
		while($this->current != "") $this->store(readReeks());
		return $this->result();
	}
	
	private function next(){
		$this->pos++;
		return $this->current();
	}
	
	private function current(){
		if ($this-pos >= sizeof($this->input)) return "";
		else return get_class($this->input[$this->pos]);
	}
	
	private function content(){
		return $this->input[$this->pos];
	}
	
	private function store($content){
		$result[] = $content;
	}	
	


}





?>