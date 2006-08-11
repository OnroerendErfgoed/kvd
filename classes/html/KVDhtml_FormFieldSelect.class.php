<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_FormFieldSelect.class.php,v 1.1 2006/01/12 12:30:14 Koen Exp $
 */

 /**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
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
        foreach ($this->options as $value => $text) {
            $selected = ($value == $this->value) ? ' selected="selected"' : '';
            $select .= sprintf($this->optionFormat, $value, $selected, $text);
        }
        $select .= "</select>\n";
        return $select;
    }
}
?>
