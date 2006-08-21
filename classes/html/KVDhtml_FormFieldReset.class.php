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
class KVDhtml_FormFieldReset extends KVDhtml_FormField
{
    /**
     * @return string
     */
    public function toHtml()
    {
        $this->toHtmlAttribValue();
        $this->toHtmlAttribClass();
        $this->toHtmlAttribDisabled();
        $this->toHtmlAttribSize( );
     
        return sprintf($this->fieldFormat, 'reset', $this->name, $this->id, $this->value, $this->class, '', $this->disabled,$this->size,'');
    }
}
?>
