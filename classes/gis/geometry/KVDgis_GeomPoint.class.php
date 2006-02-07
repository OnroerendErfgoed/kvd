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
class KVDgis_GeomPoint extends KVDgis_GeomGeometry
{
    /**
     * @var integer
     */
    private $x;

    /**
     * @var integer
     */
    private $y;

    /**
     * @param integer $srid
     * @param integer $x
     * @param integer $y
     */
    public function __construct ( $srid = -1, $x= 0, $y=0)
    {
        $this->setSrid($srid);
        $this->setX($x);
        $this->setY($y);
    }
    
    /**
     * @param integer $x
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @param integer $y
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    /**
     * @return integer
     */
    public function getX()
    {
        return $this->x;    
    }

    /**
     * @return integer
     */
    public function getY()
    {
        return $this->y;    
    }

    /**
     * @see KVDgis_GeomGeometry::setGeometryFromText()
     * @param string $wkt
     */
    public function setGeometryFromText ( $wkt )
    {
        if (substr($wkt,0,5) != 'POINT') {
            throw new Exception ('Ongeldige Well-Known Text string: ' . $wkt . "\n. De string zou moeten beginnen met 'POINT'.");
        }
        
        $stringPoint = $this->getStringBetweenBraces($wkt);
        $points = explode(" " , $stringPoint);
        $this->setX($points['0']);
        $this->setY($points['1']);
    }

    /**
     * @see KVDgis_GeomGeometry::getAsText()
     * @return string
     */
    public function getAsText()
    {
        return "POINT({$this->x} {$this->y})";
    }
}
?>
