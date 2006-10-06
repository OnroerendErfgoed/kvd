<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_FormFieldPassword extends KVDhtml_FormField
{
    /**
     * @return string
     */
    public function toHtml()
    {
        $this->toHtmlAttribValue();
        $this->toHtmlAttribClass();
        $this->toHtmlAttribReadonly();
        $this->toHtmlAttribDisabled();
        $this->toHtmlAttribSize( );
     
        return sprintf($this->fieldFormat, 'password', $this->name, $this->id, $this->value, $this->class, $this->readonly, $this->disabled,$this->size,'');
        
    }
}
?>
