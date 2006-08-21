<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_FormFieldHidden.class.php,v 1.1 2006/01/12 12:30:14 Koen Exp $
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDhtml_FormFieldHidden extends KVDhtml_FormField
{
    /**
     * @return string
     */
    public function toHtml()
    {
        $this->toHtmlAttribValue();
        
        return sprintf($this->fieldFormat, 'hidden', $this->name, $this->id, $this->value, '', '', '','','');
    }
}
?>
