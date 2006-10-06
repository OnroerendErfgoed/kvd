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
abstract class KVDhtml_FormField
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $disabled;
    
    /**
     * @var string
     */
    protected $readonly;

    /**
     * @var string
     */
    protected $checked;

    /**
     * @var string
     */
    protected $size;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string 
     */
    protected $fieldFormat = "<input type=\"%s\" name=\"%s\" id=\"%s\"%s%s%s%s%s%s/>\n";

    /**
     * @param array $fieldOptions
     */
    public function __construct( $fieldOptions=null )
    {
        if ( !is_null($fieldOptions) ) {
            $this->setField($fieldOptions);
        } else {
            $this->setField(array());
        }
    }

    /**
     * @param array $fieldOptions
     */
    protected function setField(&$fieldOptions)
    {
        $this->name = (isset($fieldOptions['name'])) ? $fieldOptions['name'] : '';
        $this->value = (isset($fieldOptions['value'])) ? $fieldOptions['value'] : '';
        $this->class = (isset($fieldOptions['class'])) ? $fieldOptions['class'] : '';
        $this->readonly = (isset($fieldOptions['readonly'])) ? $fieldOptions['readonly'] : '';
        $this->disabled = (isset($fieldOptions['disabled'])) ? $fieldOptions['disabled'] : '';
        $this->checked = ( isset($fieldOptions['checked'])) ? $fieldOptions['checked'] : '';
        $this->size = ( isset( $fieldOptions['size'])) ? $fieldOptions['size'] : '';
        $this->id = ( isset( $fieldOptions['id'])) ? $fieldOptions['id'] : $this->name;
    }

    protected function toHtmlAttribClass()
    {
        if ($this->class != '') {
            $this->class = " class=\"{$this->class}\"";
        }
    }

    protected function toHtmlAttribValue()
    {
        if ($this->value != '') {
            $this->value = " value=\"{$this->value}\"";
        }
    }

    protected function toHtmlAttribReadonly()
    {
        if ($this->readonly == TRUE) {
            $this->readonly = " readonly=\"readonly\"";
        } 
    }

    protected function toHtmlAttribDisabled()
    {
        if ($this->disabled == TRUE) {
            $this->disabled = " disabled=\"disabled\"";
        } 
    }

    protected function toHtmlAttribChecked( )
    {
        if ( $this->checked == TRUE ) {
            $this->checked = " checked=\"checked\"";
        }
    }

    protected function toHtmlAttribSize( )
    {
        if ( $this->size != '' && is_numeric( $this->size) ) {
            $this->size = " size=\"{$this->size}\"";   
        }
    }

    abstract public function toHtml();
    
}
?>
