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
class KVDdm_AdrProvincie extends KVDdom_PDODataMapper {

    const ID = "provincie.id";
    
    const RETURNTYPE = "KVDdo_AdrProvincie";

    const TABEL = "kvd_adr.provincie";

    const VELDEN = "provincie_naam";

    private function getSelectStatement( )
    {
        return "SELECT " . self::ID . ", " . self::VELDEN ." FROM " . self::TABEL;
    }

    protected function getFindByIdStatement( )
    {
        return $this->getSelectStatement( ) . " WHERE " . self::ID . " = ?";
    }

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

        return new KVDdo_AdrProvincie ( $id , 
                                        $this->_sessie,
                                        $rs->provincie_naam 
                                        );
    }
    
}
?>
