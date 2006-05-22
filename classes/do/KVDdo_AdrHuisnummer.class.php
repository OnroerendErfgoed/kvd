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
class KVDdo_AdrHuisnummer extends KVDdom_ReadonlyDomainObject {
    
    /**
     * @var string
     */
    private $huisnummer;

    /**
     * @var KVDdo_AdrStraat
     */
    private $_straat;

    /**
     * @var integer
     */
    private $postcode;

    /**
     * Collectie van alle terreinobjecten die onder dit huisnummer vallen.
     * @var KVDdom_DomainObjectCollection
     */
    private $_percelen;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param KVDdo_AdrStraat $straat
     * @param string $huisnummer
     * @param integer $postcode
     * @param KVDdom_DomainObjectCollection $percelen
     */
    public function __construct ( $id , $sessie , $straat , $huisnummer = 'Onbepaald', $postcode = null, $percelen = null )
    {
        parent::__construct ( $id , $sessie);
        $this->huisnummer = $huisnummer;
        $this->_straat= $straat;
        $this->postcode = ( is_null( $postcode ) ) ? self::PLACEHOLDER : $postcode;
        $this->_percelen = ( is_null( $postcode ) ) ? self::PLACEHOLDER : $percelen;
    }

    /**
     * @return string
     */
    public function getHuisnummer( )
    {
        return $this->huisnummer;
    }

    /**
     * @return KVDdo_AdrStraat
     */
    public function getStraat( )
    {
        return $this->_straat;
    }

    /**
     * @return integer
     */
    public function getPostcode( )
    {
        if ( $this->postcode === self::PLACEHOLDER ) {
            $huisnummerMapper = $this->_sessie->getMapper( 'KVDdo_AdrHuisnummer');
            $this->postcode = $huisnummerMapper->findPostcodeByHuisnummer( $this );
        }
        return $this->postcode;
    }

    /**
     * Een collectie van KVDdo_AdrPercelen.
     * @return KVDdom_DomainObjectCollection
     */
    public function getPercelen( )
    {
        if ( $this->_terreinobjecten === self::PLACEHOLDER ) {
            $percelenMapper = $this->_sessie->getMapper( 'KVDdo_AdrTerreinobject');
            $this->_terreinobjecten = $percelenMapper->findByHuisnummer( $this );
            
        }
        return $this->_huisnummers;    
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return  $this->_straat->getStraatLabel( ) . " " . $this->huisnummer ;
    }
}
?>
