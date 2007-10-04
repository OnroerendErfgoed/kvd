<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

 /**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDhtml_FormFieldSelect extends KVDhtml_FormField
{
    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $multiple;

    /**
     * empty 
     * 
     * @var boolean
     */
    private $empty;

    /**
     * emptyValue 
     * 
     * @var string
     */
    private $emptyValue;

    /**
     * emptyKey 
     * 
     * @var mixed
     */
    private $emptyKey;

    /**
     * @var string
     */
    protected $fieldFormat = "<select name=\"%s\" id=\"%s\"%s%s%s%s>\n";

    /**
     * @var string
     */
    private $optionFormat = "  <option value=\"%s\"%s>%s</option>\n";
        
    /**
     * @param array $fieldOptions
     */
    protected function setField(&$fieldOptions)
    {
        parent::setField($fieldOptions);
        $this->options = (isset($fieldOptions['options'])) ? $fieldOptions['options'] : array( );
        $this->multiple = (isset($fieldOptions['multiple'])) ? $fieldOptions['multiple'] : '';
        $this->empty = (isset($fieldOptions['empty']) && $fieldOptions['empty'] == true ) ? true : false;
        $this->emptyKey = (isset($fieldOptions['emptyKey'])) ? $fieldOptions['emptyKey'] : '';
        $this->emptyValue = (isset($fieldOptions['emptyValue'])) ? $fieldOptions['emptyValue'] : '';
    }

    private function toHtmlAttribMultiple()
    {
        if ($this->multiple) {
            $this->multiple = ' multiple="multiple"';
        }
    }

    protected function toHtmlAttribValue()
    {
        if ($this->value === '') {
            $this->value = -1;
        }
    }
    
    /**
     * @return $string
     */
    public function toHtml()
    {
        $this->toHtmlAttribClass();
        $this->tohtmlAttribMultiple();
        $this->toHtmlAttribValue();
        $this->toHtmlAttribDisabled();
        $this->toHtmlAttribSize( );

        if ($this->multiple && substr($this->name, -2) != "[]") {
            $this->name .= "[]";
        }
        
        $select = sprintf($this->fieldFormat,$this->name,$this->id, $this->class, $this->multiple, $this->disabled,$this->size);
        $select .= $this->getEmptyOption( );
        foreach ($this->options as $value => $text) {
            $selected = ($value == $this->value) ? ' selected="selected"' : '';
            $select .= sprintf($this->optionFormat, $value, $selected, $text);
        }
        $select .= "</select>\n";
        return $select;
    }

    /**
     * getEmptyOption 
     * 
     * @return string
     */
    private function getEmptyOption( )
    {
        if ( $this->empty == true ) {
            return sprintf( $this->optionFormat, $this->emptyKey, '', $this->emptyValue );
        }
        return '';
    }
}
?>
