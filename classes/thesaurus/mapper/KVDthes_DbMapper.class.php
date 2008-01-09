<?php
/**
 * @package KVD.thes
 * @subpackage mapper
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_DbMapper 
 * 
 * @package KVD.thes
 * @subpackage mapper
 * @since 17 okt 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_DbMapper implements KVDthes_IDataMapper
{
    /**
     * sessie 
     * 
     * @var KVDthes_ISessie
     */
    private $sessie;

    /**
     * __construct 
     * 
     * @param KVDdom_IReadSessie $sessie 
     * @param array $parameters 
     */
    public function __construct ( $sessie , $parameters )
    {
        $this->sessie = $sessie;
        $this->parameters = array ( 'thesaurus_id' => 0 );
        if ( !isset ( $parameters['schema'] ) ) {
            throw new KVDdom_MapperConfigurationException( 'Er is geen schema gespecifieerd voor deze thesaurus.', $this);
        }
        $this->parameters = array_merge( $this->parameters , $parameters);
    }

    /**
     * getFindByIdStatement 
     * 
     * @return string
     */
    protected function getFindByIdStatement( )
    {
        return sprintf( 'SELECT id, term FROM term WHERE id = ? AND thesaurus_id = %d' , $this->parameters['thesaurus_id'] );
    }

    /**
     * getLoadRelationsStatement 
     * 
     * @return string
     */
    protected function getLoadRelationsStatement( )
    {
        return sprintf( 'SELECT r.relation_type, t2.id AS to_id, t2.term AS to 
                        FROM term t1, relation r, term t2 
                        WHERE 
                            r.id_from = t1.id 
                            AND r.id_to = t2.id 
                            AND t1.id = ?
                            AND t1.thesaurus_id = %d', $this->parameters['thesaurus_id']);
    }

    /**
     * getFindSubTreeStatement 
     * 
     * @return string
     */
    protected function getFindSubTreeStatement( )
    {
         return sprintf( 'SELECT visitation.id, lft, rght, depth, term_id, term 
                         FROM visitation LEFT JOIN term ON ( visitation.term_id = term.id )
                         WHERE 
                            term.thesaurus_id = %d
                            AND ( lft BETWEEN ( SELECT lft FROM visitation WHERE term_id = ? LIMIT 1) 
                                AND ( SELECT rght FROM visitation WHERE term_id = ? LIMIT 1) )
                            ORDER BY lft', $this->parameters['thesarus_id']);
    }

    /**
     * getLoadScopeNoteStatement 
     * 
     * @return string
     */
    protected function getLoadScopeNoteStatement( )
    {
        return sprintf( 'SELECT scope_note FROM scope_note WHERE term_id = ? AND thesaurus_id = %d', $this->parameters['thesaurus_id']);
    }

    /**
     * findById 
     * 
     * @param integer $id 
     * @return KVDthes_Term
     * @throws KVDdom_DomainObjectNotFoundException - Indien de term niet bestaat
     */
    public function findById( $id )
    {
        $domainObject = $this->_sessie->getIdentityMap()->getDomainObject( $this->getReturnType( ), $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        $sql = $this->getFindByIdStatement( );
        $this->_sessie->getSqlLogger( )->log( $sql );
        $stmt = $this->_conn->prepare($sql);
        $stmt->bindValue(1, $id , PDO::PARAM_INT );
        $stmt->execute();
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = $this->getReturnType( ) . " met id $id kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ( $msg , $this->getReturnType( ) , $id );
        }
        return $this->doLoadRow( $id, $row);
    }

    /**
     * doLoadRow 
     * 
     * @param integer   $id 
     * @param StdClass  $row 
     * @return KVDthes_Term
     */
    private function doLoadRow( $id , $row )
    {
        $domainObject = $this->_sessie->getIdentityMap()->getDomainObject( $this->getReturnType( ), $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        $termType = $this->getReturnType( );
        return new $termType( $this->sessie , $id , $row->term , 'Nederlands' );
    }

    /**
     * loadRelations 
     * 
     * @param KVDdom_DomainObject $termObj 
     * @return KVDdom_DomainObject
     */
    public function loadRelations( KVDthes_Term $termObj )
    {
        $stmt = $this->conn->prepare( $this->getLoadRelationsStatement( ) );
        $stmt->bindValue( 1, $termObj->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $termObj->addRelation( new KVDthes_Relation( $row->relation_type, $this->findById( $row->id_to ) ) );
        }
        $termObj->setLoadState( KVDthes_Term::LS_REL);
        return $termObj;
    }

    /**
     * loadScopeNote 
     * 
     * @param KVDthes_Term $termObj 
     * @return KVDthes_Term
    */
    public function loadScopeNote( KVDthes_Term $termObj )
    {
        $stmt = $this->conn->prepare( $this->getLoadScopeNoteStatement( ) );
        $stmt->bindValue( 1, $id, PDO::PARAM_INT )
        $stmt->execute( );
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $termObj->setLoadState( KVDthes_Term::LS_SCOPENOTE );
            return $termObj;
        }
        $termObj->addScopeNote ( $row->scope_notes );
        $termObj->setLoadState( KVDthes_Term::LS_SCOPENOTE );
        return $termObj;
    }

    /**
     * loadSourceNote 
     * 
     * @todo Implementeren
     * @param KVDthes_Term $termObj 
     * @return KVDthes_Term
     */
    public function loadSourceNote( KVDthes_Term $termObj )
    {
        return $termObj;
    }

    /**
     * findSubTree 
     * 
     * @todo    Goede manier zoeken om de load state voor diepere termen correct in te stellen.
     * @param   KVDthes_Term    $termObj 
     * @return  KVDthes_Term
     * @throws  KVDdom_DomainObjectNotFoundException - Indien de term niet gevonden werd.
     */
    public function findSubTree(KVDthes_Term $termObj )
    {
         $stmt = $this->conn->prepare( $this->getFindSubTreeStatement( ) );
         $stmt->bindValue( 1, $termObj->getId( ), PDO::PARAM_INT);
         $stmt->bindValue( 1, $termObj->getId( ), PDO::PARAM_INT);
         $stmt->execute( );
         if ( !$row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $msg = $this->getReturnType( ) . " met id $id kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ( $msg , $this->getReturnType( ) , $id );
         }
         $branch = array( );
         // Algoritme gaat de lijst af en controleert of er al een object op het bovenliggende niveau zit.
         // Indien wel, dan wordt het bovenliggende object gekoppeld als de BT van het huidige.
         while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
             $current = $this->doLoadRow( $row->id, $row );
             if ( isset( $branch[$row->depth - 1]) ) {
                 $parent = $branch[$row->depth-1];
                 $current->addRelation( new KVDthes_Relation( KVDthes_Relation::REL_BT, $parent ) );
             }
             $branch[$row->depth] = $current;
         }
         $termObj->setLoadState( KVDthes_Term::LS_NT );
         return $termObj;
    }

    /**
     * getReturnType 
     * 
     * @return string
     */
    abstract protected function getReturnType( );
}
?>
