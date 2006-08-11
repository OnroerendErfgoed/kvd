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
     * @return KVDdo_NullAdrDeelgemeente
     */
    public static function newNull( )
    {
        return new KVDdo_NullAdrDeelgemeente( );
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
    public function __construct( )
    {
        $this->gemeente = KVDdo_AdrGemeente::newNull( );
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
