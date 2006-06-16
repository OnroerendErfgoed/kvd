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
class KVDag_NavigatieAlgemeenHelper
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
     * @var mixed
     */
    private $id;

    /**
     * Maak een NavigatieNamenHelper aan.
     * @param WebController $ctrl
     * @param string $module
     * @param array $actions
     */
    public function __construct( $ctrl , $module, $actions , $id = null)
    {
        $this->_controller=$ctrl;

        $this->setModule($module);
        $this->setActions($actions);

        $this->id = $id;
        
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
     * action, needsId, attribute, noHtml, naam, titel, refererKey, referer, refererIdKey, refererId, target.
     * @var array $actions
     */
    public function setActions ( $actions )
    {
        if (!is_array($actions)) {
            $err = "De acties moet u opgegeven onder de vorm van een array!";
            throw New InvalidArgumentException($err);
        }
        $this->actions = $actions;
    }

    /**
     * @return array
     */
    public function genHtmlLinks()
    {
        foreach ($this->actions as &$action) {
            if ( $this->checkCredential( $action ) ) {
                $parameters = $this->checkAction ( $action );
                $this->checkReferer ( $action, $parameters);
                $this->checkIdSet ( $action, $parameters);
                $attribute = $this->checkAttribute ( $action );
                $target = $this->checkTarget( $action );
                if (!isset($action['noHtml'])) {
                    $Links[$attribute] = $this->_htmlLink->genHtmlLink ( $this->_controller->genURL( null , $parameters),
                                                                         $action['naam'],
                                                                         $action['titel'],
                                                                         '',
                                                                         $target);
                } else {
                    $Links[$attribute] = $this->_controller->genURL(null,$parameters);
                }
            }
        }   

        return $Links;
    }

    private function checkTarget ( &$action )
    {
        if ( isset( $action['target'])) {
            return $action['target'];
        } else {
            return '';
        }
    }

    private function checkAttribute ( &$action )
    {
        if (isset($action['attribute'])) {
            return $action['attribute'];
        } else {
            return $action['action'];
        }
    }

    private function checkAction ( &$action )
    {
        if (!is_array ( $action['action'] ) ) {
            $action['action'] =  array (  AG_MODULE_ACCESSOR => $this->module,
                                          AG_ACTION_ACCESSOR => $action['action']);
        } 
        return $action['action'];
    }

    private function checkReferer ( &$action, &$parameters )
    {
        if (isset($action['refererKey']) && isset ( $action['referer'] ) ) {
            $parameters[$action['refererKey']] = $action['referer'];    
        }
        if (isset($action['refererIdKey']) && isset ( $action['refererId'] ) ) {
            $parameters[$action['refererIdKey']] = $action['refererId'];    
        }
    }

    private function checkIdSet ( &$action , &$parameters )
    {
        if (isset($action['needsId']) && $action['needsId'] == true && $this->id != null) {
            $parameters['id'] = $this->id;
        } else {
            if (isset($parameters['id'])) {
                unset ($parameters['id']);
            }
        }
    }

    private function checkCredential(  &$action )
    {
        if (  !array_key_exists(  'credential', $action) ) {
            return true;
        } else {
            if (  !(  $this->_controller->getContext(  )->getUser(  ) instanceof BasicSecurityUser) ) {
                throw new Exception (  'U kunt enkel maar credentials toewijzen indien er met een BasicSecurityUser gewerkt wordt.');
            }
            if (  $this->_controller->getContext(  )->getUser(  )->hasCredential(  $action['credential'] ) ) {
                return true;
            } else {
                return false;
            }
        }
    }
}
?>
