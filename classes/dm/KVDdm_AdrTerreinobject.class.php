<?php
/**
 * @package KVD.dm
 * @subpackage Adr
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */

/**
 * KVDdm_AdrTerreinobject 
 * 
 * @package KVD.dm
 * @subpackage Adr
 * @since maart 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdm_AdrTerreinobject {
    
    /**
     * Code volgens de EPSG voor de Belge Lambert projectie.
     * @var integer
     */
    const EPSG_CODE = 31300;

    /**
     * Het soort domain-object dat wordt teruggegeven door deze mapper.
     * @var string 
     */
    const RETURNTYPE = "KVDdo_AdrTerreinObject";
    
    /**
     * @var KVDdom_Sessie;
     */
    private $_sessie;

    /**
     * @var KVDgis_Crab2Gateway;
     */
    private $_gateway;
    
    /**
     * @param KVDdom_Sessie $sessie
     */
    public function __construct ( $sessie , $parameters = array( ) )
    {
        $this->_sessie = $sessie;
        $this->_gateway = $sessie->getGateway( 'KVDgis_Crab2Gateway');
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
            $center = new KVDgis_GeomPoint ( self::EPSG_CODE , $crabData['centerX'], $crabData['centerY']);
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
     * @totdo herbekijken hoe het zit met het huisnummer
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
     */ 
    public function findById ( $id )
    {
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }
        try {
            $terreinArray = $this->_gateway->getTerreinobjectByIdentificatorTerreinobject( $id );
        } catch ( RuntimeException $e ) {
            $message = "Kon het terreinobject niet laden omdat de crab service een fout gaf:\n " . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrTerreinobject' , $id  );
        }
        
        $huisnummer = KVDdo_AdrHuisnummer::newNull( );
        
        return $this->doLoad( $id, $terreinArray, $huisnummer );
    }

    /**
     * Zoek alle terreinobjecten van een bepaald huisnummer.
     * @param KVDdo_AdrHuisnummer $huisnummer
     * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrTerreinobject} objecten
     */
    public function findByHuisnummer ( $huisnummer )
    {
        $terreinobjectenArray = $this->_gateway->listTerreinobjectenByHuisnummerId( $huisnummer->getId( ) );
        $domainObjects = array( );
        foreach ( $terreinobjectenArray as $terreinobjectArray ) {
            try {
                $terreinArray = $this->_gateway->getTerreinobjectByIdentificatorTerreinobject( $terreinobjectArray['identificatorTerreinobject'] );
            } catch ( Exception $e ) {
                continue;    
            }
            $terreinobject = $this->doLoad ( $terreinobjectArray['identificatorTerreinobject'] , $terreinArray , $huisnummer);
            $domainObjects[] = $terreinobject;
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }

}
?>
