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
class KVDdo_AdrTerreinobject extends KVDdom_ReadonlyDomainObject {
    
    /**
     * De IdentificatorTerreinobject uit CRAB
     * @var string
     */
    protected $id;

    /**
     * Geeft aan om welk soort terreinobject het gaat.
     * @var string
     */
    private $aardTerreinobjectCode;

    /**
     * Het huisnummer waarbij dit terreinobject hoort.
     * @var KVDdo_AdrHuisnummer
     */
    private $_huisnummer;

    /**
     * Een geometrie die het centrum van het terreinobject vormt.
     * @var KVDgis_GeomPoint
     */
    private $_center;

    /**
     * @param string $id Het identificatorTerreinobject uit Crab
     * @param KVDdom_Sessie $sessie
     * @param string $terreinobjectCode Het soort terreinobject
     * @param KVDdo_AdrHuisnummer Het huisnummer waartoe dit terreinobject hoort.
     * @param KVDgis_GeomPoint $center De centroide van het terreinObject.
     */
    public function __construct ( $id , $sessie , $aardTerreinobjectCode , $huisnummer , $center )
    {
        parent::__construct ( $id , $sessie);
        $this->aardTerreinobjectCode = $aardTerreinobjectCode;
        $this->_huisnummer = $huisnummer;
        $this->_center = $center;
    }

    /**
     * @return string De identificatorTerreinobject uit CRAB.
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getAardTerreinObject( )
    {
        return $this->aardTerreinobjectCode;
    }

    /**
     * @return KVDdo_AdrHuisnummer
     */
    public function getHuisnummer( )
    {
        return $this->_huisnummer;
    }

    /**
     * @return KVDgis_GeomPoint
     */
    public function getCenter( )
    {
        /*
        if ( $this->_center === self::PLACEHOLDER ) {
            $terreinobjectMapper = $this->_sessie->getMapper( 'KVDdo_AdrTerreinobject');
            $this->_center = $terreinobjectMapper->findCenterTerreinobjectByTerreinobject( $this )
        }
        */
        return $this->_center;
    }

    /**
     * @return string
     */
    public function getOmschrijving( )
    {
        return  $this->id;
    }
}
?>
