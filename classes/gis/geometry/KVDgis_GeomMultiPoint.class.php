<?php
/**
 * @package KVD.gis.geometry
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license GNU General Public License {@link http://www.gnu.org/copyleft/gpl.html}
 * @version $Id$
 */

/**
 * KVDgis_GeomMultiPoint 
 * 
 * @package KVD.gis.geometry 
 * @since 23 jun 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license GNU General Public License {@link http://www.gnu.org/copyleft/gpl.html}
 */
class KVDgis_GeomMultiPoint extends KVDgis_GeomGeometry
{
    /**
     * @var array
     */
    private $points = array( );

    /**
     * @param integer $srid
     * @param array $points
     */
    public function __construct ( $srid = -1, $points = null)
    {
        $this->setSrid($srid);
        if ( $points != null && is_array( $points ) ) {
            $this->setPoints( $points );
        }
    }

    /**
     * addPoint 
     * 
     * @param KVDgis_GeomPoint $point 
     * @return void
     */
    public function addPoint( $point )
    {
        if ( !is_object( $point ) ) {
            throw new InvalidArgumentException ( 'U probeert een punt toe te voegen dat geen object is.' );
        }
        if ( $point->getX( ) == 0 && $point->getY( ) == 0 ) {
            return;
        }
        $this->points[] = $point;
    }

    /**
     * clearPoints 
     * 
     * @return void
     */
    public function clearPoints( )
    {
        $this->points = array( );    
    }

    /**
     * setPoints 
     * 
     * @param array $points Een verzameling KVDgis_GeomPoint objecten.
     * @return void
     */
    public function setPoints( $points )
    {
        if ( !is_array( $points ) ) {
            throw new InvalidArgumentException( 'U probeert een collectie van punten toe te voegen die niet bestaat.' );
        }
        foreach ( $points as $point ) {
            $this->addPoint( $point );
        }
    }

    /**
     * getPoints 
     * 
     * @return array $points Een verzameling KVDgis_GeomPoint objecten.
     */
    public function getPoints( )
    {
        return $this->points;
    }

    /**
     * 
     * @see KVDgis_GeomGeometry::setGeometryFromText()
     * @param string $wkt vb. MULTIPOINT((1 2), (4 5), (8 9)). Het ongeldige type dat postgis en de meeste andere paketten aanmaken kan ook gelezen worden.
     * @throws <b>InvalidArgumentException</b> - Indien de wkt-string ongeldig is.
     */
    public function setGeometryFromText ( $wkt )
    {
        if (substr($wkt,0,10) != 'MULTIPOINT') {
            throw new InvalidArgumentException ('Ongeldige Well-Known Text string: ' . $wkt . "\n. De string zou moeten beginnen met 'MULTIPOINT'.");
        }
        $this->clearPoints( );
        
        $stringMultiPoint = $this->getStringBetweenBraces($wkt);
        $points = explode("," , $stringMultiPoint);
        foreach ( $points as $point ) {
            if ( strpos( $point , '(' ) === false ) {
                $stringPoint = trim( $point );
            } else {
                $stringPoint = $this->getStringBetweenBraces( $point );
            }
            $punten = explode( " ", $stringPoint );
            $pointObj = new KVDgis_GeomPoint( $this->getSrid( ) );
            $pointObj->setX($punten['0']);
            $pointObj->setY($punten['1']);
            $this->addPoint( $pointObj );
        }
    }

    /**
     * @see KVDgis_GeomGeometry::getAsText()
     * @return string
     */
    public function getAsText()
    {
        if ( count( $this->points ) <= 0 ) {
            return null;
        }
        $buffer = "MULTIPOINT(";
        $pointArray = array( );
        foreach ( $this->points as $point ) {
            $pointArray[] = $point->getX( ) . " " . $point->getY( );
        }
        $buffer .= implode ( ', ' , $pointArray);
        $buffer .= ")";
        return $buffer;
    }
}
?>
