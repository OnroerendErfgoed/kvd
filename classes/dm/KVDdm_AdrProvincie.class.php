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
 * @since 1.0.0
 */
class KVDdm_AdrProvincie extends KVDdom_PDODataMapper {

    const ID = "provincie.id";
    
    const RETURNTYPE = "KVDdo_AdrProvincie";

    const TABEL = "kvd_adr.provincie";

    const VELDEN = "provincie_naam";

    /**
     * getSelectStatement 
     * 
     * @return string
     */
    protected function getSelectStatement( )
    {
        return "SELECT " . self::ID . ", " . self::VELDEN ." FROM " . self::TABEL;
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
     * getFindByNaamStatement 
     * 
     * @return string
     */
    protected function getFindByNaamStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE UPPER(provincie_naam) = UPPER( ? )";
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
     * Zoek een provincie op basis van zijn id.
     * @param integer Komt overeen met het nisnummer.
     * @return KVDdo_AdrProvincie
     */
    public function findById( $id )
    {
        return $this->abstractFindById ( self::RETURNTYPE , $id );
    }

    /**
     * @return KVDdom_DomainObjectCollection
     */
    protected function abstractFindAll ( )
    {
        $stmt = $this->_conn->prepare( $this->getFindAllStatement( ) );
        return $this->executeFindMany( $stmt );
    }

    /**
     * @return KVDdom_DomainObjectCollection
     */
    public function findAll( )
    {
        return $this->abstractFindAll( );
    }

    /**
     * findByNaam 
     * 
     * @throws  KVDdom_DomainObjectNotFoundException
     * @return  KVDdo_AdrProvincie
     */
    public function findByNaam( $naam )
    {
         $stmt = $this->_conn->prepare( $this->getFindByNaamStatement( ));
         $stmt->bindParam( 1, $naam,PDO::PARAM_STR);
         $stmt->execute( );
         if ( !$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = "KVDdo_AdrProvincie met naam $naam kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException (  $msg , 'KVDdo_AdrProvincie' , $naam );
         }
         return $this->doLoad( $row->id, $row);
    }

    /**
     * @param integer $id
     * @param Resultset $rs
     * @return KVDdo_AdrProvincie
     */
    public function doLoad( $id, $rs)
    {   
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }

        if( $id == null && $rs->provincie_naam == null ) {
            return KVDdo_AdrProvincie::newNull( );
        }

        return new KVDdo_AdrProvincie ( $id , 
                                        $this->_sessie,
                                        $rs->provincie_naam 
                                        );
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
    
}
?>
