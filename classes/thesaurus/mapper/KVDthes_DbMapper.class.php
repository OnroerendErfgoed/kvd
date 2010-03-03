<?php
/**
 * @package     KVD.thes
 * @subpackage  mapper
 * @version     $Id$
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_DbMapper 
 * 
 * @package     KVD.thes
 * @subpackage  mapper
 * @since       17 okt 2007
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_DbMapper implements KVDthes_IDataMapper
{
    /**
     * sessie 
     * 
     * @var KVDdom_IWriteSessie
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
     * @param KVDdom_IWriteSessie $sessie 
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
        $this->parameters = array ( 'thesaurus_id' => 0, 'thesaurus_naam' => 'Onbepaalde Thesaurus', 'thesaurus_taal' => 'nl-BE');
        if ( !isset ( $parameters['schema'] ) ) {
            throw new KVDdom_MapperConfigurationException( 'Er is geen schema gespecifieerd voor deze thesaurus.', $this);
        }
        if ( !isset ( $parameters['id_seq_naam'] ) ) {
            throw new KVDdom_MapperConfigurationException( 'Er is geen naam van een sequentie gespecifieerd voor deze thesaurus.', $this);
        }
        $this->parameters = array_merge( $this->parameters , $parameters);
    }

    protected function getSelectStatement( )
    {
        return sprintf( 'SELECT t.id, term, tt.id AS type_id, tt.name AS type_naam, qualifier, language, sort_key 
                         FROM %s.term t 
                            LEFT JOIN %s.term_type_code tt ON ( t.type = tt.id )
                         WHERE thesaurus_id = %d' , $this->parameters['schema'], $this->parameters['schema'], $this->parameters['thesaurus_id'] );
    }

    /**
     * getFindByIdStatement 
     * 
     * @return string SQL Statement
     */
    protected function getFindByIdStatement( )
    {
        return $this->getSelectStatement( ) . ' AND t.id = ?';
    }

    /**
     * getFindByNaamStatement 
     * 
     * @return string SQL Statement
     */
    protected function getFindByNaamStatement( )
    {
        return $this->getSelectStatement( ) . ' AND lower(term) = lower(?)';
    }

    /**
     * getFindByQualifiedNaamStatement 
     * 
     * @return string SQL Statement
     */
    protected function getFindByQualifiedNaamStatement( )
    {
        return $this->getFindByNaamStatement( ) . ' AND lower(qualifier) = lower(?)';
    }


    /**
     * getFindSubTreeIdStatement 
     * 
     * @return string
     */
    protected function getFindSubTreeIdStatement( )
    {
         return sprintf( 'SELECT term_id 
                         FROM %s.visitation v 
                         WHERE 
                            v.thesaurus_id = %d
                            AND ( lft BETWEEN ( SELECT lft FROM %s.visitation WHERE thesaurus_id = v.thesaurus_id AND term_id = ?) 
                                        AND ( SELECT rght FROM %s.visitation WHERE thesaurus_id = v.thesaurus_id AND term_id = ?) )
                            ORDER BY lft',
                         $this->parameters['schema'],
                         $this->parameters['thesaurus_id'],
                         $this->parameters['schema'],
                         $this->parameters['schema']
                         );
    }

    /**
     * getFindAllStatement 
     * 
     * @return string
     */
    protected function getFindAllStatement( )
    {
        return $this->getSelectStatement( ) . ' ORDER BY term, qualifier';
    }

    /**
     * getFindRootStatement 
     * 
     * @return string
     */
    protected function getFindRootStatement( )
    {
        return $this->getSelectStatement( ) . ' AND t.type = \'HR\' LIMIT 1';
    }

    /**
     * getLoadRelationsStatement 
     * 
     * @return string
     */
    protected function getLoadRelationsStatement( )
    {
        return sprintf( 'SELECT r.relation_type, t2.id AS id_to, t2.term AS term, tt.id AS type_id, tt.name AS type_naam, t2.qualifier AS qualifier, t2.language AS language, t2.sort_key AS sort_key 
                        FROM %s.term t1 
                            INNER JOIN %s.relation r ON ( t1.id = r.id_from AND t1.thesaurus_id = r.thesaurus_id ) 
                            INNER JOIN %s.term t2 ON ( r.id_to=t2.id AND r.thesaurus_id=t2.thesaurus_id)
                            INNER JOIN %s.term_type_code tt ON ( t2.type = tt.id )
                        WHERE 
                            t1.id = ?
                            AND t1.thesaurus_id = %d',
                        $this->parameters['schema'],
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
         return sprintf( 'SELECT v.id, lft, rght, depth, term_id, term, qualifier, language, sort_key 
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
        return sprintf( 'SELECT scope_note, source_note, indexing_note, history_note FROM %s.notes WHERE term_id = ? AND thesaurus_id = %d', 
                        $this->parameters['schema'],
                        $this->parameters['thesaurus_id']);
    }

    /**
     * getDeleteVisitationStatement 
     * 
     * @since   19 apr 2009
     * @return  string  SQL Statement
     */
    public function getDeleteVisitationStatement( )
    {
        return sprintf( 'DELETE FROM %s.visitation WHERE thesaurus_id = %d', $this->parameters['schema'], $this->parameters['thesaurus_id'] );
    }

    /**
     * getInsertVisitationStatement 
     * 
     * @since   19 apr 2009
     * @return  string   SQL Statement
     */
    public function getInsertVisitationStatement( )
    {
        return sprintf( 'INSERT INTO %s.visitation VALUES ( default, ?, ?, %d, ?, ?)', $this->parameters['schema'], $this->parameters['thesaurus_id'] );
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
     * findByNaam 
     * 
     * @author  Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
     * @author  Dieter Standaert <dieter.standaert@eds.com>
     * @param   string $naam                    Zoek een term op basis van de naam. Case insensitive.
     * @return  KVDdom_DomainObjectCollection   Alle termen die aan deze naam voldoen.
     */
    public function findByNaam( $naam )
    {
        if(preg_match("#^([\w\h]+)\h*\(([\w\h]+)\)\h*$#", $naam, $matches)) {
					$stmt = $this->conn->prepare( $this->getFindByQualifiedNaamStatement());
          $stmt->bindValue(1, trim($matches[1]) , PDO::PARAM_STR );
          $stmt->bindValue(2, trim($matches[2]) , PDO::PARAM_STR );					
				} else {
					$stmt = $this->conn->prepare( $this->getFindByNaamStatement());
          $stmt->bindValue(1, $naam , PDO::PARAM_STR );
				}
        $stmt->execute();
        $termen = array( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $termen[$row->id] = $this->doLoadRow( $row->id, $row );
        }
        return new KVDdom_DomainObjectCollection( $termen );
    }

    /**
     * findRoot 
     * 
     * @return KVDthes_Term
     */
    public function findRoot( )
    {
        $stmt = $this->conn->prepare($this->getFindRootStatement( ));
        $stmt->execute();
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = "De root van " . $this->getReturnType( ) . " kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ( $msg , $this->getReturnType( ), null );
        }
        return $this->doLoadRow( $row->id, $row);
    }

    /**
     * findSubTreeId 
     * 
     * Zoek de id's van alle termen die een NT zijn van een bepaalde term en het id van de term zelf.
     * @return array    Een array met ten minste de id van de term zelf en indien de term NTs heeft ook hun ids.
     */
    public function findSubTreeId( KVDthes_Term $term )
    {
        $stmt = $this->conn->prepare( $this->getFindSubTreeIdStatement( ) );
        $stmt->bindValue(1, $term->getId( ) , PDO::PARAM_INT );
        $stmt->bindValue(2, $term->getId( ) , PDO::PARAM_INT );
        $stmt->execute();
        $res = $stmt->fetchAll( PDO::FETCH_COLUMN );
        if ( count( $res ) < 1 ) {
            $res[] = $term->getId( );
        }
        return $res;
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
     * findByCriteria 
     * 
     * @param KVDdb_Criteria $c
     * @return KVDdom_DomainObjectCollection
     */
    public function findByCriteria( KVDdb_Criteria $c )
    {
        if ( $c->count( ) == 0 ) {
            return $this->findAll( );
        }
        
        // criterion toevoegen dat enkel termen uit deze thesaurus toont.
        $c->add( KVDdb_Criterion::equals( 'thesaurus_id', $this->parameters['thesaurus_id'] ) );
        $c->addAscendingOrder( 'term' );
        $c->addAscendingOrder( 'qualifier' );
        
        $sql = sprintf( 'SELECT t.id, term, tt.id AS type_id, tt.name AS type_naam, qualifier, language, sort_key 
                         FROM %s.term t 
                            LEFT JOIN %s.term_type_code tt ON ( t.type = tt.id )' , 
                        $this->parameters['schema'], $this->parameters['schema'] );
        
        $sql .= $c->generateSql( KVDdb_Criteria::MODE_PARAMETERIZED );
        $stmt = $this->conn->prepare( $sql );
        $values = $c->getValues();
        for ( $i = 0; $i<count( $values); $i++) {
            $stmt->bindValue( $i+1, $values[$i] );
        }
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
        $returnType = $this->getReturnType( );
        $thesaurus = $this->doLoadThesaurus( );
        $termType = new KVDthes_TermType( $row->type_id, $row->type_naam );
        return new $returnType( $id, $this->sessie, $row->term, $termType, $row->qualifier, $row->language, $row->sort_key, null, $thesaurus);
    }

    /**
     * doLoadThesaurus 
     * 
     * @return KVDthes_Thesaurus
     */
    protected function doLoadThesaurus( )
    {
        $domainObject = $this->sessie->getIdentityMap()->getDomainObject( 'KVDthes_Thesaurus', $this->parameters['thesaurus_id'] );
        if ($domainObject != null) {
            return $domainObject;
        }
        return new KVDthes_Thesaurus( $this->sessie, $this->parameters['thesaurus_id'], $this->parameters['thesaurus_naam'], $this->parameters['thesaurus_taal'] );
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
            $termObj->loadRelation( new KVDthes_Relation( $row->relation_type, $this->doLoadRow( $row->id_to, $row ) ) );
        }
        $termObj->setLoadState( KVDthes_Term::LS_REL);
        return $termObj;
    }

    /**
     * loadNotes 
     * 
     * Deze methode zal alle notes (scope, source, indexing, history) van een term laden.
     * @param   KVDthes_Term $termObj 
     * @return  KVDthes_Term
     */
    public function loadNotes( KVDthes_Term $termObj )
    {
        $stmt = $this->conn->prepare( $this->getLoadNotesStatement( ) );
        $stmt->bindValue( 1, $termObj->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $termObj->setLoadState( KVDthes_Term::LS_NOTES );
            return $termObj;
        }
        $notes = array (    'scopeNote'     => $row->scope_note,
                            'sourceNote'    => $row->source_note,
                            'indexingNote'  => $row->indexing_note,
                            'historyNote'   => $row->history_note );
        $termObj->loadNotes( $notes );
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
     * @return  KVDthes_Term
     */
    public function create ( )
    {
        $id = $this->getIdFromSequence( $this->parameters['schema'] . '.' . $this->parameters['id_seq_naam'] );
        $termType = $this->getReturnType( );
        return call_user_func( $termType . '::create', $termType, $id, $this->sessie, $this->doLoadThesaurus( ) );
    }

    /**
     * @param   string  $sequenceName
     * @return  integer $id
     */
    protected function getIdFromSequence( $sequenceName )
    {
        $stmt = $this->conn->query( "SELECT nextval ( '$sequenceName' )" );
        return $stmt->fetchColumn( );
    }

    /**
     * getReturnType 
     * 
     * @return string
     */
    abstract protected function getReturnType( );

    /**
     * bindValues 
     * 
     * @param   PDOStatement    $stmt 
     * @param   integer         $nextIndex 
     * @param   KVDthes_Term    $term 
     * @return  integer         Volgende te gebruiken index.
     */
    protected function bindValues( $stmt, $nextIndex, $term )
    {
        $stmt->bindValue ( $nextIndex++, $term->getTerm( ) , PDO::PARAM_STR );
        $stmt->bindValue ( $nextIndex++, $term->getType( )->getId( ) , PDO::PARAM_STR );
        $stmt->bindValue ( $nextIndex++, $term->getLanguage( ) , PDO::PARAM_STR );
        $stmt->bindValue ( $nextIndex++, $term->getQualifier( ) , PDO::PARAM_STR );
        $stmt->bindValue ( $nextIndex++, $term->getSortKey( ) , PDO::PARAM_STR );
        return $nextIndex;
    }

    /**
     * insert 
     * 
     * @param   KVDthes_Term $term 
     * @return  KVDthes_Term
     */
    public function insert( KVDthes_Term $term )
    {
        $sql = sprintf( 'INSERT INTO %s.term (thesaurus_id, id, term, type, language, qualifier, sort_key ) VALUES ( %s, ?, ?, ?, ?, ?, ? )', $this->parameters['schema'], $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare(  $sql );
        $stmt->bindValue(  1, $term->getId(  ) , PDO::PARAM_INT );
        $this->bindValues(  $stmt , 2, $term );
        $stmt->execute( );

        $this->insertNotes( $term );

        $this->insertRelations( $term );

        return $term;
    }

    /**
     * @param   KVDthes_Term    $term
     * @return  KVDthes_Term    
     */
    public function update ( KVDthes_Term $term ) 
    {
        $sql = sprintf( 'UPDATE %s.term SET 
                            term = ?,
                            type = ?,
                            language = ?,
                            qualifier = ?,
                            sort_key = ?
                          WHERE thesaurus_id = %s AND id = ?', $this->parameters['schema'], $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare(  $sql );
        $nextIndex = $this->bindValues(  $stmt , 1 , $term );
        $stmt->bindValue(  $nextIndex , $term->getId(  ) , PDO::PARAM_INT );
        $stmt->execute(  );

        $this->deleteNotes( $term );
        $this->insertNotes( $term );

        $this->deleteRelations( $term );
        $this->insertRelations( $term );
        
        return $term;
    }

    /**
     * delete 
     * 
     * @param   KVDthes_Term $term 
     * @return  KVDthes_Term
     */
    public function delete( KVDthes_Term $term )
    {
        $sql = sprintf( 'DELETE FROM %s.term WHERE thesaurus_id = %s AND id = ?', $this->parameters['schema'], $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue( 1, $term->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
        return $term;
    }

    /**
     * deleteNotes 
     * 
     * @since   3 mrt 2010
     * @param   KVdthes_Term $term 
     * @return  void
     */
    protected function deleteNotes( KVDthes_Term $term )
    {
        $sql = sprintf( 'DELETE FROM %s.notes WHERE thesaurus_id = %s AND term_id = ?', 
                        $this->parameters['schema'], 
                        $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue( 1, $term->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
    }
    
    /**
     * insertNotes 
     * 
     * @param   KVDthes_Term $term 
     * @return  void
     */
    protected function insertNotes( KVDthes_Term $term )
    {
        $sql = sprintf( 'INSERT INTO %s.notes 
                        ( thesaurus_id, term_id, scope_note, source_note, indexing_note, history_note ) 
                        VALUES ( %s, ?, ?, ?, ?, ?)', 
                        $this->parameters['schema'], 
                        $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare(  $sql );
        $stmt->bindValue(  1, $term->getId(  ) , PDO::PARAM_INT );
        $stmt->bindValue ( 2, $term->getScopeNote( ) , PDO::PARAM_STR );
        $stmt->bindValue ( 3, $term->getSourceNote( ) , PDO::PARAM_STR );
        $stmt->bindValue ( 4, $term->getIndexingNote( ) , PDO::PARAM_STR );
        $stmt->bindValue ( 5, $term->getHistoryNote( ) , PDO::PARAM_STR );
        $stmt->execute(  );
        return $term;
    }

    /**
     * deleteRelations 
     * 
     * @param   KVDthes_Term    $term
     * @return  void
     */
    private function deleteRelations( KVDthes_Term $term )
    {
        $sql = sprintf( 'DELETE FROM %s.relation WHERE thesaurus_id = %s AND id_from = ?', $this->parameters['schema'], $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare ($sql );
        $stmt->bindValue ( 1, $term->getId( ) , PDO::PARAM_INT );
        $stmt->execute();
    }

    /**
     * insertRelations 
     * 
     * @param   KVDthes_Term $term
     * @return  void
     */
    private function insertRelations( KVDthes_Term $term )
    {
        $sql = sprintf( 'INSERT INTO %s.relation ( thesaurus_id, id_from, relation_type, id_to) VALUES ( %s, ?, ?, ?)', $this->parameters['schema'], $this->parameters['thesaurus_id'] );
        if ( count( $term->getRelations( ) ) > 0 ) {
            $stmt = $this->conn->prepare(  $sql );
            $stmt->bindValue(  1 , $term->getId(), PDO::PARAM_INT );
            foreach ( $term->getRelations( ) as $rel ) {
                $stmt->bindValue( 2, $rel->getType(  ) , PDO::PARAM_STR );
                $stmt->bindValue( 3, $rel->getTerm( )->getId( ), PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    /**
     * findAllTermTypes 
     * 
     * @return KVDdom_DomainObjectCollection
     */
    public function findAllTermTypes( )
    {
        $sql = sprintf( 'SELECT id, name FROM %s.term_type_code', $this->parameters['schema'] );
        $stmt = $this->conn->prepare( $sql );
        $stmt->execute( );
        $arr = array( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $arr[] = new KVDthes_TermType( $row->id, $row->name );    
        }
        return new KVDdom_DomainObjectCollection( $arr );
    }

    /**
     * findTermTypeById 
     * 
     * @param   string $id 
     * @return  KVDthes_TermType
     */
    public function findTermTypeById( $id )
    {
        $sql = sprintf( 'SELECT id, name FROM %s.term_type_code WHERE id = ?', $this->parameters['schema'] );
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue( 1, $id, PDO::PARAM_STR );
        $stmt->execute( );
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = "KVDthes_TermType met id $id kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ( $msg , "KVDthes_TermType", $id );
        }
        return new KVDthes_TermType( $row->id, $row->name );
    }
}
?>
