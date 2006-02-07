<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionZoomProvincie.class.php,v 1.2 2005/11/24 15:29:47 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDgis_MsMapActionZoomProvincie extends KVDgis_MsMapAction
{
    const PROVINCIELAYERNAME = "Provincies";

    const PROVINCIENISNRFIELD = "PROV_NR";
    
    /**
     * @var integer Nisnr van de gemeente waarop gezoomd moet worden
     */
    private $zoomProvincie;
    
    /**
     * @param array $actionParameters Moet de parameter zmProvincie (= nis nr) bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['zmProvincie'])) {
            throw new Exception('zmProvincie not set!');
        }
        $this->zoomProvincie = floor( intval ($actionParameters['zmProvincie'] ) / 10000 );    
    }

    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
        $this->_map->freequery(-1);
        $provLayer = $this->_map->getLayerByName(self::PROVINCIELAYERNAME);
        $qProvNisNrRes = $provLayer->queryByAttributes(self::PROVINCIENISNRFIELD , $this->zoomProvincie , 'MS_SINGLE');
        if ( $qProvNisNrRes == MS_SUCCESS ) {
            $provLayer->open();
            $prov = $provLayer->getShape($provLayer->getResult(0)->tileindex , $provLayer->getResult(0)->shapeindex);
            $provLayer->close();
            $this->_map->setExtent ( $prov->bounds->minx , $prov->bounds->miny, $prov->bounds->maxx, $prov->bounds->maxy );
            $this->hasNewSelection = true;
        }
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
