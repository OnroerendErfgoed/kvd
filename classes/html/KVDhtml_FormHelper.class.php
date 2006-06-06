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

    /**
     * @var string
     */
    private $formMethod;

    /**
     * @var string
     */
    private $formAction;

    /**
     * @var string
     */
    private $formEncType = 'application/x-www-form-urlencoded';

    /**
     * @var string
     */
    private $formHeader = '<form method="%s" action="%s" enctype="%s">';

    /**
     * @param string $formAction Url naar waar de form moet gepost worden.
     * @param string $formMethod Methode die de form moet gebruiken.
     */
    public function __construct ($formAction,$formMethod='POST')
    {
        $this->_tableHelper = New KVDhtml_TableHelper();
        
        $this->_formFieldFactory = New KVDhtml_FormFieldFactory();

        $this->_tableHelper->setLijst(false);

        $this->formAction = $formAction;

        if  ( !( $formMethod == 'POST' OR $formMethod == 'GET')) {
            throw new IllegalArgumentException ( __CLASS__ . ':formMethod moet GET of POST zijn.');
        }

        $this->formMethod = $formMethod;
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
        $footerfields = array( );
        $rows = array();
        $headers = array();
        foreach ( $fieldOptions as $header => $fieldOption ) {
            if ( isset ( $fieldOption['type'] ) && $fieldOption['type'] == 'file' ) {
                $this->formEncType = 'multipart/form-data';   
            }
            if ( isset ( $fieldOption['type'] ) && $fieldOption['type'] == 'hidden' ) {
                $hiddenfields[] = $this->_formFieldFactory->getFormField ( $fieldOption )->toHtml();
            } elseif ( isset ( $fieldOption['location'] ) && $fieldOption['location'] == 'footer'){
                $footerfields[] = $this->_formFieldFactory->getFormField ( $fieldOption )->toHtml( );
            } else {
                $rows[] = $this->_formFieldFactory->getFormField ( $fieldOption )->toHtml();
                $headers[] = $header;
            }
        }
        $this->setHeaders ( $headers );
        $this->setRows ( $rows );
        $footer = implode ( "\n" , $hiddenfields );
        $footer .= implode (  "\n" , $footerfields );
        $this->_tableHelper->setFooter( $footer );
    }

    private function toHtmlFormHeader( )
    {
        return sprintf( $this->formHeader, $this->formMethod, $this->formAction, $this->formEncType);
    }

    private function toHtmlFormFooter( )
    {
        return "</form>\n";
    }

    /**
     * @param array $cssClasses
     * @see KVDhtml_TableHelper::setCssClasses()
     * @return string
     */
    public function toHtml ( $cssClasses = null )
    {
        $html = $this->toHtmlFormHeader( ) . "\n";
        if (!is_null($cssClasses)) {
            $this->_tableHelper->setCssClasses($cssClasses);
        }
        $html .= $this->_tableHelper->toHtml();
        $html .= $this->toHtmlFormFooter( );
        return $html;
    }
}
?>
