<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDag_RedactieFormulierHelper
{
    /**
     * @var string
     */
    private $standardModule;

    /**
     * @var WebController
     */
    private $_controller;
    
    /**
     * @var KVDhtml_FormulierHelper
     */
    private $_formulierHelper;

    /**
     * @var KVDag_DomainObjectToHtml
     */
    private $_domainObjectRenderer;

    /**
     * @var KVDdom_LogableDomainObject
     */
    private $_domainObject;

    /**
     * @var KVDdom_DomainObjectCollection
     */
    private $_loggedDomainObjects;

    /**
     * @var array
     */
    private $config;

    /**
     * @param Webcontroller $ctrl
     * @param array $config
     * @param string $standardModule
     * @param KVDag_DomainObjectToHtml $domainObjectRenderer
     * @param mixed $formAction Indien een string wordt er gezocht naar deze actie binnen standaardModule, indien een array wordt het array gebruikt.
     */
    public function __construct( $ctrl, $config , $standardModule , $domainObjectRenderer, $formAction)
    {
        $this->config = $config;

        $this->_controller = $ctrl;

        $this->standardModule = $standardModule;

        $this->_domainObjectRenderer = $domainObjectRenderer;

        $this->_formulierHelper = new KVDhtml_FormHelper( $this->determineFormAction( $formAction ), 'POST' );
    }

    /**
     * @param string $formAction
     * @return string Een url die verwijst naar de actie de de form moet uitvoeren.
     */
    private function determineFormAction( $formAction )
    {
        if ( !is_array( $formAction ) ) {
            $parameters = array ( AG_MODULE_ACCESSOR  =>  $this->standardModule ,
                                  AG_ACTION_ACCESSOR  =>  $formAction);
        }
        return $this->_controller->genUrl( null , $parameters );
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     */
    public function setDomainObject( $domainObject )
    {
        $this->_domainObject = $domainObject;
    }

    /**
     * @param KVDdom_DomainObjectCollection $collection
     */
    public function setLogCollection ( $collection )
    {
        $this->_loggedDomainObjects = $collection;    
    }

    /**
     * @param array $cssClasses
     * @return string Html versie van het formulier
     */
    public function getFormtoHtml( $cssClasses )
    {
        $config = $this->initializeConfig( $this->config );
        $this->_formulierHelper->genRows( $config );
        return $this->_formulierHelper->toHtml( );
    }

    /**
     * @param array $cssClasses
     * @return string Html versie van het object.
     */
    public function getDomainObjectToHtml( $cssClasses )
    {
        $this->_domainObjectRenderer->genAllForDomainObject( $this->_domainObject );
        return $this->_domainObjectRenderer->toHtml( $cssClasses );
    }

    /**
     * @param array $cssClasses
     * @return string Html versies van het gelogde objecten.
     */
    public function getLogCollectionToHtml ( $cssClasses )
    {
        $result='';
        foreach ( $this->_loggedDomainObjects as $domainObject ) {
            $this->_domainObjectRenderer->genAllForDomainObject( $domainObject );
            $result .= $this->_domainObjectRenderer->toHtml( $cssClasses ) . "\n<br/>\n";
        }
        return $result;
    }

    /**
     * @param array $config
     */
    private function initializeConfig ( $config )
    {
        $config['Id']['value'] = $this->_domainObject->getId(  );
        if (  $this->_domainObject->isNull(  ) ) {
            unset(  $config['Dit record Goedkeuren'] );
        } else {
            unset (  $config['De geschiedenis van dit record verwijderen'] );
        }
        $this->_loggedDomainObjects->rewind( );
        if ( $this->_loggedDomainObjects->valid(  ) ) {
            $config['Versie']['value'] = $this->_loggedDomainObjects->current(  )->getSystemFields(  )->getVersie(  );
        } else {
            //Er zijn geen vorige versies;
            unset (  $config['Versie'] );
            unset (  $config['Een versie terugzetten'] );
        }
        return $config;
    }

}
?>
