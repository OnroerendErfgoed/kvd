<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since juni 2006
 */
class KVDhtml_FormFieldFile extends KVDhtml_FormField
{
    /**
     * Fieldoptions is een array die bepaalt hoe het item gerenderd moet worden. Mogelijk opties zijn:
     * <ul>
     *  <li><b>name</b>: name attribuut van een input element.</li>
     *  <li><b>class</b>: een css class.</li>
     *  <li><b>value</b>: een eventuele startwaarde, dit moet dus een bestandsnaam zijn.</li>
     *  <li><b>disabled</b>: Boolean die bepaalt of het input element al dan niet moet ingeschakeld zijn.</li>
     *  <li><b>readonly</b>: Boolean die bepaalt of het input element al dan niet readonly is.</li>
     * </ul> 
     * @param array $fieldOptions
     */
    public function __construct( $fieldOptions )
    {
        parent::__construct( $fieldOptions );
    }
    
    /**
     * @return string Html weergave van het input element.
     */
    public function toHtml()
    {
        $this->toHtmlAttribValue();
        $this->toHtmlAttribClass();
        $this->toHtmlAttribDisabled();
        $this->toHtmlAttribReadonly();
        $this->toHtmlAttribSize( );
     
        return sprintf($this->fieldFormat, 'file', $this->name, $this->id, $this->value, $this->class, $this->readonly, $this->disabled,$this->size,'');
    }
}
?>
