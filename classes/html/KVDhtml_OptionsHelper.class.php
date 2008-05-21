<?php
/**
 * KVDhtml_OptionsHelper 
 * 
 * @package KVD.html
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

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
class KVDhtml_OptionsHelper
{
    /**
     * collection 
     * 
     * @var KVDdom_DomainObjectCollection
     */
    private $collection;

    /**
     * addEmptyLine 
     * 
     * @var boolean
     */
    private $addEmptyLine;

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
     * selectedValue 
     * 
     * @var mixed Zal meestal integer of string zijn.
     */
    private $selectedValue;

    /**
     * emptyId 
     * 
     * @var mixed Zal meestal integer of string zijn.
     */
    private $emptyId = null;

    /**
     * emptyValue 
     * 
     * @var string
     */
    private $emptyValue = '';

    /**
     * @var string 
     */
    private static $optionFormat = "<option value=\"%s\"%s>%s</option>\n";
    
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
        foreach ( $this->collection as $item ) {
            $buffer .= $this->generateOption( $this->getValue( $item ) , $this->getOmschrijving( $item ) , $this->selectedValue === $this->getValue( $item ) );
        }
        return $buffer;
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
     * generateOption 
     * 
     * @param mixed $value 
     * @param string $omschrijving 
     * @param mixed $selected 
     * @return string
     */
    private function generateOption( $value , $omschrijving , $selected = false )
    {
        $selected = ( $selected ) ? ' selected="selected"' : '';
        return sprintf( self::$optionFormat , KVDhtml_Tools::out( $value ) , $selected , KVDhtml_Tools::out( $omschrijving ) );
    }
}
?>
