<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionIdentifyPoint.class.php,v 1.2 2005/10/18 12:02:27 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionIdentifyPoint extends KVDgis_MsMapAction
{
    const QUERYBYPOINTBUFFER = 25;

    /**
     * @var PointObj
     */
    private $_identifyPoint;

    /**
     * @var string Een string met een HTML versie van het identify resultaat.
     */
    private $identifyResult = null;

    /**
     * @return KVDgis_MsMapState
     */
    protected function generateResponse()
    {
	    parent::generateResponse();
        $mapState = parent::generateResponse();
	    // Als de query succesvol was, dan wordt er een resultaat opgeslagen
	    $mapState->setIdentifyResult($this->identifyResult);
        return $mapState;
    }

    /**
     * @param array $actionParameters Moet de parameters idfyX en idfyY bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['idfyX']) || !isset($actionParameters['idfyY'])) {
            throw new Exception('idfyX en idfyY not set!');
        }
        $this->_identifyPoint = ms_newPointObj();
        $this->_identifyPoint->setXY($actionParameters['idfyX'],$actionParameters['idfyY']);
    }
    
    /**
     * @param array $actionParameters Moet de parameters idfyX en idfyY bevatten. 
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
	    //Skeleton code, moet nog aangepast worden
	    $qryResult = $this->_map->queryByPoint( $this->_identifyPoint,
						                        MS_MULTIPLE,
						                        self::POINTQUERYBUFFER);
	    //Loop door alle Layers en controleer waar er matches zijn.
	    //Indien ja worden die in HTML-tabellen gegoten (één per laag) en opgeslagen in queryResult.
			
	    // De map moet gezoomd worden naar de huidige extent
	    $this->_map->extent = $this->_currentExtent;
			
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
