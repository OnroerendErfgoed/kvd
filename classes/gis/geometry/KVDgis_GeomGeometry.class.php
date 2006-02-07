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
     */
    protected function getStringBetweenBraces ( $string )
    {
        $firstBrace = strpos($string,'(');
        if ($firstBrace === FALSE) {
            throw new Exception ('Ongeldige parameter. ' . $string . ' bevat geen openingshaakje!');    
        }
        $lastBrace = strrpos($string,')');
        if ($lastBrace === FALSE) {
            throw new Exception ('Ongeldige parameter. ' . $string . ' bevat geen sluitshaakje!');
        }
        return substr($string,$firstBrace+1,$lastBrace-1);
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

}
?>
