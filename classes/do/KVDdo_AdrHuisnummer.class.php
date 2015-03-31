<?php
/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since maart 2006
 */
class KVDdo_AdrHuisnummer extends KVDdom_ReadonlyDomainObject {

    /**
     * @var string
     */
    protected $huisnummer;

    /**
     * @var KVDdo_AdrStraat
     */
    protected $straat;

    /**
     * @var integer
     */
    protected $postcode;

    /**
     * Collectie van alle terreinobjecten die onder dit huisnummer vallen.
     * @var KVDdom_DomainObjectCollection
     */
    protected $terreinobjecten;

    /**
     * @param integer $id
     * @param KVDdom_Sessie $sessie
     * @param KVDdo_AdrStraat $straat
     * @param string $huisnummer
     * @param integer $postcode
     * @param KVDdom_DomainObjectCollection $percelen
     */
    public function __construct ( $id , $sessie , $straat , $huisnummer, $postcode = null, $percelen = null )
    {
        parent::__construct ( $id , $sessie);
        $this->huisnummer = $huisnummer;
        $this->straat= $straat;
        $this->postcode = ( is_null( $postcode ) ) ? self::PLACEHOLDER : $postcode;
        $this->terreinobjecten = ( is_null( $postcode ) ) ? self::PLACEHOLDER : $percelen;
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
        return $this->straat;
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
        if ( $this->terreinobjecten === self::PLACEHOLDER ) {
            $percelenMapper = $this->_sessie->getMapper( 'KVDdo_AdrTerreinobject');
            $this->terreinobjecten = $percelenMapper->findByHuisnummer( $this );

        }
        return $this->terreinobjecten;
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return  $this->straat->getStraatLabel( ) . " " . $this->huisnummer ;
    }

    /**
     * @return KVDdo_NullAdrHuisnummer
     */
    public static function newNull( )
    {
        return new KVDdo_NullAdrHuisnummer( );
    }
}

/**
 * @package KVD.do
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDdo_NullAdrHuisnummer extends KVDdo_AdrHuisnummer
{
    public function __construct ( )
    {
        $this->id = 0;
        $this->huisnummer = 'Onbepaald';
        $this->straat = KVDdo_AdrStraat::newNull( );
        $this->postcode = null;
        $this->terreinobjecten = new KVDdom_DomainObjectCollection( );
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
        return 'KVDdo_AdrHuisnummer';
    }
}
?>
