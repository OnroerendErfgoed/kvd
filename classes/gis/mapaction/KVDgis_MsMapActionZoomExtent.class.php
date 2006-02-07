<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionZoomExtent.class.php,v 1.3 2005/10/27 10:08:37 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionZoomExtent extends KVDgis_MsMapAction
{
    /**
     * @return KVDgis_MsMapState
     */
    public function execute( $actionParameters )
    {
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
