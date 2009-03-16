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
 * @since 21 jun 2006
 */
class KVDdm_AdrDeelgemeente extends KVDdom_PDODataMapper {

    const ID = "deelgemeente.id";
    
    const RETURNTYPE = "KVDdo_AdrDeelgemeente";

    const TABEL = "kvd_adr.deelgemeente";

    const VELDEN = "deelgemeente_naam, gemeente_id";

    /**
     * getSelectStatement 
     * 
     * @return string
     */
    protected function getSelectStatement( )
    {
        return  "SELECT " . self::ID . ", " . self::VELDEN . " , " . KVDdm_AdrGemeente::VELDEN . " , " . KVDdm_AdrProvincie::VELDEN .
                " FROM " . self::TABEL . 
                " LEFT JOIN " . KVDdm_AdrGemeente::TABEL . 
                " ON (" . self::TABEL . ".gemeente_id = " . KVDdm_AdrGemeente::TABEL . ".id)" .
                " LEFT JOIN " . KVDdm_AdrProvincie::TABEL . 
                " ON (" . KVDdm_AdrGemeente::TABEL . ".provincie_id = " . KVDdm_AdrProvincie::TABEL . ".id)";
    }

    /**
     * getFindByIdStatement 
     * 
     * @return string
     */
    protected function getFindByIdStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE " . self::ID . " = ?";
    }

    /**
     * getFindAllStatement 
     * 
     * @return string
     */
    protected function getFindAllStatement( )
    {
        return $this->getSelectStatement( );
    }

    /**
     * getFindAllByNaamStatement 
     * 
     * @since   13 mrt 2009
     * @return  string
     */
    protected function getFindAllByNaamStatement( )
    {
        return $this->getSelectStatement( ) . ' WHERE UPPER( deelgemeente_naam ) = UPPER( ? )';
    }

    /**
     * getFindByGemeenteStatement 
     * 
     * @return string
     */
    private function getFindByGemeenteStatement()
    {
        return  $this->getSelectStatement( ) .
                " WHERE gemeente_id = ?";
    }

    /**
     * getFindByNaamStatement 
     * 
     * @return string
     */
    private function getFindByNaamStatement( )
    {
        return  $this->getSelectStatement( ) .
                " WHERE gemeente_id = ? AND deelgemeente_naam = ?";
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

        if ( $id == null && $rs->deelgemeente_naam == null ) {
            return new KVDdo_NullAdrDeelgemeente( $gemeente );
        }
        
        return new KVDdo_AdrDeelgemeente (  $id , 
                                            $this->_sessie,
                                            $rs->deelgemeente_naam,
                                            $gemeente
                                            );
    }
    
    /**
     * @param KVDdo_AdrGemeente $gemeente
     * @param string $orderField, Veld om op te sorteren. Kan id, deelgemeenteNaam, gemeenteNaam of provincieNaam zijn.
     * @return KVDdom_DomainObjectCollection Een collecte van KVDdo_AdrDeelgemeente objecten.
     */
    public function findByGemeente ( $gemeente , $orderField = null)
    {
       $stmt = $this->_conn->prepare ( $this->getFindByGemeenteStatement( ) . $this->getOrderClause( $orderField) );
       $id = $gemeente->getId( );
       $stmt->bindParam( 1, $id , PDO::PARAM_INT );
       return $this->executeFindMany( $stmt );
    }

    /**
     * @param KVDdo_AdrGemeente $gemeente Een gemeente object
     * @param string $naam Naam van de deelgemeente
     * @return KVDdo_AdrDeelgemeente
     */
    public function findByNaam ( $gemeente , $naam )
    {
        $stmt = $this->_conn->prepare( $this->getFindByNaamStatement( ) );
        $id = $gemeente->getId( );
        $stmt->bindParam( 1, $id, PDO::PARAM_INT );
        $stmt->bindParam( 2, $naam, PDO::PARAM_STR );
        $stmt->execute( );
        if ( !$row = $stmt->fetch( PDO::FETCH_OBJ )) {
           $msg = "KVDdo_AdrDeelgemeente met naam $naam kon niet gevonden worden";
           throw new KVDdom_DomainObjectNotFoundException (  $msg , 'KVDdo_AdrDeelgemeente' , $naam );
        }
        return $this->doLoad( $row->id, $row);
    }

    /**
     * findAllByNaam 
     * 
     * Zoek alle deelgemeenten met een bepaalde naam.
     * @param   string $naam 
     * @return  KVDdom_DomainObjectCollection
     */
    public function findAllByNaam( $naam )
    {
       $stmt = $this->_conn->prepare ( $this->getFindAllByNaamStatement( ) );
       $stmt->bindParam( 1, $naam , PDO::PARAM_STR );
       return $this->executeFindMany( $stmt );
    }

    /**
     * findByCriteria 
     *
     * @since 30 okt 2007
     * @param KVDdb_Criteria $criteria 
     * @return void
     */
    public function findByCriteria ( KVDdb_Criteria $criteria )
    {
        $sql = $this->getSelectStatement( ) . ' ' . $criteria->generateSql( KVDdb_Criteria::MODE_PARAMETERIZED );
        $stmt = $this->_conn->prepare( $sql );
        $values = $criteria->getValues( );
        for ( $i=0 ; $i<count( $values ) ; $i++ ) {
            $stmt->bindValue( $i+1 , $values[$i] );
        }
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
