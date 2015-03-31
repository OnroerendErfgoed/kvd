<?php
/**
 * KVDhtml_OptionsHelper
 *
 * @package     KVD.html
 * @copyright   2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDhtml_AbstractOptionsHelper
 *
 * Helper class die een collectie van opties neemt en er een html options list
 * ( voor een select veld in een form) van maakt.
 *
 * @package     KVD.html
 * @since       9 jan 2007
 * @copyright   2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
abstract class KVDhtml_AbstractOptionsHelper
{
	/**
	 * collection
	 * @var KVDdom_DomainObjectCollection
	 */
	protected $collection;

	/**
	 * addEmptyLine
	 * @var boolean
	 */
	protected $addEmptyLine;

	/**
	 * selectedValue
	 *
	 * @var mixed Zal meestal integer of string zijn.
	 */
	protected $selectedValue;

	/**
	 * emptyId
	 *
	 * @var mixed Zal meestal integer of string zijn.
	 */
	protected $emptyId = null;

	/**
	 * emptyValue
	 *
	 * @var string
	 */
	protected $emptyValue = '';

	/**
	 * @var string
	 */
	protected static $optionFormat = "<option value=\"%s\"%s>%s</option>\n";

	/**
	 * setEmptyValues
	 *
	 * Stel de waarden in voor de lege regel.
	 * @since 7 feb 2007
	 * @param string $id
	 * @param string $value
	 * @return void
	 */
	public function setEmptyValues( $id = '' , $value = '' )
	{
		$this->emptyId = $id;
		$this->emptyValue = $value;
	}

	/**
	 * toHtml
	 *
	 * @return string Html-weergave van de option-list
	 */
	public function toHtml( )
	{
		$buffer = '';
		if ( $this->addEmptyLine ) {
			$buffer .= $this->generateOption($this->emptyId , $this->emptyValue );
		}
		foreach ( $this->collection as $key => $item ) {
			$buffer .= $this->generateItem($key, $item);
		}
		return $buffer;
	}


	/**
	 * generateOption
	 *
	 * @param mixed $value
	 * @param string $omschrijving
	 * @param mixed $selected
	 * @return string
	 */
	protected function generateOption( $value , $omschrijving , $selected = false )
	{
		$selected = ( $selected ) ? ' selected="selected"' : '';
		return sprintf( self::$optionFormat , KVDhtml_Tools::out( $value ) , $selected , KVDhtml_Tools::out( $omschrijving ) );
	}

	/**
	 * generateItem
	 * 	vraagt aan de subclass om een html optie te genereren voor een item uit de collectie.
	 * @param mixed
	 * @param mixed
	 * @return string
	 */
	abstract protected function generateItem($key, $item);


	/**
	 * arrayOptionsHelper
	 *  Constructor functie voor een optionslijst o.b.v. een array
	 * @param array
	 * @param boolean
	 * @param string
	 */
	public static function arrayOptionsHelper($collection, $addEmptyLine = false, $selectedValue = null)
	{
		return new KVDhtml_OptionsHelperArray($collection, $addEmptyLine, $selectedValue);
	}

	/**
	 * arrayOptionsHelper
	 *  Constructor functie voor een optionslijst o.b.v. een array
	 * @param KVDdom_ObjectCollection
	 * @param boolean
	 * @param string
	 */
	public static function objectOptionsHelper($collection , $addEmptyLine = false, $valueField = 'Id' , $omschrijvingField = 'Omschrijving' , $selectedValue = null)
	{
		return new KVDhtml_OptionsHelper($collection, $addEmptyLine, $valueField, $omschrijvingField, $selectedValue);
	}
}


/**
 * KVDhtml_OptionsHelper
 *
 * Helper class die een collectie van {@link KVDdom_DomainObject} neemt en er een html options list
 * ( voor een select veld in een form) van maakt.
 * @package KVD.html
 * @since 9 jan 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDhtml_OptionsHelper extends KVDhtml_AbstractOptionsHelper
{

	/**
	 * valueField
	 *
	 * @var string
	 */
	private $valueField;

	/**
	 * omschrijvingField
	 *
	 * @var string
	 */
	private $omschrijvingField;


	/**
	 * __construct
	 *
	 * @param KVDdom_DomainObjectCollection $collection Een gewone array van domainobjecten zal ook werken
	 * @param boolean $addEmptyLine Moet er een lege regel bovenaan de lijst worden toegevoegd?
	 * @param string $valueField Het veld dat als index dient.
	 * @param string $omschrijvingField Het veld dat de omschrijving levert.
	 * @param mixed $selectedValue Indien aanwezig wordt deze option geselecteerd.
	 */
	public function __construct( $collection , $addEmptyLine = false, $valueField = 'Id' , $omschrijvingField = 'Omschrijving' , $selectedValue = null)
	{
		$this->collection = $collection;
		$this->addEmptyLine = $addEmptyLine;
		$this->valueField = $valueField;
		$this->omschrijvingField = $omschrijvingField;
		$this->selectedValue = $selectedValue;
	}

	/**
	 * getValue
	 *
	 * @param KVDdom_DomainObject $object
	 * @return mixed Meestal integer of string
	 */
	private function getValue( $object )
	{
		$method = 'get' . $this->valueField;
		return $object->$method( );
	}

	/**
	 * getOmschrijving
	 *
	 * @param KVDdom_DomainObject $object
	 * @return string
	 */
	private function getOmschrijving( $object )
	{
		$method = 'get' . $this->omschrijvingField;
		return $object->$method( );
	}

	/**
	 * generateItem
	 * 	genereert een html optie voor een object uit de collectie.
	 * @param mixed
	 * @param KVDdom_Object
	 * @return string
	 */
	protected function generateItem($key, $item)
	{
		return $this->generateOption(
			$this->getValue($item),
			$this->getOmschrijving( $item ),
			($this->selectedValue === $this->getValue( $item )));
	}

}



/**
 * KVDhtml_OptionsHelperArray
 *
 * Helper class die een collectie van strings neemt en er een html options list
 * ( voor een select veld in een form) van maakt.
 * @package     KVD.html
 * @since       9 jan 2007
 * @copyright   2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDhtml_OptionsHelperArray extends KVDhtml_AbstractOptionsHelper
{
    /**
     * __construct
     *
     * @param   array   $collection
     * @param   boolean $addEmptyLine
     * @param   string  $selectedValue
     * @return  void
     */
	public function __construct($collection, $addEmptyLine = false, $selectedValue = null)
	{
        $this->collection = $collection;
        $this->addEmptyLine = $addEmptyLine;
        $this->selectedValue = $selectedValue;
	}

	/**
	 * generateItem
	 * 	genereert een html optie voor een item uit de collectie.
	 * @param   string  $key    Sleutel van het element in de array
	 * @param   string  $item   Waarde van het element in de array
	 * @return  string  Het element omgezet in een html option tag.
	 */
	protected function generateItem($key, $item)
	{
		return $this->generateOption($key, $item, ($this->selectedValue === $key));
	}
}

?>
