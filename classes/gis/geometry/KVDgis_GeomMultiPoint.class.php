<?php
/**
 * @package KVD.gis.geometry
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.gis.geometry
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 23 jun 2006
 */
class KVDgis_GeomMultiPoint extends KVDgis_GeomGeometry
{
    /**
     * @var array
     */
    private $points

    /**
     * @param integer $srid
     * @param array $points
     */
    public function __construct ( $srid = -1, $points = null)
    {
        $this->setSrid($srid);
        if ( is_null( $points ) ) {
            $points = array( );
        }
        $this->setPoints( $points );
    }

    public function addPoint( $point )
    {
        $this->points[] = $point;
        
    }

    public function removePoint ( $point )
    {
        
    }

    public function clearPoints( )
    {
        
    }

    public function setPoints( $points )
    {
        
    }

    public function getPoint( $index )
    {
        
    }
    
    /**
     * @see KVDgis_GeomGeometry::setGeometryFromText()
     * @param string $wkt vb. MULTIPOINT(1 2, 4 5, 8 9). Opgelet! Dit is eigenlijk ongeldig! Volgende versies van postgis zullen een andere syntax gebruiken.
     * @throws <b>InvalidArgumentException</b> - Indien de wkt-string ongeldig is.
     */
    public function setGeometryFromText ( $wkt )
    {
        if (substr($wkt,0,10) != 'MULTIPOINT') {
            throw new InvalidArgumentException ('Ongeldige Well-Known Text string: ' . $wkt . "\n. De string zou moeten beginnen met 'MULTIPOINT'.");
        }
        $this->clearPoints( );
        
        $stringiMultiPoint = $this->getStringBetweenBraces($wkt);
        $points = explode(", " , $stringMultiPoint);
        foreach ( $points as $point ) {
            $stringPoint = $this->getStringBetweenBraces( $point );
            $points = explode( " ", $stringPoint );
            $pointObj = new KVDgis_GeomMultiPoint( $this->getSrid( ) );
            $pointObj->setX($points['0']);
            $pointObj->setY($points['1']);
            $this->addPoint( $pointObj );
        }
    }

    /**
     * @see KVDgis_GeomGeometry::getAsText()
     * @return string
     */
    public function getAsText()
    {
        $buffer = "MULTIPOINT(";
        foreach ( $this->points as $point ) {
            $buffer .= substr( $point->getAsText( ) , 5 );
        }
        $buffer .= ")";
        return $buffer;
    }
}
?>
