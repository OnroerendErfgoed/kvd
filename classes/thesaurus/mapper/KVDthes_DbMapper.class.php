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
     * @var KVDdom_IReadSessie
     */
    protected $sessie;

    /**
     * conn 
     * 
     * @var PDO
     */
    protected $conn;

    /**
     * __construct 
     * 
     * @param KVDdom_IReadSessie $sessie 
     * @param array $parameters 
     */
    public function __construct ( $sessie , $parameters )
    {
        $this->sessie = $sessie;
        $this->conn = $sessie->getDatabaseConnection( get_class($this) );
        $this->initialize( $parameters );
    }

    /**
     * initialize 
     * 
     * @param array $parameters 
     * @throws KVDdom_MapperConfigurationException Indien er een parameter niet gespecifieerd werd.
     * @return void
     */
    protected function initialize( array $parameters )
    {
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
        return sprintf( 'SELECT id, term, language FROM %s.term WHERE id = ? AND thesaurus_id = %d' , $this->parameters['schema'], $this->parameters['thesaurus_id'] );
    }

    /**
     * getFindAllStatement 
     * 
     * @return string
     */
    protected function getFindAllStatement( )
    {
        return sprintf( 'SELECT id, term, language FROM %s.term WHERE thesaurus_id = %d' , $this->parameters['schema'], $this->parameters['thesaurus_id'] );
    }

    /**
     * getFindRootStatement 
     * 
     * @return string
     */
    protected function getFindRootStatement( )
    {
        return sprintf( 'SELECT t.id AS id, term, language 
                         FROM %s.visitation v LEFT JOIN %s.term t ON ( v.term_id = t.id and v.thesaurus_id = t.thesaurus_id )
                         WHERE 
                            v.thesaurus_id = %d
                            AND v.depth = 1',
                         $this->parameters['schema'],
                         $this->parameters['schema'],
                         $this->parameters['thesaurus_id'] );
    }

    /**
     * getLoadRelationsStatement 
     * 
     * @return string
     */
    protected function getLoadRelationsStatement( )
    {
        return sprintf( 'SELECT r.relation_type, t2.id AS id_to, t2.term as term, t2.language as language 
                        FROM %s.term t1 
                            LEFT JOIN %s.relation r ON ( t1.id = r.id_from AND t1.thesaurus_id = r.thesaurus_id ) 
                            LEFT JOIN %s.term t2 ON ( r.id_to=t2.id AND r.thesaurus_id=t2.thesaurus_id)
                        WHERE 
                            t1.id = ?
                            AND t1.thesaurus_id = %d',
                        $this->parameters['schema'],
                        $this->parameters['schema'],
                        $this->parameters['schema'],
                        $this->parameters['thesaurus_id']);
    }

    /**
     * getFindSubTreeStatement 
     * 
     * @return string
     */
    protected function getFindSubTreeStatement( )
    {
         return sprintf( 'SELECT v.id, lft, rght, depth, term_id, term, language 
                         FROM %s.visitation v LEFT JOIN %s.term t ON ( v.term_id = t.id AND v.thesaurus_id = t.thesaurus_id )
                         WHERE 
                            t.thesaurus_id = %d
                            AND ( lft BETWEEN ( SELECT lft FROM %s.visitation WHERE thesaurus_id = v.thesaurus_id AND term_id = ? LIMIT 1) 
                                AND ( SELECT rght FROM %s.visitation WHERE thesaurus_id = v.thesaurus_id AND term_id = ? LIMIT 1) )
                            ORDER BY lft',
                         $this->parameters['schema'],
                         $this->parameters['schema'],
                         $this->parameters['schema'],
                         $this->parameters['schema'],
                         $this->parameters['thesarus_id']);
    }

    /**
     * getLoadScopeNoteStatement 
     * 
     * @return string
     */
    protected function getLoadNotesStatement( )
    {
        return sprintf( 'SELECT scope_note, source_note FROM %s.notes WHERE term_id = ? AND thesaurus_id = %d', 
                        $this->parameters['schema'],
                        $this->parameters['thesaurus_id']);
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
        $domainObject = $this->sessie->getIdentityMap()->getDomainObject( $this->getReturnType( ), $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        $sql = $this->getFindByIdStatement( );
        $this->sessie->getSqlLogger( )->log( $sql );
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id , PDO::PARAM_INT );
        $stmt->execute();
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = $this->getReturnType( ) . " met id $id kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ( $msg , $this->getReturnType( ) , $id );
        }
        return $this->doLoadRow( $id, $row);
    }

    /**
     * findRoot 
     * 
     * @return KVDthes_Term
     */
    public function findRoot( )
    {
        $this->sessie->getSqlLogger( )->log( $this->getFindRootStatement( ) );
        $stmt = $this->conn->prepare($this->getFindRootStatement( ));
        $stmt->execute();
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = "De root van " . $this->getReturnType( ) . " kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ( $msg , $this->getReturnType( ), null );
        }
        return $this->doLoadRow( $row->id, $row);
    }

    /**
     * findAll 
     * 
     * @return KVDdom_DomainObjectCollection
     */
    public function findAll( )
    {
        $stmt = $this->conn->prepare( $this->getFindAllStatement( ) );
        $stmt->execute( );
        $termen = array( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $termen[$row->id] = $this->doLoadRow( $row->id, $row );
        }
        return new KVDdom_DomainObjectCollection( $termen );
    }

    /**
     * doLoadRow 
     * 
     * @param integer   $id 
     * @param StdClass  $row 
     * @return KVDthes_Term
     */
    protected function doLoadRow( $id , $row )
    {
        $domainObject = $this->sessie->getIdentityMap()->getDomainObject( $this->getReturnType( ), $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        $termType = $this->getReturnType( );
        return new $termType( $this->sessie , $id , $row->term , $row->language );
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
            $termObj->addRelation( new KVDthes_Relation( $row->relation_type, $this->doLoadRow( $row->id_to, $row ) ) );
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
        return $this->loadNotes( $termObj);
    }

    /**
     * loadNotes 
     * 
     * Deze methode zal alle notes ( zowel scope als source) van een term laden.
     * @param KVDthes_Term $termObj 
     * @return KVDthes_Term
     */
    private function loadNotes( KVDthes_Term $termObj )
    {
        $stmt = $this->conn->prepare( $this->getLoadNotesStatement( ) );
        $stmt->bindValue( 1, $termObj->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $termObj->setLoadState( KVDthes_Term::LS_SCOPENOTE );
            return $termObj;
        }
        $termObj->addScopeNote ( $row->scope_note );
        $termObj->setLoadState( KVDthes_Term::LS_SCOPENOTE );
        $termObj->addSourceNote ( $row->source_note );
        $termObj->setLoadState( KVDthes_Term::LS_SOURCENOTE );
        return $termObj;
    }

    /**
     * loadSourceNote 
     * 
     * @param KVDthes_Term $termObj 
     * @return KVDthes_Term
     */
    public function loadSourceNote( KVDthes_Term $termObj )
    {
        return $this->loadNotes( $termObj);
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
     * Voor een query uit en geef de resultaten terug als een collectie van domainobjects.
     * Het type van deze collectie kan meegegeven worden als parameter maar moet een subklasse
     * zijn van de KVDdom_DomainObjectCollection.
     * @param PDOStatement      $stmt
     * @param string	        $collectiontype het type van de collectie van domainobjects.
     * @return KVDdom_DomainObjectCollection
     */
    protected function executeFindMany ( $stmt , $collectiontype = "KVDdom_DomainObjectCollection" )
    {
        if(($collectiontype != "KVDdom_DomainObjectCollection") && (!is_subclass_of($collectiontype, "KVDdom_DomainObjectCollection"))) {
            throw new InvalidArgumentException("type moet een subtype zijn van KVDdom_DomainObjectCollection. Gegeven: $collectiontype");
        }
		$stmt->execute( );
		$domainObjects = array ( );
		while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
			$domainObjects[$row->id] = $this->doLoadRow ( $row->id , $row );
		}
		return new $collectiontype( $domainObjects );
	}

    /**
     * getReturnType 
     * 
     * @return string
     */
    abstract protected function getReturnType( );
}
?>
