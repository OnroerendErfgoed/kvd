<?php
/**
 * @package     KVD.gis.geometry
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * @package     KVD.gis.geometry
 * @since       11 jun 2009
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_GeomLineString extends KVDgis_GeomGeometry
{
    /**
     * @var array
     */
    private $points = array( );

    /**
     * @param integer $srid
     * @param array $points
     */
    public function __construct ( $srid = -1, array $points = null)
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
    public function addPoint( KVDgis_GeomPoint $point )
    {
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
    public function setPoints( array $points )
    {
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
     * @param string $wkt vb. LINESTRING(1 2,4 5, 8 9).
     * @throws <b>InvalidArgumentException</b> - Indien de wkt-string ongeldig is.
     */
    public function setGeometryFromText ( $wkt )
    {
        if (substr($wkt,0,10) != 'LINESTRING') {
            throw new InvalidArgumentException ('Ongeldige Well-Known Text string: ' . $wkt . "\n. De string zou moeten beginnen met 'LINESTRING'.");
        }
        $this->clearPoints( );
        
        $stringLineString = $this->getStringBetweenBraces($wkt);
        $points = explode("," , $stringLineString);
        foreach ( $points as $point ) {
            if ( strpos( $point , '(' ) === false ) {
                $stringPoint = trim( $point );
            } else {
                throw new InvalidArgumentException( 'Ongeldige Well-Known Text string: ' . $wkt . "\n. Een xy-paar mag niet omgeven worden door ronde haakjes." );
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
        $buffer = "LINESTRING(";
        $pointArray = array( );
        foreach ( $this->points as $point ) {
            $pointArray[] = $point->getX( ) . ' ' . $point->getY( );
        }
        $buffer .= implode ( ', ' , $pointArray);
        $buffer .= ")";
        return $buffer;
    }

    /**
     * isEmpty 
     * 
     * @return boolean
     */
    public function isEmpty( )
    {
        return count( $this->points ) <= 0;
    }

}
?>
