<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionRedraw.class.php,v 1.3 2005/12/20 15:58:14 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionRedraw extends KVDgis_MsMapAction
{
    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->_map->setExtent (    $this->_currentExtent->minx,
                                    $this->_currentExtent->miny,
                                    $this->_currentExtent->maxx,
                                    $this->_currentExtent->maxy
                                );
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
