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
class KVDag_OverzichtTableHelper extends KVDag_AbstractHelper
{

    /**
     * @var WebController
     */
    protected $_controller;
    /**
     * @var string
     */
    protected $standardModule;
    /**
     * @var array
     */
    protected $actionOverzicht;
    /**
     * @var array
     */
    protected $actionsPerRow;
    /**
     * @var KVDhtml_LinkHelper
     */
    protected $_htmlLinkHelper;
    /**
     * @var KVDhtml_TableHelper
     */
    protected $_htmlTableHelper;

    /**
     * @var array;
     */
    protected $headers;

    /**
     * @var array;
     */
    protected $fieldsPerRow;
    

    /**
     * Maak het object aan.
     * 
     * $moduleConfig ziet er ongeveer zo uit:
     * <code>
     * <?php
     * $moduleConfig =  array ( 'module' => 'Gebruiker' ,
     *                          'actionOverzicht' => 'OrganisatieOverzicht',
     *                          'headers'         => array ( array ( 'titel' => 'Id'),
     *                                                       array ( 'titel' => 'Omschrijving',
     *                                                               'orderField' => true,
     *                                                               'orderFieldName' => 'omschrijving')
     *                                                             )
     *                                                      ),
     *                          'fieldsPerRow'  => array ( 'getId', 'getOmschrijving' ),
     *                          'actionsPerRow' => array ( 'action' => 'OrganisatieTonen',
     *                                                     'titel' => 'Deze organisatie tonen',
     *                                                     'naam' => 'Toon',
     *                                                     'credential => 'Raadpleger')
     *                        );
     * ?>                      
     * </code>
     * @param WebController $ctrl
     * @param array $moduleConfig
     */
    public function __construct ( $ctrl , $moduleConfig )
    {
        // Een TableHelper aanmaken
        $this->_htmlTableHelper = New KVDhtml_TableHelper();
        
        // Een Linkhelper aanmaken
        $this->_htmlLinkHelper = New KVDhtml_LinkHelper();
        
        $this->_controller = $ctrl;
        $this->standardModule = $moduleConfig['module'];
        // maak de basisurl aan voor navigatie door de records
        if (!is_array ( $moduleConfig['actionOverzicht'] ) ) {
            $this->actionOverzicht =    array ( AG_MODULE_ACCESSOR => $this->standardModule,
                                                AG_ACTION_ACCESSOR => $moduleConfig['actionOverzicht']);
        } else {
            $this->actionOverzicht = $moduleConfig['actionOverzicht'];
        }

        if ( isset( $moduleConfig['fieldsPerRow'] ) ) {
             $this->fieldsPerRow = $moduleConfig['fieldsPerRow'];
        } else {
            $this->fieldsPerRow = array (  'getId', 'getOmschrijving' );
        }
        
        if ( isset ( $moduleConfig['actionsPerRow'] ) ) {
            $this->actionsPerRow = $moduleConfig['actionsPerRow'];
        } else {
            $this->actionsPerRow = array( );
        }

        if ( isset( $moduleConfig['headers'] ) ) {
            $this->genHeaders(  $moduleConfig['headers'] , null );
        } else {
            $this->headers = array (  'Id', 'Omschrijving' );
        }

    }

    /**
     * @param array $headers
     */
    public function setHeaders ( $headers )
    {
        $this->_htmlTableHelper->setHeaders ( $headers );
    }

    /**
     * Stel de te tonen rijen in.
     * 
     * Het eerste veld van elke rij in de array moet een integer zijn die de id-waarde van een rij voorstelt.
     * @param array $rows
     */
    public function setRows ( $rows )
    {
        // bouw het overzicht
        foreach ($rows as &$row) {
            $parameters =   array ( AG_MODULE_ACCESSOR => $this->standardModule,
                                    'id' => $row[0]);
            foreach ($this->actionsPerRow as &$action) {
                if ( $this->checkCredential( $action) ) {
                    $parameters[AG_ACTION_ACCESSOR] = $action['action'];
                    $row[] = $this->_htmlLinkHelper->genHtmlLink($this->_controller->genURL(null, $parameters),$action['naam'],$action['titel']);
                }
            }
        }
        $this->_htmlTableHelper->setRows ( $rows );
    }

    /**
     * @param array $cssClasses
     * @see KVDhtml_TableHelper::setCssClasses()
     * @return string
     */
    public function toHtml ( $cssClasses = null )
    {
        if (!is_null($cssClasses)) {
            $this->_htmlTableHelper->setCssClasses($cssClasses);
        }
        return $this->_htmlTableHelper->toHtml();
    }
    
    /**
     * @param array $action
     * @return boolean True indien de actie getoond mag worden, anders false.
     */
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

    /**
     *@param array $headerConfig
     */
    protected function genHeaders (  $headerConfig , $pageParameterName = null)
    {
        $this->headers = array(  );
        
        foreach (  $headerConfig as $header ) {
            if (  isset(  $header['orderField']) && $header['orderField'] == true ) {
                if (  !isset(  $header['orderFieldName'] ) ) {
                    throw new InvalidArgumentException (  'U hebt gespecifieerd dat er moet gesorteerd worden, maar niet op welk veld. Voeg de parameter orderFieldName toe.');
                }
                $parameters = $this->actionOverzicht;
                $parameters['orderField'] = $header['orderFieldName'];
                if ( $pageParameterName !== null ) {
                    $parameters[$pageParameterName] = 1;
                }
                $url = $this->_controller->genURL( null, $parameters);
                $this->headers[] = $this->_htmlLinkHelper->genHtmlLink(  $url, $header['titel'], 'Sorteren op ' . $header['titel'] );
            } else {
                $this->headers[] = $header['titel'];
            }
        }
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @param string $fieldString
     */
    protected function getDataForFieldString(  $domainObject, $fieldString)
    {
        $fields = explode( '.',$fieldString );
        foreach (  $fields as $field) {
            $domainObject = $domainObject->$field(  );
        }
        return $domainObject;
    }

    /**
     * @param KVDdom_DomainObjectCollection $collection
     */
    public function genRowsForCollection ( $collection , $generateActions = true )
    {
        $rows=array( );
        foreach ( $collection as $domainObject) {
            $row = array(  );
            foreach (  $this->fieldsPerRow as $field ) {
                $row[] = $this->getDataForFieldString(  $domainObject , $field );
            }
            if ( $generateActions && isset( $this->actionsPerRow ) ) {
                $row[] = $this->getLinks( $domainObject );           
            }
            $rows[] = $row;
        }
        if ( count ( $rows ) > 0) {
            $this->_htmlTableHelper->setRows (  $rows );
            $this->_htmlTableHelper->setHeaders (  $this->headers );
        }
    }

    private function getLinks ( $domainObject )
    {
         $links = array(  );
         foreach( $this->actionsPerRow as &$action ) {
             if (  $this->checkCredential(  $action ) ) {
                 if (  $this->checkForCurrentRecord(  $action, $domainObject ) &&
                    $this->checkForGecontroleerd(  $action, $domainObject ) ) {
                    $links[$action['naam']] = $this->genLinkFromAction(  $action , $domainObject);
                 }
             }
         }
         return implode ( ' ' , $links );
    }

    protected function determineAction( $action , $domainObject )
    {
         if ( !is_array ( $action['action'] ) ) {
            $action['action'] = array (  AG_MODULE_ACCESSOR => $this->standardModule,
                                         AG_ACTION_ACCESSOR => $action['action']);
         }
         $action['action']['id'] = $domainObject->getId( );
         return $action['action'];
    }
}
?>
