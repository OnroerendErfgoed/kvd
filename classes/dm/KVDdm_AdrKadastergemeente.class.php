<?php
/**
 * @package KVD.dm
 * @subpackage Adr
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdm_AdrKadastergemeente 
 * 
 * @package KVD.dm
 * @subpackage Adr
 * @since 31 aug 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdm_AdrKadastergemeente extends KVDdom_PDODataMapper {

    const ID = "kadastergemeente.id";
    
    const RETURNTYPE = "KVDdo_AdrKadastergemeente";

    const TABEL = "kvd_adr.kadastergemeente";

    const VELDEN = "kadastergemeente_naam, afdeling, gemeente_id";

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
                " WHERE gemeente_id = ? AND kadastergemeente_naam = ?";
    }

    /**
     * Zoek een gemeente op basis van zijn id.
     * @param integer $id Komt overeen met het nisnummer.
     * @return KVDdo_AdrKadasterGemeente
     */
    public function findById( $id )
    {
        return $this->abstractFindById ( self::RETURNTYPE , $id );
    }

    /**
     * @param string $orderField    Veld om op te sorteren. Kan id, kadastergemeenteNaam, gemeenteNaam, provincieNaam of getrapt zijn.
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
     * @return KVDdo_AdrKadasterGemeente
     */
    public function doLoad( $id, $rs)
    {   
        $domainObject = $this->_sessie->getIdentityMap( )->getDomainObject( self::RETURNTYPE, $id);
        if ( $domainObject !== null ) {
            return $domainObject;
        }

        $gemeenteMapper = $this->_sessie->getMapper( 'KVDdo_AdrGemeente' ); 
        $gemeente = $gemeenteMapper->doLoad( $rs->gemeente_id , $rs );
        
        return new KVDdo_AdrKadastergemeente (  $id , 
                                                $this->_sessie,
                                                $rs->afdeling,
                                                $rs->kadastergemeente_naam,
                                                $gemeente
                                                );
    }
    
    /**
     * @param KVDdo_AdrGemeente $gemeente
     * @param string $orderField    Veld om op te sorteren. Kan id, kadastergemeenteNaam, gemeenteNaam, provincieNaam of getrapt zijn.
     * @return KVDdom_DomainObjectCollection Een collecte van KVDdo_AdrKadastergemeente objecten.
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
     * @param string $naam Naam van de kadastergemeente
     * @return KVDdo_AdrKadastergemeente
     */
    public function findByNaam ( $gemeente , $naam )
    {
        $stmt = $this->_conn->prepare( $this->getFindByNaamStatement( ) );
        $id = $gemeente->getId( );
        $stmt->bindParam( 1, $id, PDO::PARAM_INT );
        $stmt->bindParam( 2, $naam, PDO::PARAM_STR );
        $stmt->execute( );
        if ( !$row = $stmt->fetch( PDO::FETCH_OBJ )) {
           $msg = "KVDdo_AdrKadastergemeente met naam $naam kon niet gevonden worden";
           throw new KVDdom_DomainObjectNotFoundException (  $msg , 'KVDdo_AdrKadastergemeente' , $naam );
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
     * getOrderClause 
     * 
     * @param string $orderField 
     * @return string
     */
    private function getOrderClause ( $orderField = 'id' )
    {
        switch ( $orderField ) {
            case "id":
                $orderFieldName = 'kadastergemeente.id';
                break;
            case "afdeling":
                $orderFieldName = 'kadastergemeente.afdeling';
                break;
            case "kadastergemeenteNaam":
                $orderFieldName = 'kadastergemeente.kadastergemeente_naam';
                break;
            case "gemeenteNaam":
                $orderFieldName = 'gemeente.gemeente_naam';
                break;
            case "provincieNaam":
                $orderFieldName = 'provincie.provincie_naam';
                break;
            case "getrapt":
                $orderFieldName = 'provincie.provincie_naam ASC, gemeente.gemeente_naam ASC, kadastergemeente.kadastergemeente_naam'; 
                break;
            default:
                $orderFieldName = 'gemeente.id';
        }
        return " ORDER BY $orderFieldName ASC";
    }
}
?>
