<?php
/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 21 jun 2006
 */
class KVDdo_AdrDeelgemeente extends KVDdom_ReadonlyDomainObject {
    
    /**
     * @var string
     */
    protected $naam;

    /**
     * @var KVDdo_AdrGemeente
     */
    protected $gemeente;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param KVddo_AdrGemeente $gemeente
     */
    public function __construct ( $id , $sessie , $naam , $gemeente )
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->gemeente = $gemeente;
    }

    /**
     * @return string
     */
    public function getDeelgemeenteNaam( )
    {
        return $this->naam;
    }

    /**
     * @return KVDdo_AdrGemeente
     */
    public function getGemeente( )
    {
        return $this->gemeente;
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
    }

    /**
     * @param mixed $gemeente Een NullDeelgemeente kan behoren aan een geldige gemeente.
     * @return KVDdo_NullAdrDeelgemeente
     */
    public static function newNull( $gemeente = null )
    {
        return new KVDdo_NullAdrDeelgemeente( $gemeente );
    }
}

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 21 jun 2006
 */
class KVDdo_NullAdrDeelgemeente extends KVDdo_AdrDeelgemeente
{
    /**
     * @param mixed $gemeente Ofwel de gemeente waartoe de NullDeelgemeente behoort ofwel null
     */
    public function __construct( $gemeente = null )
    {
        $this->gemeente = ( is_null( $gemeente ) || !( $gemeente instanceof KVDdo_AdrGemeente ) ) ? KVDdo_AdrGemeente::newNull( ) : $gemeente; 
        $this->naam = 'Onbepaald';
        $this->id = 0;
    }

    /**
     * @return boolean
     */
    public function isNull( )
    {
        return true;
    }

    /**
     * @return string
     */
    public function getClass( )
    {
        return 'KVDdo_AdrDeelgemeente';
    }
}
?>
