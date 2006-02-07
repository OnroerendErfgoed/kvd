<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionZoomScale extends KVDgis_MsMapAction
{
    /**
     * @var PointObj
     */
    private $_zoomPoint;

    /**
     * @var integer
     */
    private $scale; 
    
    /**
     * @param array $actionParameters Moet de parameter zmScale bevatten. Mag optioneel de parameters zmX en zmY bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['zmScale'])) {
            throw new Exception ( 'zmScale not set!' );    
        }
        $this->scale = $actionParameters['zmScale'];
        
        if (!isset($actionParameters['zmX']) || !isset($actionParameters['zmY'])) {
            $actionParameters['zmX'] = $this->_map->width/2;
            $actionParameters['zmY'] = $this->_map->height/2;
        }
        $this->_zoomPoint = ms_newPointObj();
        $this->_zoomPoint->setXY($actionParameters['zmX'],$actionParameters['zmY']);    
    }

    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
        $this->_map->zoomscale  (   $this->scale, 
                                    $this->_zoomPoint, 
                                    $this->_map->width, 
                                    $this->_map->height, 
                                    $this->_currentExtent, 
                                    $this->_map->extent);
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
