<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionZoomOut.class.php,v 1.2 2005/10/18 12:06:35 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionZoomOut extends KVDgis_MsMapAction
{
    const ZOOMFACTOR = 2;
    
    /**
     * @var PointObj
     */
    private $_zoomPoint;
    
    /**
     * @param array $actionParameters Moet de parameters zmX en zmY bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['zmX']) || !isset($actionParameters['zmY'])) {
            throw new Exception('zmX en zmY not set!');
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
        
        $this->_map->zoompoint  (   -self::ZOOMFACTOR, 
                                    $this->_zoomPoint, 
                                    $this->_map->width, 
                                    $this->_map->height, 
                                    $this->_currentExtent, 
                                    $this->_map->extent
                                 );
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
