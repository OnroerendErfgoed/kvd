<?php
/**
 * @package    KVD.gis
 * @subpackage geometry
 * @version    $Id$
 * @copyright  2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDgis_GeomPoint 
 * 
 * @package    KVD.gis
 * @subpackage geometry
 * @since      jan 2006
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_GeomPoint extends KVDgis_GeomGeometry
{
    /**
     * @var number
     */
    private $x;

    /**
     * @var number
     */
    private $y;

    /**
     * @param integer $srid
     * @param number $x
     * @param number $y
     */
    public function __construct ( $srid = -1, $x= null, $y=null)
    {
        $this->setSrid($srid);
        $this->setX($x);
        $this->setY($y);
    }
    
    /**
     * @param number $x
     * @throws InvalidArgumentException - Indien $x geen integer of float is.
     */
    public function setX($x)
    {
        if ( !is_numeric( $x ) && !is_null( $x ) ) {
            throw new InvalidArgumentException( 
                "$x is geen geldig nummer en kan dus geen punt in een geometry zijn!" );
        }
        $this->x = $x;
    }

    /**
     * @param number $y
     * @throws InvalidArgumentException - Indien $x geen integer of float is.
     */
    public function setY($y)
    {
        if ( !is_numeric( $y ) && !is_null( $y ) ) {
            throw new InvalidArgumentException( 
                "$y is geen geldig nummer en kan dus geen punt in een geometry zijn!" );
        }
        $this->y = $y;
    }

    /**
     *@return number
     */
    public function getX()
    {
        return $this->x;    
    }

    /**
     * @return number
     */
    public function getY()
    {
        return $this->y;    
    }

    /**
     * @see KVDgis_GeomGeometry::setGeometryFromText()
     * @param string $wkt
     * @throws <b>InvalidArgumentException</b> - Indien de wkt-string ongeldig is.
     */
    public function setGeometryFromText ( $wkt )
    {
        if ( $wkt == 'EMPTY' ) {
            $this->x = null;
            $this->y = null;
            return;
        }
        if (substr($wkt, 0, 5) != 'POINT') {
            throw new InvalidArgumentException (
                'Ongeldige Well-Known Text string: ' . $wkt . 
                "\n. De string zou moeten beginnen met 'POINT'.");
        }
        
        $stringPoint = $this->getStringBetweenBraces($wkt);
        $points = explode(" ", $stringPoint);
        $this->setX($points['0']);
        $this->setY($points['1']);
    }

    /**
     * @see KVDgis_GeomGeometry::getAsText()
     * @return string
     */
    public function getAsText()
    {
        if ( $this->isEmpty( ) ) {
            return 'EMPTY';
        } else {
            return "POINT({$this->x} {$this->y})";
        }
    }

    /**
     * getAsJson 
     * 
     * @param boolean $encode Json terug geven als string of als object?
     * @return mixed String of Object.
     */
    public function getAsJson( $encode = true )
    {
        $json = new stdClass( );
        $json->type = 'Point';
        $json->coordinates = array( $this->x, $this->y );
        
        return $encode ? json_encode( $json ) : $json;
    }

    /**
     * isEmpty 
     * 
     * @return  boolean
     */
    public function isEmpty( )
    {
        return ( $this->x == null || $this->y == null );
    }
}
?>
