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
    
    /**
     * @return string
     */
    public function toHtml()
    {
        $this->toHtmlAttribValue();
        $this->toHtmlAttribClass();
        $this->toHtmlAttribDisabled();
        $this->toHtmlAttribChecked( );
        $this->toHtmlAttribSize( );
     
        return sprintf($this->fieldFormat, 'checkbox', $this->name, $this->id, $this->value, $this->class, '', $this->disabled, $this->checked, $this->size);
    }
}
?>
