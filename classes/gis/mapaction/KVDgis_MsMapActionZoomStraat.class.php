<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionZoomStraat.class.php,v 1.4 2005/12/07 15:54:31 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionZoomStraat extends KVDgis_MsMapAction
{
    const DBCONNECTIONNAME = "alg_gis";
    
    const STRAATLAYERNAME = "Stratenatlas";

    /**
     * @var integer Nisnr van de gemeente waarin de straat ligt waarop gezoomd moet worden.
     */
    private $zoomGemeente;
    
    /**
     * @var string Naam van de straat waarop gezoomd moet worden.
     */
    private $zoomStraat;
    
    private function initActionParameters ( $actionParameters )
    {
        if ( !(isset($actionParameters['zmGemeente'])) || !(isset($actionParameters['zmStraat'])) ) {
            throw new Exception ('zmGemeente of zmStraat werd niet ingesteld!');    
        }
        $this->zoomGemeente = intval ( $actionParameters['zmGemeente'] );
        $this->zoomStraat = strval ( $actionParameters['zmStraat'] );  
    }

    /**
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );
        $this->_map->freequery(-1);
        try {
            $db = $this->_ctrl->getConnection(self::DBCONNECTIONNAME);
        } catch (Exception $e) {
            $this->drawMap();
            return $this->generateResponse();    
        }
        
        $sql =  "SELECT
                 xmin(extent(the_geom)),
                 xmax(extent(the_geom)),
                 ymin(extent(the_geom)),
                 ymax(extent(the_geom))
                 FROM alg_gis.straten
                 WHERE fgnisnr = {$this->zoomGemeente} AND stname='{$this->zoomStraat}';";
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $bbox = $stmt->fetch(PDO_FETCH_ASSOC);    
        } catch (Exception $e) {
            throw new Exception ('Er kan niet gezoomd worden op de straat omwille van: ' . $e->getMessage());    
        }
        
        // De extents van de map instellen (komt neer op zoom to spatial rectangle)
        $this->_map->setExtent ( $bbox['xmin'] , $bbox['ymin'] , $bbox['xmax'] , $bbox['ymax'] );
        // Zorgen dat de laag met straatdata zeker aanstaat
        $this->_map->getLayerByName(self::STRAATLAYERNAME)->set('status',MS_ON);
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
