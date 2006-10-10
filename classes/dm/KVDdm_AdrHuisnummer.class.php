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
 * KVDdm_AdrHuisnummer 
 * 
 * @package KVD.dm
 * @subpackage Adr
 * @since maart 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdm_AdrHuisnummer {
    
    /**
     * Het soort domain-objects dat deze mapper teruggeeft. 
     */
    const RETURNTYPE = "KVDdo_AdrHuisnummer";
    
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
    public function __construct ( $sessie )
    {
        $this->_sessie = $sessie;
        $this->_gateway = $sessie->getGateway( 'KVDgis_Crab2Gateway');
    }

    /**
     * @param integer $id
     * @param array $crabData   Een associatieve array met minimaal de sleutel huisnummer en huisnummerId. 
     *                          Indien er geen KVDdo_AdrStraat wordt meegegeven als derde parameter moet er ook een sleutel straatnaamId aanwezig zijn.
     * @param KVDdo_AdrStraat
     * @return KVDdo_AdrHuisnummer
     */
    public function doLoad( $id , $crabData , $straat = null)
    {
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }
        
        if ( is_null( $straat ) ) {
            $straatMapper = $this->_sessie->getMapper( 'KVDdo_AdrStraat');
            $straat = $straatMapper->findById( $crabData['straatnaamId']);
        }
        return new KVDdo_AdrHuisnummer (    $id,
                                            $this->_sessie,
                                            $straat,
                                            $crabData['huisnummer']
                                            );
    }

    /**
     * Zoek een huisnummer op basis van zijn id.
     * @param integer $id Id van het huisnummer, dit is een nummer dat toegewezen werd door crab.
     * @return KVDdo_AdrHuisnummer
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
     */ 
    public function findById ( $id )
    {
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }

        try {
            $huisnummerArray = $this->_gateway->getHuisnummerByHuisnummerId( $id );
        } catch ( RuntimeException $e ) {
            $message = 'Kon een huisnummer niet laden. Waarschijnlijk is het id ongeldig.';
            $message .= "\nDe Crab-Gateway gaf de volgende foutmelding: " . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrHuisnummer' , $id );
        } catch ( SoapFault $e ) {
            $message = "Kon een huisnummer niet laden omdat de crab service een fout gaf:\n" . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrHuisnummer' , $id );
        }
        return $this->doLoad( $id , $huisnummerArray);
    }

    /**
     * Zoek een huisnummer op basis van zijn huisnummer. Dit is een tekstuele voorstelling van het huisnummer met inbegrip van eventuele bis-waarden. Tevens is het crabId van de straat nodig.
     * @param string $huisnummer Het huisnummer volgens Crab.
     * @param integer $straatId Het crabId van de straat.
     * @return KVDdo_AdrHuisnummer
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
     */ 
    public function findByHuisnummer ( $huisnummer , $straatId )
    {
        try {
            $huisnummerArray = $this->_gateway->getHuisnummerByHuisnummer( $huisnummer , $straatId );
        } catch ( RuntimeException $e ) {
            $message = 'Kon een huisnummer niet laden. Waarschijnlijk is het huisnummer ongeldig.';
            $message .= "\nDe Crab-Gateway gaf de volgende foutmelding: " . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrHuisnummer' , $huisnummer );
        } catch ( SoapFault $e ) {
            $message = "Kon een huisnummer niet laden omdat de crab service een fout gaf:\n" . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrHuisnummer' , $huisnummer );
        }
        return $this->doLoad( $huisnummerArray['huisnummerId'] , $huisnummerArray);
    }

    /**
     * Zoek alle huisnummers in een bepaalde straat.
     * @param KVDdo_AdrStraat $straat
     * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrHuisnummer} objecten.
     */
    public function findByStraat ( $straat )
    {
        $huisnummerArray = $this->_gateway->listHuisnummersByStraatnaamId( $straat->getId( ) );
        $domainObjects = array( );
        foreach ( $huisnummerArray as $huisnummerArray ) {
            $huisnummer = $this->doLoad ( $huisnummerArray['huisnummerId'] , $huisnummerArray , $straat);
            $domainObjects[] = $huisnummer;
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }

    /**
     * findPostCodeByHuisnummer 
     * 
     * @param KVDdo_AdrHuisnummer $huisnummer 
     * @return integer De postkantonCode van het huisnummer.
     */
    public function findPostCodeByHuisnummer( $huisnummer )
    {
        try {
            $postkantonArray = $this->_gateway->getPostkantonByHuisnummerId ( $huisnummer->getId( ) );
        } catch ( RuntimeException $e ) {
            return 'Onbepaald';
        } 
        return $postkantonArray['postkantonCode'];
    }

}
?>
