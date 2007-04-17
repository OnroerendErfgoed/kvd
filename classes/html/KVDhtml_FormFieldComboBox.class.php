<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */


/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 13 april 2007
 */
class KVDhtml_FormFieldComboBox extends KVDhtml_FormField
{
    
    /**
     * @var string 
     */
    protected $fieldFormat = "<input dojoType=\"ComboBox\" autoComplete=\"%s\" dataUrl=\"%s\" maxListLength=\"%d\" mode=\"remote\" name=\"%s\" id=\"%s\"%s%s%s%s/>\n";

    /**
     * setField 
     * 
     * @param array $fieldOptions Extra opties zijn startDate en endDate
     * @return void
     */
    protected function setField(&$fieldOptions)
    {
        parent::setField( $fieldOptions );
        $this->value = isset( $fieldOptions['value'] ) ? $fieldOptions['value'] : null;
        $this->autoComplete = isset( $fieldOptions['autoComplete'] ) ? $fieldOptions['autoComplete'] : true;
        if ( !isset( $fieldOptions['dataUrl'] ) ) {
            throw new InvalidArgumentException ( 'U moet een dataUrl opgeven voor een ComboBox.' );
        }
        $this->dataUrl = $fieldOptions['dataUrl'];
        $this->maxListLength = isset( $fieldOptions['maxListLength'] ) ? $fieldOptions['maxListLength'] : '20';
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

        return sprintf($this->fieldFormat, $this->autoComplete , $this->dataUrl, $this->maxListLength, $this->name, $this->id, $this->value, $this->class, $this->readonly, $this->disabled);
    }

}
?>
