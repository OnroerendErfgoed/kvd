<?php
/**
 * @package     KVD.gis
 * @subpackage  geometry
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @version     $Id$
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDgis_GeomGeometry
 *
 * Abstracte class die de basis is voor alle geometry objecten.
 *
 * @package     KVD.gis
 * @subpackage  geometry
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @since       jan 2006
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDgis_GeomGeometry
{
    /**
     * @var integer $srid Spatial Referencing System Identifier
     */
    private $srid;

    
    /**
     * @var boolean geeft aan of de reguliere expresses voor de types al geladen zijn
     */
    protected $RE_LOADED = false;
    /**
     * @var string reguliere expressie voor een float getal met (optioneel) een + of - teken
     */
    protected $RE_SIGNED;
    /**
     * @var string reguliere expressie voor een punt
     */
    protected $RE_POINT;
    /**
     * @var string reguliere expressie voor een linestring
     */
    protected $RE_LINESTRING;
    /**
     * @var string reguliere expressie voor een polygoon
     */
    protected $RE_POLYGON;
    /**
     * @var string reguliere expressie voor een multi polygoon
     */
    protected $RE_MULTIPOLYGON;
    
    
    /**
     * @param integer $srid Spatial Referencing System Identifier
     */
    public function __construct( $srid = -1)
    {
        $this->setSrid ( $srid );
        if(!$this->RE_LOADED) $this->initRegEx();
    }

    /**
     * initRegEx
     * Laadt de reguliere expressies in de class (static) variabelen.
     * @var void
     * @return void
     */
    public function initRegEx()
    {
        $this->RE_LOADED = true;
        // Syntax: {<sign>}<digit>*{<punt><digit>*}
        // Bijvoorbeeld: +0.235
        // Bijvoorbeeld: 2
        // Bijvoorbeeld: -5.2
        $this->RE_SIGNED = "(-|\+)?\d+(\.\d+)?";
        // Syntax: <signed numeric literal> <signed numeric literal>
        // Bijvoorbeeld: +0.235 -5.2
        $this->RE_POINT = $this->RE_SIGNED."\s+".$this->RE_SIGNED; 
        // Syntax: '(' <point> { ',' <point>}* ')'
        // Bijvoorbeeld: (+0.235 -5.2, 2 -5)
        $this->RE_LINESTRING = "\(\s*(".$this->RE_POINT."(\s*,\s*".$this->RE_POINT.")*)\s*\)";
        // Syntax: '(' <linestring> { ',' <linestring>}* ')'
        // Bijvoorbeeld: ((+0.235 -5.2, 2 -5), (0.2 -5, 2 -15))
        $this->RE_POLYGON = "\(\s*(".$this->RE_LINESTRING."\s*(,\s*".$this->RE_LINESTRING.")*)\s*\)";
        // Syntax: '(' <polygon> { ',' <polygon>}* ')'
        // Bijvoorbeeld: (((+0.235 -5.2, 2 -5), (0.2 -5, 2 -15)), ((+12.235 -6.2, 3 -1), (0.9 -7, 3 -10)))
        $this->RE_MULTIPOLYGON = "\(\s*".$this->RE_POLYGON."\s*(,\s*".$this->RE_POLYGON.")*\s*\)";    
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
        } elseif (substr($wkt,0,7) == 'POLYGON') {
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
