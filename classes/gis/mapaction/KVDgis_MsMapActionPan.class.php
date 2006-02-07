<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionPan.class.php,v 1.3 2005/10/27 10:08:00 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionPan extends KVDgis_MsMapAction
{
    const ZOOMFACTOR = 1;
    
    /**
     * @var PointObj
     */
    private $_zoomPoint;
    
    /**
     * @param array $actionParameters Moet de parameters panX en panY bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['panX']) || !isset($actionParameters['panY'])) {
            throw new Exception('panX en panY not set!');
        }
        $this->_panPoint = ms_newPointObj();
        $this->_panPoint->setXY($actionParameters['panX'],$actionParameters['panY']);
    }

    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
        $this->_map->zoompoint  (   self::ZOOMFACTOR, 
                                    $this->_panPoint, 
                                    $this->_map->width, 
                                    $this->_map->height, 
                                    $this->_currentExtent, 
                                    $this->_map->extent);
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
