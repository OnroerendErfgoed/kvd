<?php
/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.do.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDdo_AdrStraat extends KVDdom_ReadonlyDomainObject {
    
    /**
     * @var string
     */
    private $naam;

    /**
     * @var string
     */
    private $label;

    /**
     * @var KVDdo_AdrGemeente
     */
    private $_gemeente;

    /**
     * Collectie van alle huisnummers in deze straat.
     * @var KVDdom_DomainObjectCollection
     */
    private $_huisnummers;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param string $naam
     * @param string $label
     * @param KVDdo_AdrGemeente $gemeente
     * @param KVDdom_DomainObjectCollection $huisnummers
     */
    public function __construct ( $id , $sessie , $naam = 'Onbepaald', $label = 'Onbepaald', $gemeente = null, $huisnummers = null )
    {
        parent::__construct ( $id , $sessie);
        $this->naam = $naam;
        $this->label = $label;
        $this->_gemeente = ( $gemeente === null ) ? new KVDdo_AdrGemeente( 0 , $sessie) : $gemeente;
        $this->_huisnummers = ( $huisnummers === null ) ? self::PLACEHOLDER : $huisnummers;
    }

    /**
     * @return string
     */
    public function getStraatNaam( )
    {
        return $this->naam;
    }

    /**
     * @return string
     */
    public function getStraatLabel( )
    {
        return $this->label;
    }

    /**
     * @return KVDdo_AdrGemeente
     */
    public function getGemeente( )
    {
        return $this->_gemeente;
    }

    /**
     * Een collectie van KVDdo_AdrHuisnummers.
     * @return KVDdom_DomainObjectCollection
     */
    public function getHuisnummers( )
    {
        if ( $this->_huisnummers === self::PLACEHOLDER ) {
            $huisnummerMapper = $this->_sessie->getMapper( 'KVDdo_AdrHuisnummer');
            $this->_huisnummers = $huisnummerMapper->findByStraat( $this );
            
        }
        return $this->_huisnummers;    
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->label;
    }
}
?>
