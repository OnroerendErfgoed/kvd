<?php
/**
 * @package     KVD.gis.geometry
 * @copyright   2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     GNU General Public License {@link http://www.gnu.org/copyleft/gpl.html}
 * @version     $Id$
 */

/**
 * KVDgis_GeomMultiPolygon 
 * 
 * @package     KVD.gis.geometry 
 * @since       29 jun 2009
 * @copyright   2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     GNU General Public License {@link http://www.gnu.org/copyleft/gpl.html}
 */
class KVDgis_GeomMultiPolygon extends KVDgis_GeomGeometry
{
    /**
     * @var array
     */
    private $polys = array( );

    /**
     * @param integer $srid
     * @param array $polys
     */
    public function __construct ( $srid = -1, $polys = null)
    {
        $this->setSrid($srid);
        if ( $polys != null && is_array( $polys ) ) {
            $this->setPolygons( $polys );
        }
    }

    /**
     * addPolygon 
     * 
     * @param   KVDgis_GeomPolygon $poly
     * @return  void
     */
    public function addPolygon( KVDgis_GeomPolygon $poly )
    {
        $this->polys[] = $poly;
    }

    /**
     * clearPolygons
     * 
     * @return void
     */
    public function clearPolygons( )
    {
        $this->polygons = array( );    
    }

    /**
     * setPolygons 
     * 
     * @param   array   $polys     Een verzameling KVDgis_GeomPolygon objecten.
     * @return  void
     */
    public function setPolygons( array $polys )
    {
        foreach ( $polys as $poly ) {
            $this->addPolygon( $poly );
        }
    }

    /**
     * getPolygons
     * 
     * @return  array   Een verzameling KVDgis_GeomPolygon objecten.
     */
    public function getPolygons( )
    {
        return $this->polys;
    }

    /**
     * 
     * @see KVDgis_GeomGeometry::setGeometryFromText()
     * @param string $wkt vb. MULTIPOLYGON(((1 2, 3 4, 5 6)), ((7 8, 9 10, 11 12, 13 14),(15 16, 17 18, 19 20))). 
     * @throws <b>InvalidArgumentException</b> - Indien de wkt-string ongeldig is.
     */
    public function setGeometryFromText ( $wkt )
    {
        if (substr($wkt,0,12) != 'MULTIPOLYGON') {
            throw new InvalidArgumentException ('Ongeldige Well-Known Text string: ' . $wkt . "\n. De string zou moeten beginnen met 'MULTIPOLYGON'.");
        }
        $this->clearPolygons( );
        
        $stringMultiPoly = $this->getStringBetweenBraces($wkt);
        $polystrings = array( );
        preg_match_all( '#\((\(((\d+(\.\d+)?)\s(\d+(\.\d+)?)\s*,?\s*)+\)\s*,?\s*)+\)#', $stringMultiPoly, $polystrings, PREG_SET_ORDER);
        foreach ( $polystrings as $poly ) {
            $polyWKT = 'POLYGON' . $poly[0];
            $p = new KVDgis_GeomPolygon( $this->getSrid( ) );
            $p->setGeometryFromText( $polyWKT );
            $this->addPolygon( $p );
        }
    }

    /**
     * @see KVDgis_GeomGeometry::getAsText()
     * @return string
     */
    public function getAsText()
    {
        if ( count( $this->polys) < 1 ) {
            return 'EMPTY';
        }
        $buffer = "MULTIPOLYGON(";
        
        $pArray = array();
        foreach ( $this->polys as $poly ) {
            $pArray[] = substr( $poly->getAsText( ) , 7 );
        }
        $buffer .= implode ( ', ' , $pArray);
        $buffer .= ")";
        return $buffer;
    }
}
?>
