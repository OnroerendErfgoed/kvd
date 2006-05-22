<?php
/**
 * @package KVD.dm.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.dm.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdm_AdrStraat {
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
     * @param array $crabData   Een associatieve array met minimaal de sleutel straatnaam, straatnaamLabel. 
     *                          Indien er geen KVDdo_AdrGemeente wordt meegegeven als derde parameter moet er ook een sleutel gemeenteId aanwezig zijn.
     *                          Deze sleutel is niet de nisGemeenteCode maar de crabId.
     * @param KVDdo_AdrGemeente
     * @return KVDdo_AdrStraat
     */
    public function doLoad( $id , $crabData , $gemeente = null)
    {
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
     * @throws <b>KVDdom_DomainObjectNotFoundException</b> - Indien het object niet geladen kon worden.
     */ 
    public function findById ( $id )
    {
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
     * @return KVDdom_DomainObjectCollection Een verzameling van KVDdo_AdrStraat objecten
     */
    public function findByGemeente ( $gemeente )
    {
        $stratenArray = $this->_gateway->listStraatnamenByGemeenteId( $gemeente->getCrabId( ) , 2);
        $domainObject = array( );
        foreach ( $stratenArray as $straatArray ) {
            $straat = $this->doLoad ( $straatArray['straatnaamId'] , $straatArray , $gemeente);
            $domainObjects[] = $straat;
        }
        return new KVDdom_DomainObjectCollection ( $domainObjects );
    }

}
?>
