<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapAction.class.php,v 1.6 2005/12/20 15:56:36 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
abstract class KVDgis_MsMapAction
{
    /**
     * @var KVDgis_MsMapController
     */
    protected $_ctrl; 
    
    /**
     * @var MapObj
     */
    protected $_map;

    /**
     * @var ImageObj
     */
    protected $_mapImage;

    /**
     * @var string
     */
    protected $legendHtml; 

    /**
     * @var RectObj
     */
    protected $_currentExtent;

    /**
     * @var boolean
     */
    protected $hasActiveSelection = false;

    /**
     * @var boolean
     */
    protected $hasNewSelection = false;

    /**
     * @var string
     */
    private $queryFile = null; 
        
    /**
     * @param KVDgis_MsMapController $mapController
     * @param KVDgis_MsMapState $mapState
     */
    public function __construct ( $mapController , $mapState )
    {
        $this->_ctrl = $mapController;
        
        try {
            $this->_map = ms_newMapObj($this->_ctrl->getMapFile());
        } catch ( Exception $e ) {
            throw new Exception ( "Er kon geen MapObj worden aangemaakt op basis van de mapfile {$this->_ctrl->getMapFile()}. 
            Volgende exception werd gegenereerd: {$e->getMessage()}.");    
        }
        $this->generateCurrentExtent( $mapState );
	    $this->adjustActiveLayers ( $mapState );
        $this->checkSavedQuery ( $mapState );
        $this->_map->web->set( 'imagepath' , $this->_ctrl->getImageSaveDir() );    
    }

    /**
     * @param KVDgis_MsMapState $mapState
     */
    private function checkSavedQuery ( $mapState )
    {
        if ( !is_null ( $mapState->getQueryFile() ) ) {
            $this->queryFile = $mapState->getQueryFile();
            $this->_map->loadquery( $this->queryFile );
            $this->hasActiveSelection = true;
        }
    }

    /**
     * @return string $queryFile Volledig pad naar een savefile
     * @todo zorgen dat niet het volledige pad wordt verstuurd, is onveilig
     */
    private function saveSelectionToFile ()
    {
        $qrySaveDir = $this->_ctrl->getQuerySaveDir();
        if (substr($qrySaveDir, -1,1) == '/') {
            $qrySaveDir = substr($qrySaveDir,0,-1);    
        }
        $filePath = tempnam($qrySaveDir,'qry');
        $this->_map->savequery ( $filePath );
        $this->queryFile = $filePath;
    }

    /**
     * @param KVDgis_MsMapState $mapState
     */
    private function generateCurrentExtent( $mapState )
    {
        $this->_currentExtent = ms_newRectObj();
        $currentExtent = $mapState->getCurrentExtent();
        $this->_currentExtent->setExtent    (   $currentExtent['minx'],
                                                $currentExtent['miny'], 
                                                $currentExtent['maxx'],
                                                $currentExtent['maxy']
                                            );    
    }

    /**
     * Stel de actieve lagen in.
     * @param KVDgis_MsMapState $mapState
     */
    private function adjustActiveLayers ( $mapState )
    {
	    foreach ($mapState->getLayers() as $layer) {
            $msLayer = $this->_map->getLayerByName($layer['name']);
            if ($layer['on'] == true ) {
                $msLayer->set('status',MS_ON);        
            } else {
                $msLayer->set('status',MS_OFF);    
            }
	    }
    }

    /**
     * Tekent de kaart en de legende.
     */
    protected function drawMap()
    {
        if ( $this->hasActiveSelection || $this->hasNewSelection ) {
            $this->_mapImage = $this->_map->drawQuery();
        } else {
            $this->_mapImage = $this->_map->draw();
        }
        $this->legendHtml = $this->_map->processlegendtemplate(array());
    }

    /**
     * @return KVDgis_MsMapState
     */
    protected function generateResponse()
    {
        $mapState = new KVDgis_MsMapState();
        $mapState->setMapImageUrl($this->_mapImage->saveWebImage());
        $mapState->setLegendHtml($this->legendHtml);
        $extent = Array ( 'minx' => $this->_map->extent->minx,
                          'miny' => $this->_map->extent->miny,
                          'maxx' => $this->_map->extent->maxx,
                          'maxy' => $this->_map->extent->maxy
                        );
        $mapState->setCurrentExtent( $extent );
        $mapState->setMapImageWidth ( $this->_map->width );
        $mapState->setMapImageHeight ( $this->_map->height );
        $mapState->setScale ( round($this->_map->scale,0) );
        if ( $this->hasNewSelection ) {
            $this->saveSelectionToFile();
        }
        $mapState->setQueryFile ( $this->queryFile ); 
        return $mapState;
    }

    /**
     * @return KVDgis_MsMapState
     */
    abstract public function execute ( $mapActionParameters );
}
?>
