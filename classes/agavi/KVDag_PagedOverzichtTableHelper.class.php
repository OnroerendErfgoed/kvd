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
class KVDag_PagedOverzichtTableHelper extends KVDag_OverzichtTableHelper {

    /**
     * @var KVDdomain_ObjectCollectionPager
     */
    private $pager;

    /**
     * Maak het object aan.
     * 
     * $moduleConfig ziet er ongeveer zo uit:
     * <code>
     * <?php
     * $moduleConfig =  array ( 'module' => 'Gebruiker' ,
     *                          'actionOverzicht' => 'OrganisatieOverzicht',
     *                          array ( 'action' => 'OrganisatieTonen',
     *                                   'titel' => 'Deze organisatie tonen',
     *                                   'naam' => 'Toon')
     *                        );
     * ?>                      
     * </code>
     * @param WebController $ctrl
     * @param array $moduleConfig
     * @param KVDdomain_ObjectCollectionPager $pager
     */
    public function __construct ( $ctrl , $moduleConfig , $pager )
    {
        $this->_controller = $ctrl;
        $this->module = $moduleConfig['module'];
        // maak de basisurl aan voor gepagineerde navigatie door de records
        if (!is_array ( $moduleConfig['actionOverzicht'] ) ) {
            $this->actionOverzicht =    array ( AG_MODULE_ACCESSOR => $this->module,
                                                AG_ACTION_ACCESSOR => $moduleConfig['actionOverzicht']);
        } else {
            $this->actionOverzicht = $moduleConfig['actionOverzicht'];
        }
        $this->actionsPerRow = $moduleConfig['actionsPerRow'];
        
        $this->_htmlTableHelper = New KVDhtml_PagedTableHelper();
        
        $this->_htmlLinkHelper = New KVDhtml_LinkHelper();
    
        $this->_pager = $pager;    
    }

    /**
     * @param string $pageParameterName
     */
    public function genPageLinks ( $pageParameterName = 'page' )
    {
        $parameters = $this->actionOverzicht;
        $pageLinks = array();
        $parameters[$pageParameterName] = $this->_pager->getFirstPage();
        $pageLinks['eerste'] = $this->_controller->genURL(null, $parameters);
        $parameters[$pageParameterName] = $this->_pager->getLastPage();
        $pageLinks['laatste'] = $this->_controller->genURL(null, $parameters);
        if ($this->_pager->getPrev() !== false) {
            $parameters[$pageParameterName] = $this->_pager->getPrev();
            $pageLinks['vorige'] = $this->_controller->genURL(null, $parameters);
        }
        if ($this->_pager->getNext() !== false) {
            $parameters[$pageParameterName] = $this->_pager->getNext();
            $pageLinks['volgende'] = $this->_controller->genURL(null, $parameters);
        }
        foreach ($this->_pager->getPrevLinks() as $prevLink) {
            $parameters[$pageParameterName] = $prevLink;
            $pageLinks[$prevLink] = $this->_controller->genURL(null, $parameters);
        }
        $parameters[$pageParameterName] = $this->_pager->getPage();
        $pageLinks[$this->_pager->getPage()] = $this->_controller->genURL(null, $parameters);
        foreach ($this->_pager->getNextLinks() as $nextLink) {
            $parameters[$pageParameterName] = $nextLink;
            $pageLinks[$nextLink] = $this->_controller->genURL(null, $parameters);
        }
        $this->_htmlTableHelper->setPageLinks($this->_pager->getPage(),$this->_pager->getTotalPages(),$pageLinks);
    }

    /**
     * Vul de rows en de headers in op basis van de domainObjects in de pager hun Id en Omschrijving.
     */
    public function genRows ()
    {
        $domainObjects = $this->_pager->getResult();
        $rows=array();
        foreach ($domainObjects as $domainObject) {
            $rows[] = array ( $domainObject->getId() , $domainObject->getOmschrijving() );   
        }
        if (count ( $rows ) > 0) {
            $this->setRows ( $rows );
            $this->setHeaders ( array ( 'Id' , 'Omschrijving' ) );    
        }
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
