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
 * @since maart 2006
 */
class KVDdo_AdrGemeente extends KVDdom_ReadonlyDomainObject {
    
    /**
     * Het id dat voor de crab-webservice gebruikt wordt.
     * @var integer
     */
    protected $crabId;

    /**
     * @var string
     */
    protected $naam;

    /**
     * @var KVDdo_AdrProvincie
     */
    protected $provincie;

    /**
     * Collectie van alle straten in deze gemeente.
     * @var KVDdom_DomainObjectCollection
     */
    protected $straten;

    /**
     * Collectie van alle deelgemeenten in deze gemeente.
     * @var KVDdom_DomainObjectCollection
     */
    protected $deelgemeenten;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param integer $crabId
     * @param KVddo_AdrProvincie $provincie
     * @param KVDdom_DomainObjectCollection $straten
     * @param KVDdom_DomainObjectCollection $deelgemeenten
     */
    public function __construct ( $id , $sessie , $naam = 'Onbepaald', $crabId = 0, $provincie = null, $straten = null, $deelgemeenten = null)
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->crabId = $crabId;
        $this->provincie = ( $provincie === null ) ? new KVDdo_AdrProvincie( 0 , $sessie) : $provincie;
        $this->straten = ( $straten === null ) ? self::PLACEHOLDER : $straten;
        $this->deelgemeenten = ( $deelgemeenten === null ) ? self::PLACEHOLDER : $deelgemeenten;
    }

    /**
     * @return string
     */
    public function getGemeenteNaam( )
    {
        return $this->naam;
    }

    /**
     * @return integer
     */
    public function getCrabId( )
    {
        return $this->crabId;
    }

    /**
     * @return KVDdo_AdrProvincie
     */
    public function getProvincie( )
    {
        return $this->provincie;
    }

    /**
     * Een collectie van KVDdo_AdrStraten.
     * @return KVDdom_DomainObjectCollection
     */
    public function getStraten( )
    {
        if ( $this->straten === self::PLACEHOLDER ) {
            $stratenMapper = $this->_sessie->getMapper( 'KVDdo_AdrStraat');
            $this->straten = $stratenMapper->findByGemeente( $this );
            
        }
        return $this->straten;    
    }

    /**
     * Een collectie van KVDdo_Deelgemeente objecten.
     * @return KVDdom_DomainObjectCollection
     */
    public function getDeelgemeenten( )
    {
        if ( $this->deelgemeenten === self::PLACEHOLDER ) {
            $mapper = $this->_sessie->getMapper( 'KVDdo_AdrDeelgemeente' );
            $this->deelgemeenten = $mapper->findByGemeente( $this , 'deelgemeenteNaam');
        }
        return $this->deelgemeenten;
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
    }

    /**
     * @return KVDdo_NullAdrGemeente
     */
    public static function newNull( )
    {
        return new KVDdo_NullAdrGemeente( );
    }
}

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 25 jul 2006
 */
class KVDdo_NullAdrGemeente extends KVDdo_AdrGemeente
{
    /**
     * @param KVDdo_AdrProvincie
     */
    public function __construct ( $provincie = null ) 
    {
        $this->provincie = ( $provincie === null ) ? KVDdo_AdrProvincie::newNull() : $provincie;
        $this->naam = 'Onbepaald';
        $this->crabId = 0;
        $this->id = 0;
        $this->straten = new KVDdom_DomainObjectCollection( array( ) );
        $this->deelgemeenten = new KVDdom_DomainObjectCollection( array( ) );
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
        return 'KVDdo_AdrGemeente';
    }
}
?>
