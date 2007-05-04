<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */


/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 4 april 2007
 */
class KVDhtml_FormFieldDate extends KVDhtml_FormField
{
    /**
     * startDate 
     * 
     * @var string
     */
    protected $startDate;

    /**
     * endDate 
     * 
     * @var string
     */
    protected $endDate;
    
    /**
     * @var string 
     */
    protected $fieldFormat = "<input dojoType=\"%s\" name=\"%s\" id=\"%s\"%s%s%s%s%s%s widgetId=\"%s\" />\n";

    /**
     * setField 
     * 
     * @param array $fieldOptions Extra opties zijn startDate en endDate
     * @return void
     */
    protected function setField(&$fieldOptions)
    {
        parent::setField( $fieldOptions );
        $this->value = isset( $fieldOptions['value'] ) ? $this->cleanDate( $fieldOptions['value'] ) : null;
        $this->startDate = isset( $fieldOptions['startDate'] ) ? $this->cleanDate( $fieldOptions['startDate'] ) : null;
        $this->endDate = isset( $fieldOptions['endDate'] ) ? $this->cleanDate( $fieldOptions['endDate'] ) : null;
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

        $startDate = ( $this->startDate === null ) ? '' : " startDate=\"$this->startDate\"";
        $endDate = ( $this->endDate === null ) ? '' : " endDate=\"$this->endDate\"";
     
        return sprintf($this->fieldFormat, 'dropdowndatepicker', $this->name, $this->id, $this->value, $this->class, $this->readonly, $this->disabled,$startDate,$endDate,$this->id);
    }

    /**
     * cleanDate 
     * 
     * @param mixed $date Integer ( timestamp ) of string
     * @return Datum geformatteerd in RFC3339 formaat
     */
    private function cleanDate( $date )
    {
        if ( !is_numeric( $date ) ) {
            $date = strtotime( $date );
        }
        return date( 'Y-m-d' , $date );
    }
}
?>
