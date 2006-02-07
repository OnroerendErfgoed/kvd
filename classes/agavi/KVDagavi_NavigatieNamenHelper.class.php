<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDagavi_NavigatieNamenHelper
{
    /**
     * @var string
     */
    private $module;
    
    /**
     * @var array
     */
    private $actions;
    
    /**
     * @var WebController
     */
    private $_controller;
    
    /**
     * @var KVDhtml_LinkHelper
     */
    private $_htmlLink;

    /**
     * Maak een NavigatieNamenHelper aan.
     * @param WebController $ctrl
     * @param string $module
     * @param array $actions
     */
    public function __construct( $ctrl , $module, $actions )
    {
        $this->_controller=$ctrl;

        $this->setModule($module);
        $this->setActions($actions);
        
        $this->_htmlLink = new KVDhtml_LinkHelper();
    }

    /**
     * @param string $module
     */
    public function setModule ( $module )
    {
        $this->module = $module;
    }
    
    /**
     * array $actions kan volgende velden bevatten:
     * action, Id, attribute, noHtml, naam, titel, refererKey, referer, refererIdKey, refererId.
     * @var array $actions
     */
    public function setActions ( $actions )
    {
        if (!is_array($actions)) {
            $err = "De acties moet u opgegeven onder de vorm van een array!";
            throw New IllegalArgumentException($err);
        }
        $this->actions = $actions;
    }

    /**
     * @return array
     */
    public function genHtmlLinks()
    {
        foreach ($this->actions as $action) {
            if (!is_array ( $action['action'] ) ) {
                $action['action'] =  array (  MO_MODULE_ACCESSOR => $this->module,
                                              MO_ACTION_ACCESSOR => $action['action']);
            } 
            $parameters = $action['action'];
            if (isset($action['id'])) {
                $parameters['id'] = $action['id'];
            } else {
                if (isset($parameters['id'])) {
                    unset ($parameters['id']);
                }
            }
            if (isset($action['refererKey']) && isset ( $action['referer'] ) ) {
                $parameters[$action['refererKey']] = $action['referer'];    
            }
            if (isset($action['refererIdKey']) && isset ( $action['refererId'] ) ) {
                $parameters[$action['refererIdKey']] = $action['refererId'];    
            }
            if (isset($action['attribute'])) {
                $attribute = $action['attribute'];
            } else {
                $attribute = $action['action'];
            }
            if (!isset($action['noHtml'])) {
                $Links[$attribute] = $this->_htmlLink->genHtmlLink ( $this->_controller->genURL( null , $parameters),
                                                                     $action['naam'],
                                                                     $action['titel']);
            } else {
                $Links[$attribute] = $this->_controller->genURL(null,$parameters);
            }
        }

        return $Links;
    }
}
?>
