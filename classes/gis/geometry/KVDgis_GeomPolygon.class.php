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
class KVDgis_GeomPolygon extends KVDgis_GeomGeometry
{
    /**
     * outer 
     * 
     * @var KVDgis_GeomLinearRing
     */
    private $outer;

    /**
     * inner 
     * 
     * @var array
     */
    private $inner = array( );

    /**
     * @param integer                   $srid
     * @param KVDgis_GeomLinearRing     $outer
     * @param array                     $inner
     */
    public function __construct ( $srid = -1, KVDgis_GeomLinearRing $outer = null, array $inner = null)
    {
        $this->setSrid($srid);
        if ( $outer != null ) {
            $this->setOuterRing( $outer );
        }
        if ( $inner != null && is_array( $inner ) ) {
            $this->setInnerRings( $inner );
        }
    }
    /**
     * setOuterRing
     *
     * Stel de buitenste ring in van een polygoon.
     * @param KVDgis_GeomLinearRing $outer
     */
    public function setOuterRing(KVDgis_GeomLinearRing $outer )
    {
        $this->outer = $outer;
    }
    /**
     * setInnerRings
     *
     * @param array $inner
     */
    public function setInnerRings( array $inner )
    {
        foreach ( $inner as $ring ) {
            $this->addInnerRing( $ring );
        }
    }
    /**
     * addInnerRing
     *
     * Voeg een binnenring toe aan een polygoon. Dit is dus een "gat" in een polygoon.
     * @param KVDgis_GeomLinearRing $inner
     */
    public function addInnerRing( KVDgis_GeomLinearRing $inner )
    {
        $this->inner[] = $inner;
    }

    public function getAsText( )
    {
        if ( $this->outer == null ) {
            return null;
        }
        $buffer = "POLYGON(";
        $inArray = array( );
        $inArray[] = substr($this->outer->getAsText( ), 10);
        foreach ( $this->inner as $inner ) {
            $inArray[] = substr( $inner->getAsText( ) , 10 );
        }
        $buffer .= implode ( ', ' , $inArray);
        $buffer .= ")";
        return $buffer;
    }

    public function clear( )
    {
        $this->outer = null;
        $this->inner = array( );
    }

    /**
     * setGeometryFromText
     *
     * Stel de geometry in aan de hand van het well known text formaat.
     * @param string $wkt
     */
    public function setGeometryFromText( $wkt )
    {
        if (substr($wkt,0,7) != 'POLYGON') {
            throw new InvalidArgumentException ('Ongeldige Well-Known Text string: ' . $wkt . "\n. De string zou moeten beginnen met 'POLYGON'.");
        }
        $this->clear( );
        
        $stringPolygon = $this->getStringBetweenBraces($wkt);
        $linestrings = explode("," , $stringPolygon);
        foreach ( $linestring as $ls ) {
            $lsObj = new KVDgis_GeomLinearRing( $this->getSrid( ) );
            $lsObj->setGeometryFromText( 'LINESTRING' . trim($ls));
            if ( $first ) {
                $this->setOuterRing( $lsObj );
                $first = false;
            } else {
                $this->addInnerRing( $lsObj );
            }
        }
    }

}
?>
