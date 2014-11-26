<?php
/**
 * @package    KVD.gis
 * @subpackage geometry
 * @version    $Id$
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    GNU General Public License {@link http://www.gnu.org/copyleft/gpl.html}
 */

/**
 * Class die een multipoint voorstelt.
 * 
 * @package    KVD.gis
 * @subpackage geometry
 * @since      23 jun 2006
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    GNU General Public License {@link http://www.gnu.org/copyleft/gpl.html}
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
     * @param string $wkt vb. MULTIPOINT((1 2), (4 5), (8 9)). 
     *                    Het ongeldige type dat postgis en de meeste 
     *                    andere paketten aanmaken kan ook gelezen worden.
     * @throws <b>InvalidArgumentException</b> - Indien de wkt-string ongeldig is.
     */
    public function setGeometryFromText ( $wkt )
    {
        if ( $wkt == 'EMPTY' ) {
            $this->clearPoints( );
            return;
        }
        if (substr($wkt, 0, 10) != 'MULTIPOINT') {
            throw new InvalidArgumentException (
                'Ongeldige Well-Known Text string: ' . $wkt . 
                "\n. De string zou moeten beginnen met 'MULTIPOINT'.");
        }
        $this->clearPoints( );
        
        $stringMultiPoint = $this->getStringBetweenBraces($wkt);
        $points = explode(",", $stringMultiPoint);
        foreach ( $points as $point ) {
            if ( strpos( $point, '(' ) === false ) {
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
        if ( $this->isEmpty( ) ) {
            return 'EMPTY';
        }
        $buffer = "MULTIPOINT(";
        $pointArray = array( );
        foreach ( $this->points as $point ) {
            $pointArray[] = $point->getX( ) . " " . $point->getY( );
        }
        $buffer .= implode ( ', ', $pointArray);
        $buffer .= ")";
        return $buffer;
    }

    /**
     * getAsJson 
     *
     * @param boolean $encode
     * 
     * @return mixed String of Object.
     */
    public function getAsJson( $encode = true )
    {
        $json = new stdClass( );
        $json->type = 'MultiPoint';
        $json->coordinates = array( );
        foreach ( $this->points as $point ) {
            $json->coordinates[] = $point->getAsJson(false)->coordinates;
        }
        return $encode ? json_encode( $json ) : $json;
    }

    public function isEmpty( )
    {
        return count( $this->points ) == 0;
    }
}
?>
