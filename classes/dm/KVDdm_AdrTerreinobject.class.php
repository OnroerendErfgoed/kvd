<?php
/**
 * @package KVD.dm
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.dm
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since maart 2006
 */
class KVDdm_AdrTerreinobject {
    
    /**
     * Code volgens de ESPG voor de Belge Lambert projectie.
     * @var integer
     */
    const ESPG_CODE = 31370;

    /**
     * @var string 
     */
    const RETURNTYPE = "KVDdo_AdrTerreinObject";
    
    /**
     * @var KVDdom_Sessie;
     */
    private $_sessie;

    /**
     * @var KVDgis_Crab1Gateway;
     */
    private $_gateway;
    
    /**
     * @param KVDdom_Sessie $sessie
     */
    public function __construct ( $sessie )
    {
        $this->_sessie = $sessie;
        $this->_gateway = $sessie->getGateway( 'KVDgis_Crab1Gateway');
    }

    /**
     * @param integer $id
     * @param array $crabData   Een associatieve array met minimaal de sleutels identificatorTerreinobject, aardTerreinobjectCode, centerX en centerY. 
     * @return KVDdo_AdrTerreinobject
     */
    public function doLoad( $id , $crabData , $huisnummer)
    {
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }
        try {
            $center = new KVDgis_GeomPoint ( self::ESPG_CODE , $crabData['centerX'], $crabData['centerY']);
        } catch ( InvalidArgumentException $e ) {
            $center = new KVDgis_GeomPoint ( );
        }
        return new KVDdo_AdrTerreinobject ( $id,
                                            $this->_sessie,
                                            $crabData['aardTerreinobjectCode'],
                                            $huisnummer,
                                            $center
                                            );
    }

    /**
     * Zoek een terreinobject op basis van zijn id ( identificatorTerreinobject in Crab ).
     * @param string $id IdentificatorTerreinobject uit Crab.
     * @return KVDdo_AdrTerreinobjet
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> - Indien het object niet geladen kon worden.
     * @throws <b>BadMethodCallException</b> - Indien deze functie opgeroepen wordt met een Crab1 Gateway aangezien deze dit niet ondersteund.
     */ 
    public function findById ( $id )
    {
        throw new BadMethodCallException ( 'Deze methode wordt enkel ondersteund door Crab2 en is dus momenteel niet beschikbaar!');
    }

    /**
     * Zoek alle terreinobjecten van een bepaald huisnummer.
     * @param KVDdo_AdrHuisnummer $huisnummer
     * @return KVDdom_DomainObjectCollection Een verzameling van KVDdo_AdrTerreinobject objecten
     */
    public function findByHuisnummer ( $huisnummer )
    {
        $terreinobjectenArray = $this->_gateway->listTerreinobjectenByHuisnummerId( $huisnummer->getId( ) , 1);
        $domainObject = array( );
        foreach ( $terreinobjectenArray as $terreinobjectArray ) {
            $terreinobject = $this->doLoad ( $terreinobjectArray['identificatorTerreinobject'] , $terreinobjectArray , $huisnummer);
            $domainObjects[] = $terreinobject;
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }

}
?>
