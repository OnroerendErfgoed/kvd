<?php

/**
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * Abstracte class die een dimensie voorstelt, met ingebouwde convertor en lijst van afkortingen.
 *
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
abstract class KVDutil_Dimensie
{
    /**
     * @var integer
     */
    private $dimensie;

    /**
     * @var string
     */
    private $dimensieSoort;

    /**
     * @var KVDutil_DimensieConvertor
     */
    private static $_dimensieConvertor;

    /**
     * @var KVDutil_DimensieNaamAfkorting
     */
    private static $_dimensieNaamAfkorting;

    /**
     * @param integer $dimensieMaat Een getal dat de afmeting voorstel
     * @param string $dimensieMaat De maat van de afmeting ( cm, mm, gr, ...)
     * @param string $dimensieSoort Het soort afmeting ( lengte, gewicht)
     */
    public function __construct ( $dimensie , $dimensieMaat , $dimensieSoort )
    {
        if ( !isset (self::$_dimensieConvertor) ) {
            self::$_dimensieConvertor = new KVDutil_DimensieConvertor();     
        }
        if ( !isset (self::$_dimensieNamenEnAfkortingen) ) {
            self::$_dimensieNaamAfkorting = new KVDutil_DimensieNaamAfkorting();     
        }
        $doelDimensie = $this->getDimensieMaat( );
        $this->dimensie = self::$_dimensieConvertor->convertDimensie( $dimensie , $dimensieMaat , $doelDimensie );
        $this->dimensieSoort = $dimensieSoort;
    }

    /**
     * @return integer
     */
    public function getDimensie()
    {
        return $this->dimensie;
    }

    /**
     * @return string
     */
    public function getDimensieSoort()
    {
        return $this->dimensieSoort;
    }

    /**
     * @return string
     */
    abstract public function getDimensieMaat();

    /**
     * @return string
     */
    public function getOmschrijving()
    {
        $afkorting = self::$_dimensieNaamAfkorting->convertDimensieNaamNaarAfkorting ( $this->dimensieSoort );
        return $afkorting . ": " . $this->dimensie . $this->getDimensieMaat();
    }
    
}
?>
