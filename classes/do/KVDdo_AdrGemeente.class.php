<?php
/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdo_AdrGemeente extends KVDdom_ReadonlyDomainObject {
    
    /**
     * Het id dat voor de crab-webservice gebruikt wordt.
     * @var integer
     */
    private $crabId;

    /**
     * @var string
     */
    private $naam;

    /**
     * @var KVDdo_AdrProvincie
     */
    private $_provincie;

    /**
     * Collectie van alle straten in deze gemeente.
     * @var KVDdom_DomainObjectCollection
     */
    private $_straten;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param integer $crabId
     * @param KVddo_AdrProvincie $provincie
     * @param KVDdom_DomainObjectCollection $straten
     */
    public function __construct ( $id , $sessie , $naam = 'Onbepaald', $crabId = 0, $provincie = null, $straten = null )
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->crabId = $crabId;
        $this->_provincie = ( $provincie === null ) ? new KVDdo_AdrProvincie( 0 , $sessie) : $provincie;
        $this->_straten = ( $straten === null ) ? self::PLACEHOLDER : $straten;
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
        return $this->_provincie;
    }

    /**
     * Een collectie van KVDdo_AdrStraten.
     * @return KVDdom_DomainObjectCollection
     */
    public function getStraten( )
    {
        if ( $this->_straten === self::PLACEHOLDER ) {
            $stratenMapper = $this->_sessie->getMapper( 'KVDdo_AdrStraat');
            $this->_straten = $stratenMapper->findByGemeente( $this );
            
        }
        return $this->_straten;    
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->naam;
    }
}
?>
