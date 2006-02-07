<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionZoomGemeente.class.php,v 1.4 2005/11/24 15:30:49 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDgis_MsMapActionZoomGemeente extends KVDgis_MsMapAction
{
    const GEMEENTELAYERNAME = "Gemeenten";

    const GEMEENTENISNRFIELD = "Nisnr";
    
    /**
     * @var integer Nisnr van de gemeente waarop gezoomd moet worden
     */
    private $zoomGemeente;
    
    /**
     * @param array $actionParameters Moet de parameter zmGemeente (= nis nr) bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['zmGemeente'])) {
            throw new Exception('zmGemeente not set!');
        }
        $this->zoomGemeente = intval ( $actionParameters['zmGemeente'] );    
    }

    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
        $this->_map->freequery(-1);
        $gemLayer = $this->_map->getLayerByName(self::GEMEENTELAYERNAME);
        $qGemeenteNisNrRes = $gemLayer->queryByAttributes(self::GEMEENTENISNRFIELD , $this->zoomGemeente , 'MS_SINGLE');
        if ( $qGemeenteNisNrRes == MS_SUCCESS ) {
            $gemLayer->open();
            $gem = $gemLayer->getShape($gemLayer->getResult(0)->tileindex , $gemLayer->getResult(0)->shapeindex);
            $gemLayer->close();
            $this->_map->setExtent ( $gem->bounds->minx , $gem->bounds->miny, $gem->bounds->maxx, $gem->bounds->maxy );
            $this->hasNewSelection = true;
        }
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
