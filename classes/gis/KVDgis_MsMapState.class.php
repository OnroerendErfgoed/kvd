<?php
/**
 * @package KVD.gis
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDgis_MsMapState.class.php,v 1.6 2005/12/20 16:11:37 Koen Exp $
 */

/**
 * @package KVD.gis
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDgis_MsMapState
{
    /**
     * @var string
     */
    private $mapImageUrl;

    /**
     * @var string
     */
    private $legendHtml; 

    /**
     * @var array
     */
    private $currentExtent;

    /**
     * @var integer
     */
    private $scale;

    /**
     * @var integer
     */
    private $mapImageWidth;

    /**
     * @var integer
     */
    private $mapImageHeight;

    /**
     * @var array
     */
    private $layers;

    /**
     * @var string
     */
    private $queryFile = null;

    /**
     * @param mixed Assoc array of Object met alle map kenmerken.
     */
    public function __construct ( $mapState = null )
    {
        if ( $mapState != null ) {
            if (is_array($mapState)) {
                $this->setMapStateArray ( $mapState );    
            } else if (is_object($mapState)) {
                $this->setMapStateObject ( $mapState );
            } else {
                throw new Exception ('Ongeldige parameter mapState: ' . $mapState . '. Is geen array of object.' );
            }
        }
    }

    /**
     * @param string $url
     */
    public function setMapImageUrl( $url )
    {
        $this->mapImageUrl = $url;
    }
    
    /**
     * @return string
     */
    public function getMapImageUrl()
    {
        return $this->mapImageUrl;
    }

    /**
     * @param string $html
     */
    public function setLegendHtml( $html )
    {
        $this->legendHtml = $html;
    }
    
    /**
     * @return string
     */
    public function getLegendHtml()
    {
        return $this->legendHtml;
    }

    /**
     * @param array Assoc array met keys minx, miny, maxx, maxy
     * @todo omvormen zodat de parameter ook als een object kan gegeven worden zoals bij setLayers(). In elk geval uniformiseren.
     */
    public function setCurrentExtent( $extent )
    {
        if (!is_array($extent)) {
            throw new Exception ('Ongeldige parameter extent bij methode setCurrentExtent: ' . $extent . '. Is geen array.' );
        }
        if (($extent['minx'] >= $extent ['maxx'])) {
            throw new Exception ('Ongeldige parameter extent bij methode setCurrentExtent: ' . $extent . ". Minx ({$extent['minx']}) >= maxx ({$extent['maxx']})." );    
        }
        if (($extent['miny'] >= $extent ['maxy'])) {
            throw new Exception ('Ongeldige parameter extent bij methode setCurrentExtent: ' . $extent . ". Minx ({$extent['miny']}) >= maxx ({$extent['maxy']})." );    
        }    
        $this->currentExtent = $extent;
    }
    
    /**
     * @return array Assoc array met keys minx,miny,maxx,maxy
     */
    public function getCurrentExtent()
    {
        return $this->currentExtent;
    }

    /**
     * @param integer
     */
    public function setScale( $scale )
    {
        $this->scale = $scale;
    }

    /**
     * @return integer
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * @param integer $width
     */
    public function setMapImageWidth( $width )
    {
        $this->mapImageWidth = $width;
    }

    /**
     * @return integer Breedte van de afbeelding in pixels
     */
    public function getMapImageWidth()
    {
        return $this->mapImageWidth;
    }

    /**
     * @param integer $height
     */
    public function setMapImageHeight( $height )
    {
        $this->mapImageHeight = $height;
    }

    /**
     * @return integer Hoogte van de afbeelding in pixels
     */
    public function getMapImageHeight()
    {
        return $this->mapImageHeight;
    }

    /**
     * @param string $queryFile De naam van een bestand waarin een query werd opgeslagen.
     */
    public function setQueryFile ( $queryFile )
    {
        $this->queryFile = $queryFile;    
    }

    /**
     * @return string $queryFile De naam van een bestand waarin een query werd opgeslagen.
     */
    public function getQueryFile ()
    {
        return $this->queryFile;    
    }

    /**
     * @param array $layers Array met de lagen en hun status.
     */
    public function setLayers ( $layers )
    {
        if (!is_array($layers)) {
            throw new Exception ('Ongeldige parameter layers bij methode setLayers: ' . $layers . '. Is geen array.' );
        }
        $arrayLayers = array();
        foreach ($layers as $layer) {
            $arrayLayers[] = $this->convertObjectToArray( $layer );
        }
        $this->layers = $arrayLayers;
    }

    /**
     * @return array $layers Array met de lagen en hun status.
     */
    public function getLayers ()
    {
        return $this->layers;
    }

    /**
     * Converteer de status van de map naar een associative array.
     * @return array Assoc array met alle map kenmerken.
     */
    public function convertToArray()
    {
        $mapState = $this->convertObjectToArray ( $this );
        return $mapState;
    }

    /**
     * Converteer de status van de map naar een publiek object.
     * @return stdClass Object met alle map kenmerken.
     */
    public function convertToObject()
    {
        $mapState = $this->convertArrayToObject ( $this );
        return $mapState;
    }

    /**
     * @param array array dat moet geconverteerd worden.
     * @return stdClass Object versie van array.
     */
    private function convertArrayToObject ( $array )
    {
        $object = new stdClass();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $value = $this->convertArrayToObject($value);    
            }
            $object->$key = $value;    
        }
        return $object;
    }

    /**
     * @param stdClass Object dat moet geconverteerd worden.
     * @return array Array versie van Object.
     */
    private function convertObjectToArray ( $object )
    {
        $array = array();
        foreach ( $object as $key => $value) {
            $array[$key] = $value;
            if (is_object($value)) {
                $value = $this->convertObjectToArray ( $value );     
            }
        }
        return $array; 
    }

    /**
     * @param array $mapStateArray
     */
    private function setMapStateArray ( $mapStateArray )
    {
        if (!is_array($mapStateArray)) {
            throw new Exception ( "Geen geldige parameter: mapStateArray bij functie setMapStateArray.");    
        }
        foreach ($mapStateArray as $key => $value) {
            if ( $key == 'currentExtent' ) {
                $this->setCurrentExtent($value);
            } else if ( $key == 'layers' ) {
                $this->setLayers($value);   
            } else {
                $this->$key = $value;        
            }
        }
    }

    /**
     * @param stdClass $mapStateObject
     */
    private function setMapStateObject ( $mapStateObject )
    {
        if (!is_object($mapStateObject)) {
            throw new Exception ( "Geen geldige parameter: mapStateObject bij functie setMapStateObject.");    
        }
        foreach ($mapStateObject as $key => $value) {
            if (is_object($value)) {
                $value = $this->convertObjectToArray($value);
            }
            if ( $key == 'currentExtent' ) {
                $this->setCurrentExtent($value);
            } else if ( $key == 'layers' ) {
                $this->setLayers($value);       
            } else {
                $this->$key = $value;        
            }
        }
	}
}
?>
