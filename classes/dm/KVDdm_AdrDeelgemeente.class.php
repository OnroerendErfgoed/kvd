<?php
/**
 * @package KVD.dm.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.dm.adr
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 21 jun 2006
 */
class KVDdm_AdrDeelgemeente extends KVDdom_PDODataMapper {

    const ID = "deelgemeente.id";
    
    const RETURNTYPE = "KVDdo_AdrDeelgemeente";

    const TABEL = "kvd_adr.deelgemeente";

    const VELDEN = "deelgemeente_naam, gemeente_id";

    private function getSelectStatement( )
    {
        return  "SELECT " . self::ID . ", " . self::VELDEN . " , " . KVDdm_AdrGemeente::VELDEN . " , " . KVDdm_AdrProvincie::VELDEN .
                " FROM " . self::TABEL . 
                " LEFT JOIN " . KVDdm_AdrGemeente::TABEL . 
                " ON (" . self::TABEL . ".gemeente_id = " . KVDdm_AdrGemeente::TABEL . ".id)" .
                " LEFT JOIN " . KVDdm_AdrProvincie::TABEL . 
                " ON (" . KVDdm_AdrGemeente::TABEL . ".provincie_id = " . KVDdm_AdrProvincie::TABEL . ".id)";
    }

    protected function getFindByIdStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE " . self::ID . " = ?";
    }

    protected function getFindAllStatement( )
    {
        return $this->getSelectStatement( );
    }

    private function getFindByGemeenteStatement()
    {
        return  $this->getSelectStatement( ) .
                " WHERE gemeente_id = ?";
    }

    /**
     * Zoek een gemeente op basis van zijn id.
     * @param integer $id Komt overeen met het nisnummer.
     * @return KVDdo_AdrGemeente
     */
    public function findById( $id )
    {
        return $this->abstractFindById ( self::RETURNTYPE , $id );
    }

    /**
     * @param string $orderField Kan id, gemeenteNaam of provincieNaam zijn.
     * @return KVDdom_DomainObjectCollection
     */
    public function findAll( $orderField = null )
    {
        $stmt = $this->_conn->prepare( $this->getFindAllStatement( ) . $this->getOrderClause( $orderField ));
        return $this->executeFindMany( $stmt );
    }

    /**
     * @param integer $id
     * @param Resultset $rs
     * @return KVDdo_AdrGemeente
     */
    public function doLoad( $id, $rs)
    {   
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }

        $gemeenteMapper = $this->_sessie->getMapper( 'KVDdo_AdrGemeente' ); 
        $gemeente = $gemeenteMapper->doLoad( $rs->gemeente_id , $rs );
        
        return new KVDdo_AdrDeelgemeente (  $id , 
                                            $this->_sessie,
                                            $rs->deelgemeente_naam,
                                            $gemeente
                                            );
    }
    
    /**
     * @param KVDdo_AdrGemeente
     * @param string $orderField, Veld om op te sorteren. Kan id, deelgemeenteNaam, gemeenteNaam of provincieNaam zijn.
     * @return KVDdom_DomainObjectCollection Een collecte van KVDdo_AdrDeelgemeente objecten.
     */
    public function findByGemeente ( $gemeente , $orderField = null)
    {
       $stmt = $this->_conn->prepare ( $this->getFindByGemeenteStatement( ) . $this->getOrderClause( $orderField) );
       $id = $gemeente->getId( );
       $stmt->bindParam( 1, $id );
       return $this->executeFindMany( $stmt );
    }

    private function getOrderClause ( $orderField = 'id' )
    {
        switch ( $orderField ) {
            case "id":
                $orderFieldName = 'deelgemeente.id';
                break;
            case "deelgemeenteNaam":
                $orderFieldName = 'deelgemeente.deelgemeente_naam';
                break;
            case "gemeenteNaam":
                $orderFieldName = 'gemeente.gemeente_naam';
                break;
            case "provincieNaam":
                $orderFieldName = 'provincie.provincie_naam';
                break;
            case "getrapt":
                $orderFieldName = 'provincie.provincie_naam ASC, gemeente.gemeente_naam ASC, deelgemeente.deelgemeente_naam'; 
                break;
            default:
                $orderFieldName = 'gemeente.id';
        }
        return " ORDER BY $orderFieldName ASC";
    }
}
?>
