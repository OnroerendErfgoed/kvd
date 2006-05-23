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
    public function __construct ( $srid = -1, $x= 0, $y=0)
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
        if ( !is_numeric( $x ) ) {
            throw new InvalidArgumentException( "$x is geen geldig nummer en kan dus geen punt in een geometry zijn!" );
        }
        $this->x = $x;
    }

    /**
     * @param number $y
     * @throws InvalidArgumentException - Indien $x geen integer of float is.
     */
    public function setY($y)
    {
        if ( !is_numeric( $y ) ) {
            throw new InvalidArgumentException( "$y is geen geldig nummer en kan dus geen punt in een geometry zijn!" );
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
        if (substr($wkt,0,5) != 'POINT') {
            throw new InvalidArgumentException ('Ongeldige Well-Known Text string: ' . $wkt . "\n. De string zou moeten beginnen met 'POINT'.");
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
