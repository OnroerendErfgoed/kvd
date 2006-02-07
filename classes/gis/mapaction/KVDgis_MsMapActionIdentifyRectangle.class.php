<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionIdentifyRectangle.class.php,v 1.3 2005/12/20 15:57:25 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDgis_MsMapActionIdentifyRectangle extends KVDgis_MsMapAction
{

    /**
     * @var RectObj
     */
    private $_identifyRectangle;

    /**
     * @var string Een string met een HTML versie van het identify resultaat.
     */
    protected $identifyResult = null;

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
     * @param array $actionParameters Moet de parameters idfyX1, idfyY1, idfyX2, idfyY2 bevatten.
     */
    private function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['idfyX1']) || !isset($actionParameters['idfyY1'])) {
            throw new Exception('idfyX1 en idfyY1 not set!');
        }
        if (!isset($actionParameters['idfyX2']) || !isset($actionParameters['idfY2'])) {
            throw new Exception('idfyX2 en idfyY2 not set!');
        }
        $this->_identifyRectangle = ms_newRectObj();
        $this->_identifyRectangle->setExtent (  min ( $actionParameters['idfyX1'] , $actionParameters['idfyX2']),
                                                min ( $actionParameters['idfyY1'] , $actionParameters['idfyY2']),
                                                max ( $actionParameters['idfyX1'] , $actionParameters['idfyX2']),
                                                max ( $actionParameters['idfyY1'] , $actionParameters['idfyY2'])
                                                );                                        
    }

    
    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
	    //Skeleton code, moet nog aangepast worden
	    $qryResult = $this->_map->queryByRect(  $this->_identifyRectangle );
	    //Loop door alle Layers en controleer waar er matches zijn.
	    //Indien ja worden die in HTML-tabellen gegoten (één per laag) en opgeslagen in queryResult.
			
	    // De map moet gezoomd worden naar de huidige extent
	    $this->_map->extent = $this->_currentExtent;
			
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
