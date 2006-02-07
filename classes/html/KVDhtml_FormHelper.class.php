<?php
/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDhtml_FormHelper.class.php,v 1.1 2006/01/12 12:30:14 Koen Exp $
 */

/**
 * @package KVD.html
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDhtml_FormHelper {

    /**
     * @var KVDhtml_FormFieldFactory
     */
    protected $_formFieldFactory;
    /**
     * @var KVDhtml_TableHelper
     */
    protected $_tableHelper;

    public function __construct ()
    {
        $this->_tableHelper = New KVDhtml_TableHelper();
        
        $this->_formFieldFactory = New KVDhtml_FormFieldFactory();

        $this->_tableHelper->setLijst(false);
    }

    /**
     * @param array $headers
     */
    public function setHeaders ( $headers )
    {
        $this->_tableHelper->setHeaders ( $headers );
    }

    /**
     * Stel de te tonen rijen in.
     * 
     * @param array $rows
     */
    public function setRows ( $rows )
    {
        $this->_tableHelper->setRows ( $rows );
    }

    /**
     * Maak de rijen aan op basis van de array
     *
     * @param array $fieldOptions
     */
    public function genRows ( $fieldOptions )
    {
        $hiddenfields = array();
        $rows = array();
        $headers = array();
        foreach ( $fieldOptions as $header => $fieldOption ) {
            if ( isset ( $fieldOption['type'] ) && $fieldOption['type'] == 'hidden' ) {
                $hiddenfields[] = $this->_formFieldFactory->getFormField ( $fieldOption )->toHtml();
            } else {
                $rows[] = $this->_formFieldFactory->getFormField ( $fieldOption )->toHtml();
                $headers[] = $header;
            }
        }
        $this->setHeaders ( $headers );
        $this->setRows ( $rows );
        $this->_tableHelper->setFooter( implode ( "\n" , $hiddenfields) );
    }

    /**
     * @param array $cssClasses
     * @see KVDhtml_TableHelper::setCssClasses()
     * @return string
     */
    public function toHtml ( $cssClasses = null )
    {
        if (!is_null($cssClasses)) {
            $this->_tableHelper->setCssClasses($cssClasses);
        }
        return $this->_tableHelper->toHtml();
    }
}
?>
