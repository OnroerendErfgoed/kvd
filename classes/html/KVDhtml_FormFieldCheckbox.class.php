<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_FormFieldCheckbox.class.php,v 1.1 2006/01/12 12:30:14 Koen Exp $
 */

 /**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_FormFieldCheckbox extends KVDhtml_FormField
{
    
    protected function toHtmlAttribValue()
    {
        if ($this->value != '' && $this->value == true) {
            $this->value = " checked";
        }
    }
    
    /**
     * @return string
     */
    public function toHtml()
    {
        $this->toHtmlAttribValue();
        $this->toHtmlAttribClass();
        $this->toHtmlAttribReadonly();
        $this->toHtmlAttribDisabled();
     
        return sprintf($this->fieldFormat, 'checkbox', $this->name, $this->value, $this->class, $this->readonly, $this->disabled);
    }
}
?>
