<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_FormFieldTextArea.class.php,v 1.1 2006/01/12 12:30:14 Koen Exp $
 */

 /**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_FormFieldTextarea extends KVDhtml_FormField
{
    /**
     * @var string
     */
    protected $fieldFormat = "<textarea name=\"%s\"%s%s%s>\n%s\n</textarea>\n";
    
    /**
     * @return string
     */
    public function toHtml()
    {
        $this->toHtmlAttribClass();
        $this->toHtmlAttribReadonly();
        $this->toHtmlAttribDisabled();
     
        return sprintf($this->fieldFormat, $this->name, $this->class, $this->readonly, $this->disabled, $this->value);
    }
}
?>
