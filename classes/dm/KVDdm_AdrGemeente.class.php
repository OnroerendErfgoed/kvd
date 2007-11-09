<?php
/**
 * @package KVD.dm
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.dm
 * @subpackage Adr
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since maart 2006
 */
class KVDdm_AdrGemeente extends KVDdom_PDODataMapper {

    const ID = "gemeente.id";
    
    const RETURNTYPE = "KVDdo_AdrGemeente";

    const TABEL = "kvd_adr.gemeente";

    const VELDEN = "gemeente_naam, crab_id , provincie_id";

    /**
     * getSelectStatement 
     * 
     * @return string
     */
    protected function getSelectStatement( )
    {
        return  "SELECT " . self::ID . ", " . self::VELDEN . " , " . KVDdm_AdrProvincie::VELDEN .
                " FROM " . self::TABEL . " LEFT JOIN " . KVDdm_AdrProvincie::TABEL . 
                " ON (" . self::TABEL . ".provincie_id = " . KVDdm_AdrProvincie::TABEL . ".id)";
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
     * getFindByCrabIdStatement 
     * 
     * @return string
     */
    protected function getFindByCrabIdStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE crab_id = ?";
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
     * getFindByProvincieStatement 
     * 
     * @return string
     */
    private function getFindByProvincieStatement()
    {
        return  $this->getSelectStatement( ) .
                " WHERE provincie_id = ?";
    }

    /**
     * getFindByNaamStatement 
     * 
     * @return string
     */
    private function getFindByNaamStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE gemeente_naam = ?";
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
     * @param string $orderDirection Kan omlaag of omhoog zijn.
     * @return KVDdom_DomainObjectCollection
     */
    public function findAll( $orderField = null , $orderDirection = null )
    {
        $stmt = $this->_conn->prepare( $this->getFindAllStatement( ) . $this->getOrderClause( $orderField , $orderDirection ) );
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

        if ( $id = null && $rs->gemeente_naam == null ) {
            return new KVDdo_NullAdrGemeente( $provincie );
        }
        
        return new KVDdo_AdrGemeente (  $id , 
                                        $this->_sessie,
                                        $rs->gemeente_naam,
                                        $rs->crab_id,
                                        $provincie
                                        );
    }
    
    /**
     * @param KVDdo_AdrProvincie
     * @param string $orderField Veld om op te sorteren. Kan id, gemeenteNaam of provincieNaam zijn.
     * @param string $orderDirection Kan omlaag of omhoog zijn.
     * @return KVDdom_DomainObjectCollection Een collecte van KVDdo_AdrGemeente objecten.
     */
    public function findByProvincie ( $provincie , $orderField = null , $orderDirection = null )
    {
       $stmt = $this->_conn->prepare ( $this->getFindByProvincieStatement( ) . $this->getOrderClause( $orderField , $orderDirection ) );
       $id = $provincie->getId( );
       $stmt->bindParam( 1, $id );
       return $this->executeFindMany( $stmt );
    }

    /**
     * Zoek een gemeente op basis van zijn naam. Deze moet natuurlijk correct geschreven zijn.
     * @param string $naam
     * @return KVDdo_AdrGemeente
     */
    public function findByNaam( $naam )
    {
         $stmt = $this->_conn->prepare( $this->getFindByNaamStatement( ));
         $stmt->bindParam( 1, $naam,PDO::PARAM_STR);
         $stmt->execute( );
         if ( !$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = "KVDdo_AdrGemeente met naam $naam kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException (  $msg , 'KVDdo_AdrGemeente' , $naam );
         }
         return $this->doLoad( $row->id, $row);
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

    /**
     * @param string orderField Veld waarop gesorteerd moet worden.
     * @return string
     */
    private function getOrderClause ( $orderField = 'gemeenteNaam' , $orderDirection = 'omlaag' )
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
                $orderFieldName = 'gemeente.gemeente_naam';
        }
        $orderDirection = ( $orderDirection == 'omlaag' ) ? 'DESC' : 'ASC';
        return " ORDER BY $orderFieldName $orderDirection";
    }
}
?>
