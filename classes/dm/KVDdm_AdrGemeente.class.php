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
class KVDdm_AdrGemeente extends KVDdom_PDODataMapper {

    const ID = "gemeente.id";
    
    const RETURNTYPE = "KVDdo_AdrGemeente";

    const TABEL = "kvd_adr.gemeente";

    const VELDEN = "gemeente_naam, crab_id , provincie_id";

    private function getSelectStatement( )
    {
        return  "SELECT " . self::ID . ", " . self::VELDEN . " , " . KVDdm_AdrProvincie::VELDEN .
                " FROM " . self::TABEL . " LEFT JOIN " . KVDdm_AdrProvincie::TABEL . 
                " ON (" . self::TABEL . ".provincie_id = " . KVDdm_AdrProvincie::TABEL . ".id)";
    }

    protected function getFindByIdStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE " . self::ID . " = ?";
    }

    protected function getFindByCrabIdStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE crab_id = ?";
    }

    protected function getFindAllStatement( )
    {
        return $this->getSelectStatement( );
    }

    private function getFindByProvincieStatement()
    {
        return  $this->getSelectStatement( ) .
                " WHERE provincie_id = ?";
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
     * Zoek een gemeente op basis van zijn crabId.
     * @param integer $crabId Het uniek nummer voor een gemeente binnen Crab.
     * @return KVDdo_AdrGemeente
     */
    public function findByCrabId( $crabId )
    {
         $stmt = $this->_conn->prepare( $this->getFindByCrabIdStatement( ));
         $stmt->bindParam( 1, $crabId);
         $stmt->execute( );
         if ( !$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = "KVDdo_AdrGemeente met crabId $crabId kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException (  $msg , 'KVDdo_AdrGemeente' , $crabId );
         }
         return $this->doLoad( $row->id, $row);
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

        $provincieMapper = $this->_sessie->getMapper( 'KVDdo_AdrProvincie' ); 
        $provincie = $provincieMapper->doLoad( $rs->provincie_id , $rs );
        
        return new KVDdo_AdrGemeente (  $id , 
                                        $this->_sessie,
                                        $rs->gemeente_naam,
                                        $rs->crab_id,
                                        $provincie
                                        );
    }
    
    /**
     * @param KVDdo_AdrProvincie
     * @param string $orderField, Veld om op te sorteren. Kan id, gemeenteNaam of provincieNaam zijn.
     * @return KVDdom_DomainObjectCollection Een collecte van KVDdo_AdrGemeente objecten.
     */
    public function findByProvincie ( $provincie , $orderField = null)
    {
       $stmt = $this->_conn->prepare ( $this->getFindByProvincieStatement( ) . $this->getOrderClause( $orderField) );
       $id = $provincie->getId( );
       $stmt->bindParam( 1, $id );
       return $this->executeFindMany( $stmt );
    }

    private function getOrderClause ( $orderField = 'id' )
    {
        switch ( $orderField ) {
            case "id":
                $orderFieldName = 'gemeente.id';
                break;
            case "gemeenteNaam":
                $orderFieldName = 'gemeente.gemeente_naam';
                break;
            case "provincieNaam":
                $orderFieldName = 'provincie.provincie_naam';
                break;
            default:
                $orderFieldName = 'gemeente.id';
        }
        return " ORDER BY $orderFieldName ASC";
    }
}
?>
