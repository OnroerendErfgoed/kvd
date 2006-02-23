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
class KVDag_OverzichtTableHelper {

    /**
     * @var WebController
     */
    protected $_controller;
    /**
     * @var string
     */
    protected $module;
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
     * Maak het object aan.
     * 
     * $moduleConfig ziet er ongeveer zo uit:
     * <code>
     * <?php
     * $moduleConfig =  array ( 'module' => 'Gebruiker' ,
     *                          'actionOverzicht' => 'OrganisatieOverzicht',
     *                          'actionsPerRow' => array (  'action' => 'OrganisatieTonen',
     *                                                      'titel' => 'Deze organisatie tonen',
     *                                                      'naam' => 'Toon')
     *                        );
     * ?>                      
     * </code>
     * @param WebController $ctrl
     * @param array $moduleConfig
     */
    public function __construct ( $ctrl , $moduleConfig )
    {
        $this->_controller = $ctrl;
        $this->module = $moduleConfig['module'];
        // maak de basisurl aan voor navigatie door de records
        if (!is_array ( $moduleConfig['actionOverzicht'] ) ) {
            $this->actionOverzicht =    array ( MO_MODULE_ACCESSOR => $this->module,
                                                MO_ACTION_ACCESSOR => $moduleConfig['actionOverzicht']);
        } else {
            $this->actionOverzicht = $moduleConfig['actionOverzicht'];
        }
        $this->actionsPerRow = $moduleConfig['actionsPerRow'];

        // Een TableHelper aanmaken
        $this->_htmlTableHelper = New KVDhtml_TableHelper();
        
        // Een Linkhelper aanmaken
        $this->_htmlLinkHelper = New KVDhtml_LinkHelper();
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
            $parameters =   array ( MO_MODULE_ACCESSOR => $this->module,
                                    'id' => $row[0]);
            foreach ($this->actionsPerRow as $action) {
                $parameters[MO_ACTION_ACCESSOR] = $action['action'];
                $row[] = $this->_htmlLinkHelper->genHtmlLink($this->_controller->genURL(null, $parameters),$action['naam'],$action['titel']);
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

}
?>
