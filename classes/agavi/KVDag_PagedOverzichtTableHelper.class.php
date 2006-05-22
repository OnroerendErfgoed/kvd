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
     * @var KVDdom_DomainObjectCollectionPager
     */
    private $pager;

    /**
     * @var string
     */
    private $pageParameterName;

    /**
     * Maak het object aan.
     * 
     * $moduleConfig is hetzelfde als voor {@link KVDag_OverzichtTableHelper} behalve dat er een extra parameter 'pageParameterName' is.
     * Deze parameter bepaalt met parameter in de urls gebruikt wordt om de pagina's van de tabel aan te duiden.
     * Differentiatie is nodig indien er meerdere KVDag_PagedOverzichtTableHelper op een pagina staan.
     * @see KVDag_OverzichtTableHelper::__construct( )
     * @param WebController $ctrl
     * @param array $moduleConfig
     * @param KVDdom_DomainObjectCollectionPager $pager
     */
    public function __construct ( $ctrl , $moduleConfig , $pager )
    {
        parent::__construct( $ctrl, $moduleConfig);

        // Een PagedTableHelper aanmaken ipv de standaard TableHelper
        $this->_htmlTableHelper = New KVDhtml_PagedTableHelper( );
                
        $this->pageParameterName = isset( $moduleConfig['pageParameterName']) ? $moduleConfig['pageParameterName'] : 'page';
        
        $this->_pager = $pager;    
    }

    /**
     * @param string $pageParameterName Indien niet gespecifieerd wordt de waare uit de config of de default waarde 'page' genomen.
     */
    public function genPageLinks ( $pageParameterName = null)
    {
        if ( is_null( $pageParameterName ) ) {
            $pageParameterName = $this->pageParameterName;
        }
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
        try {
            $domainObjects = $this->_pager->getResult();
        } catch ( Exception $e ) {
            throw new UnexpectedValueException ( 'Kon geen objecten krijgen van de pager. De pager deelde het volgende mee: ' . $e->getMessage( ) );
        }
        $this->genRowsForCollection( $domainObjects );
    }
    
}
?>
