<?php
/**
 * @package KVD.gis.geometry
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.gis.geometry
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since jan 2006
 */
abstract class KVDgis_GeomGeometry
{
    /**
     * @var integer $srid Spatial Referencing System Identifier
     */
    private $srid;

    /**
     * @param integer $srid Spatial Referencing System Identifier
     */
    public function __construct( $srid = -1)
    {
        $this->setSrid ( $srid );
    }

    /**
     * @param integer $srid Spatial Referencing System Identifier
     */
    public function setSrid ( $srid )
    {
        $this->srid = $srid;
    }
    
    /**
     * @return integer Spatial Referencing System Identifier
     */
    public function getSrid ()
    {
        return $this->srid;    
    }

    /**
     * createFromText 
     * 
     * @since   16 mei 2009
     * @throws  InvalidArgumentException    Indien de string ongeldig is.
     * @param   string              $wkt 
     * @return  KVDgis_GeomGeometry
     */
    public static function createFromText( $wkt )
    {
        if ( $wkt == 'EMPTY' ) {
            $g = new KVDgis_GeomPoint( );
            return $g;
        } elseif (substr($wkt,0,5) == 'POINT') {
            $g = new KVDgis_GeomPoint( );
        } elseif (substr($wkt,0,10) == 'MULTIPOINT') {
            $g = new KVDgis_GeomMultipoint( );
        } elseif (substr($wkt,O,7) == 'POLYGON') {
            $g = new KVDgis_GeomPolygon();
        } elseif (substr($wkt,0,12) == 'MULTIPOLYGON') {
            $g = new KVDgis_GeomMultiPolygon();
        } elseif (substr($wkt,0,10) == 'LINESTRING') {
            $g = new KVDgis_GeomLineString();
        } else {
            throw new InvalidArgumentException ('Ongeldige Well-Known Text string: ' . $wkt . "\n. Momenteel worden enkel de POINT en MULTIPOINT types ondersteund.");
        }
        $g->setGeometryFromText( $wkt );
        return $g;
    }

    /**
     * @param string $string De string waaruit geplukt moet worden.
     * @return string De tekst die zich binnen de buitenste haakjes bevindt.
     * @throws <b>InvalidArgumentException</b> - Indien de string geen haakjes bevat.
     */
    protected function getStringBetweenBraces ( $string )
    {
        $firstBrace = strpos($string,'(');
        if ($firstBrace === FALSE) {
            throw new InvalidArgumentException ('Ongeldige parameter. ' . $string . ' bevat geen openingshaakje!');    
        }
        $lastBrace = strrpos($string,')');
        if ($lastBrace === FALSE) {
            throw new InvalidArgumentException ('Ongeldige parameter. ' . $string . ' bevat geen sluitshaakje!');
        }
        $length = ( $lastBrace ) - ( $firstBrace + 1);
        return trim( substr($string,$firstBrace+1,$length) );
    }

    /**
     * Stel een Geometry in volgens de Well-Known Text standaard.
     */
    abstract public function setGeometryFromText( $wkt );
    
    /**
     * Converteer een Geometry naar de Well-Known Text standaard.
     * @return string
     */
    abstract public function getAsText();

    /**
     * Id dit een lege geometrie of niet?
     * 
     * @since   16 jul 2009
     * @return boolean
     */
    abstract public function isEmpty( );

    /**
     * __toString 
     * 
     * De WKT voorstelling van de geometry.
     * @return  string
     */
    public function __toString( )
    {
        return $this->getAsText( );
    }

}
?>
