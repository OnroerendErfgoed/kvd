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
class KVDdo_AdrTerreinobject extends KVDdom_ReadonlyDomainObject {
    
    /**
     * De IdentificatorTerreinobject uit CRAB
     * @var string
     */
    private $id;

    /**
     * Een geometrie die het centrum van het terreinobject vormt.
     * @var KVDgis_GeomPoint
     */
    private $_center;

    /**
     * @param string $id
     * @param KVDdom_Sessie $sessie
     * @param KVDgis_GeomPoint $center
     */
    public function __construct ( $id , $sessie , $center = null)
    {
        parent::__construct ( $id , $sessie);
        $this->_center = ( $center === null ) ? self::PLACEHOLDER : $center;
    }

    /**
     * @return string De identificatorTerreinobject uit CRAB.
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * @return KVDgis_GeomPoint
     */
    public function getCenter( )
    {
        if ( $this->_center === self::PLACEHOLDER ) {
            $huisnummerMapper = $this->_sessie->getMapper( 'KVDdo_AdrHuisnummer');
            $this->_center = $huisnummerMapper->findCenterTerreinobjectByTerreinobject( $this )
        }
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
