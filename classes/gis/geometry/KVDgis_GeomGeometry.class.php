<?php
/**
 * @package KVD.gis.geometry
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.gis.geometry
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
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
     * @return string;
     */
    abstract public function getAsText();

    /**
     * __toString 
     * 
     * De WKT voorstelling van de geometry.
     * @return string
     */
    public function __toString( )
    {
        return $this->getAsText( );
    }

}
?>
