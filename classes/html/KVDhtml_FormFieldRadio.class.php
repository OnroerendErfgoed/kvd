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
class KVDhtml_FormFieldRadio extends KVDhtml_FormField
{
    /**
     * @var string
     */
    protected $fieldFormat = "<input type=\"radio\" name=\"%s\" id=\"%s\"%s%s%s%s%s/>%s\n";

    /**
     * @var string
     */
    protected $legend;

    protected function setField( &$fieldOptions )
    {
        parent::setField( $fieldOptions );
        $this->legend = isset( $fieldOptions['legend'] )  ? $fieldOptions['legend'] : '';
    }
    
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
     
        return sprintf($this->fieldFormat, $this->name, $this->value, $this->id, $this->class, $this->disabled, $this->checked, $this->size,$this->legend);
    }
}
?>
