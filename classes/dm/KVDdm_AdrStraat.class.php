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
 * KVDdm_AdrStraat 
 * 
 * @package KVD.dm
 * @subpackage Adr
 * @since maart 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdm_AdrStraat {
    
    /**
     * Het soort domain-object dat deze mapper teruggeeft. 
     */
    const RETURNTYPE = "KVDdo_AdrStraat";
    
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
     * @param array $crabData   Een associatieve array met minimaal de sleutel straatnaam, straatnaamLabel. 
     *                          Indien er geen KVDdo_AdrGemeente wordt meegegeven als derde parameter moet er ook een sleutel gemeenteId aanwezig zijn.
     *                          Deze sleutel is niet de nisGemeenteCode maar de crabId.
     * @param KVDdo_AdrGemeente
     * @return KVDdo_AdrStraat
     */
    public function doLoad( $id , $crabData , $gemeente = null)
    {
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }
        
        if ( is_null( $gemeente ) ) {
            $gemeenteMapper = $this->_sessie->getMapper( 'KVDdo_AdrGemeente');
            $gemeente = $gemeenteMapper->findByCrabId( $crabData['gemeenteId']);
        }
        return new KVDdo_AdrStraat (    $id,
                                        $this->_sessie,
                                        $crabData['straatnaam'],
                                        $crabData['straatnaamLabel'],
                                        $gemeente,
                                        null
                                        );
    }

    /**
     * Zoek een straat op basis van zijn id.
     * @param integer $id Id van de straat, dit is een nummer dat toegewezen werd door crab.
     * @return KVDdo_AdrStraat
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
     */ 
    public function findById ( $id )
    {
        $domainObject = $this->_sessie->getIdentityMap()->getDomainObject( self::RETURNTYPE , $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        try {
            $straatArray = $this->_gateway->getStraatnaamByStraatnaamId( $id );
        } catch ( RuntimeException $e ) {
            $message = 'Kon een straat niet laden. Waarschijnlijk is het id ongeldig.';
            $message .= "\nDe Crab-Gateway gaf de volgende foutmelding: " . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrStraat' , $id );
        } catch ( SoapFault $e ) {
            $message = "Kon een straat niet laden omdat de crab service een fout gaf:\n" . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrStraat' , $id );
        }
        return $this->doLoad( $id , $straatArray);
    }

    /**
     * Zoek alle straten in een bepaalde gemeente.
     * @param KVDdo_AdrGemeente $gemeente
     * @return KVDdom_DomainObjectCollection Een verzameling van {@link KVDdo_AdrStraat} objecten
     */
    public function findByGemeente ( $gemeente )
    {
        $stratenArray = $this->_gateway->listStraatnamenByGemeenteId( $gemeente->getCrabId( ) );
        $domainObjects = array( );
        foreach ( $stratenArray as $straatArray ) {
            $straat = $this->doLoad ( $straatArray['straatnaamId'] , $straatArray , $gemeente);
            $domainObjects[] = $straat;
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }

    /**
     * Zoek een straat op basis van zijn naam en de gemeente waarin de straat ligt.
     * @param KVDdo_AdrGemeente $gemeente
     * @return KVDdo_AdrStraat
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> Indien het object niet geladen kon worden.
     */
    public function findByNaam ( $gemeente, $naam )
    {
        try {
            $straatArray = $this->_gateway->getStraatnaamByStraatnaam( $naam , $gemeente->getCrabId( ) );
        } catch ( RuntimeException $e ) {
            $message = 'Kon een straat niet laden. Waarschijnlijk is de straatnaam ongeldig.';
            $message .= "\nDe Crab-Gateway gaf de volgende foutmelding: " . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrStraat' , $naam );
        } catch ( SoapFault $e ) {
            $message = "Kon een straat niet laden omdat de crab service een fout gaf:\n" . $e->getMessage( );
            throw new KVDdom_DomainObjectNotFoundException ( $message , 'KVDdo_AdrStraat' , $id );
        }
        return $this->doLoad( $straatArray['straatnaamId'] , $straatArray, $gemeente );
    }

}
?>
