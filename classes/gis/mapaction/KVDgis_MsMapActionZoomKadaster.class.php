<?php
/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapActionZoomKadaster.class.php,v 1.5 2005/12/20 16:00:05 Koen Exp $
 */

/**
 * @package KVD.gis.mapaction
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

class KVDgis_MsMapActionZoomKadaster extends KVDgis_MsMapAction
{

    const DBCONNECTIONNAME = 'alg_gis';

    const KADASTERTABEL = 'alg_gis.KSOVL_P';

    const KADASTERLAYERNAME = 'Kadaster OVL Punten';

    const KADASTERRASTERLAYERNAME = 'Kadaster OVL';

    const KADASTERPERCEELBUFFER = 50;
    
    /**
     * @var integer
     */
    private $kadasterGemeente;

    /**
     * @var integer
     */
    private $kadasterAfdeling;

    /**
     * @var string
     */
    private $kadasterSectie;

    /**
     * @var integer
     */
    private $kadasterGrondnummer;

    /**
     * @var string
     */
    private $kadasterExponent = null;
    
    /**
     * @param array $actionParameters Een array met de parameters kadGemeente, kadAfdeling, kadSectie, kadGrondnr (verplicht) en kadExponent (optioneel). 
     */
    public function initActionParameters ( $actionParameters )
    {
        if (!isset($actionParameters['kadGemeente']) || !isset($actionParameters['kadAfdeling']) || !isset($actionParameters['kadSectie']) || !isset($actionParameters['kadGrondnr']) ) {
            throw new Exception ('Te weinig parameters. De parameters kadGemeente, kadAfdeling, kadSectie en kadGrondnr moeten ingesteld zijn.');    
        }
        $this->kadasterGemeente = intval($actionParameters['kadGemeente']);
        $this->kadasterAfdeling = intval($actionParameters['kadAfdeling']);
        $this->kadasterSectie = strval($actionParameters['kadSectie']);
        $this->kadasterGrondnr = intval($actionParameters['kadGrondnr']);
        if (isset($actionParameters['kadExponent'])) {
            $this->kadasterExponent = strval($actionParameters['kadExponent']);    
        }
        
    }

    /**
     * @param array $actionParameters Een array met de parameters kadGemeente, kadAfdeling, kadSectie, kadGrondnr (verplicht) en kadExponent (optioneel). 
     * @return KVDgis_MsMapState
     */
    public function execute ( $actionParameters )
    {
        $this->initActionParameters ( $actionParameters );

        $sql =  "SELECT gid FROM " . self::KADASTERTABEL;
        $where =   " WHERE fgnisnr={$this->kadasterGemeente}
                     AND kadgemnr={$this->kadasterAfdeling}
                     AND sectie='{$this->kadasterSectie}'
                     AND grondnr={$this->kadasterGrondnr}";
        if (!is_null($this->kadasterExponent)) {
            $where .= " AND exponent='{$this->kadasterExponent}'";
        }
        $sql .= $where; 
        try {
            $db = $this->_ctrl->getConnection(self::DBCONNECTIONNAME);
        } catch (Exception $e) {
            $this->drawMap();
            return $this->generateResponse();    
        }
        try {
            $kadLayer = $this->_map->getLayerByName(self::KADASTERLAYERNAME);
            
            // Zoek de gid (unique identifier) van alle percelen die aan de criteria voldoen
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $gids = $stmt->fetchAll(PDO_FETCH_NUM);
            
            // Stel een lijst met de gid's samen om te gebruik in een mapserver query.
            if (count($gids) > 0) {
                $expression = 'gid IN (';
                foreach ($gids as $gid) {
                    $expression .= $gid[0] . ', ';
                }
                $expression = substr ($expression,0,-2) . ')';    
            } else {
                throw new Exception ('Geen percelen gevonden met sql: ' . $sql );    
            }
            
            // Query de kadaster-laag via mapscript
            $qKadasterRes = $kadLayer->queryByAttributes('gid',$expression,MS_MULTIPLE);
            if ( $qKadasterRes == MS_SUCCESS ) {
                $this->hasActiveSelection = true;
                $sql =  "SELECT " .
                        "xmin(buffer(extent(the_geom),". self::KADASTERPERCEELBUFFER . ")), 
                        xmax(buffer(extent(the_geom),". self::KADASTERPERCEELBUFFER . ")), 
                        ymin(buffer(extent(the_geom),". self::KADASTERPERCEELBUFFER . ")),
                        ymax(buffer(extent(the_geom),". self::KADASTERPERCEELBUFFER .")) 
                        FROM alg_gis.KSOVL_P".
                        $where;
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $bbox = $stmt->fetch(PDO_FETCH_ASSOC);
                // Zoom in op de bounding box van de gevonden percelen.
                $this->_map->setExtent ( $bbox['xmin'] , $bbox['ymin'] , $bbox['xmax'] , $bbox['ymax'] );
                // Mogelijk wordt er gezoomd zonder dat de laag aanstaat. Indien ze niet aanstaat, aanzetten
                $kadLayer->set('status',MS_ON);
                $kadRasterLayer = $this->_map->getLayerByName(self::KADASTERRASTERLAYERNAME);
                $kadRasterLayer->set('status',MS_ON);
            }
        } catch ( Exception $e ) {
            throw $e;
        }
        $this->drawMap();
        return $this->generateResponse();
    }
}
?>
