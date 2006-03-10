<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_FormField.class.php,v 1.1 2006/01/12 12:30:14 Koen Exp $
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
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
    protected $fieldFormat = "<input type=\"%s\" name=\"%s\"%s%s%s%s%s/>\n";

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
            $this->readonly = " readonly";
        } 
    }

    protected function toHtmlAttribDisabled()
    {
        if ($this->disabled == TRUE) {
            $this->disabled = " disabled";
        } 
    }

    protected function toHtmlAttribChecked( )
    {
        if ( $this->checked == TRUE ) {
            $this->checked = " checked";
        }
    }

    abstract public function toHtml();
    
}
?>
