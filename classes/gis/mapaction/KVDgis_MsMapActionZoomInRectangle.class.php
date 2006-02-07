<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionZoomInRectangle.class.php,v 1.1 2005/12/07 15:57:21 Koen Exp $
 */

/**
 * Inzoomen op een rechthoek (in pixel coordinaten)
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionZoomInRectangle extends KVDgis_MsMapAction
{
    /**
     * @var RectObj
     */
    private $_zoomBox;
    
    /**
     * @param array $actionParameters Moet de parameters zmX1, zmY1, zmX2, zmY2 bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['zmX1']) || !isset($actionParameters['zmY1'])) {
            throw new Exception('zmX1 en zmY1 not set!');
        }
        if (!isset($actionParameters['zmX2']) || !isset($actionParameters['zmY2'])) {
            throw new Exception('zmX2 en zmY2 not set!');
        }
        $this->_zoomBox = ms_newRectObj();
        $this->_zoomBox->setExtent (    min ( $actionParameters['zmX1'] , $actionParameters['zmX2']),
                                        min ( $actionParameters['zmY1'] , $actionParameters['zmY2']),
                                        max ( $actionParameters['zmX1'] , $actionParameters['zmX2']),
                                        max ( $actionParameters['zmY1'] , $actionParameters['zmY2'])
                                    );
    }

    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
        $this->_map->zoomrectangle  (   $this->_zoomBox, 
                                        $this->_map->width, 
                                        $this->_map->height, 
                                        $this->_currentExtent
                                    );
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
