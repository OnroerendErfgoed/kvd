<?php
/**
 * @package     KVD.thes
 * @subpackage  mapper
 * @version     $Id$
 * @copyright   2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Een mapper voor KVDthes_Concept objecten.
 * 
 * @package     KVD.thes
 * @subpackage  mapper
 * @since       1.6
 * @copyright   2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_DbConceptMapper implements KVDdom_IDataMapper, KVDthes_IMatchableMapper
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
        $this->parameters = array ( 'thesaurus_id' => 0, 
                                    'thesaurus_naam' => 'Onbepaalde Thesaurus', 
                                    'thesaurus_korte_naam' => null,
                                    'thesaurus_taal' => 'nl-BE');
        if ( !isset ( $parameters['schema'] ) ) {
            throw new KVDdom_MapperConfigurationException( 'Er is geen schema gespecifieerd voor deze thesaurus.', $this);
        }
        if ( !isset ( $parameters['do_class_finder'] ) ) {
            throw new KVDdom_MapperConfigurationException( 
                'Er is geen functie gespecifieerd die voor een bepaalde thesaurus id de mapper kan vinden.', $this);
        }
        $this->parameters = array_merge( $this->parameters , $parameters);
    }

    /**
     * getDomainObjectClass
     *
     * @param  integer $thesaurus Id of naam van een thesaurus.
     * @return string|false Naam van het domainobject of false indien het niet gekend is.    
     */
    public function getDomainObjectClass( $thesaurus )
    {
        return call_user_func( $this->parameters['do_class_finder'], $thesaurus );
    }

    protected function getSelectStatement( )
    {
        return sprintf( 'SELECT c.id, c.term 
                         FROM %s.concept c 
                         WHERE c.thesaurus_id = %d' , 
                         $this->parameters['schema'], 
                         $this->parameters['thesaurus_id'] );
    }

    /**
     * getFindByIdStatement 
     * 
     * @return string SQL Statement
     */
    protected function getFindByIdStatement( )
    {
        return $this->getSelectStatement( ) . ' AND c.id = ?';
    }

    /**
     * getFindAllStatement 
     * 
     * @return string
     */
    protected function getFindAllStatement( )
    {
        return $this->getSelectStatement( ) . ' ORDER BY id';
    }

    /**
     * getLoadMatchesStatement 
     * 
     * @return string
     */
    protected function getLoadMatchesStatement( )
    {
        return sprintf( 
            'SELECT m.concept_match_type AS match_type, t.thesaurus_id as term_thesaurus_id, t.id AS term_id, t.term AS term, tt.id AS type_id, tt.name AS type_naam, t.qualifier AS qualifier, t.language AS language, t.sort_key AS sort_key 
             FROM %s.concept c
                INNER JOIN %s.match m ON (c.id = m.concept_id AND c.thesaurus_id = m.concept_thesaurus_id ) 
                INNER JOIN %s.term t ON (m.term_id = t.id AND m.term_thesaurus_id = t.thesaurus_id)
                INNER JOIN %s.term_type_code tt ON (t.type = tt.id)
             WHERE 
                c.id = ?
                AND c.thesaurus_id = %d',
            $this->parameters['schema'],
            $this->parameters['schema'],
            $this->parameters['schema'],
            $this->parameters['schema'],
            $this->parameters['thesaurus_id']);
    }

    /**
     * findById 
     * 
     * @param integer $id 
     * @return KVDthes_Concept
     * @throws KVDdom_DomainObjectNotFoundException - Indien het concept niet bestaat
     */
    public function findById( $id )
    {
        $domainObject = $this->sessie
                             ->getIdentityMap()
                             ->getDomainObject($this->getReturnType( ), $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        $sql = $this->getFindByIdStatement( );
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(1, $id , PDO::PARAM_INT );
        $stmt->execute();
        if (!$row = $stmt->fetch( PDO::FETCH_OBJ )) {
            $msg = $this->getReturnType( ) . " met id $id kon niet gevonden worden";
            throw new KVDdom_DomainObjectNotFoundException ($msg, $this->getReturnType( ), $id );
        }
        return $this->doLoad( $id, $row);
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
        $concepten = array( );
        while ( $row = $stmt->fetch( PDO::FETCH_OBJ ) ) {
            $concepten[$row->id] = $this->doLoad( $row->id, $row );
        }
        return new KVDdom_DomainObjectCollection( $concepten );
    }

    /**
     * doLoad
     *
     * @param integer  $id
     * @param StdClass $row
     * @return KVDthes_Concept
     */
    public function doLoad ($id, $row)
    {
        $domainObject = $this->sessie
                             ->getIdentityMap()
                             ->getDomainObject( $this->getReturnType( ), $id);
        if ($domainObject != null) {
            return $domainObject;
        }
        return $this->doLoadRow($id, $row);
    }

    /**
     * doLoadRow 
     * 
     * @param integer   $id 
     * @param StdClass  $row 
     * @return KVDthes_Concept
     */
    protected function doLoadRow( $id , $row )
    {
        $returnType = $this->getReturnType( );
        $thesaurus = $this->doLoadThesaurus( );
        return new $returnType($id, $this->sessie, $row->term, $thesaurus, array(), array());
    }

    /**
     * doLoadThesaurus 
     * 
     * @return KVDthes_Thesaurus
     */
    protected function doLoadThesaurus( )
    {
        $domainObject = $this->sessie
                             ->getIdentityMap()
                             ->getDomainObject('KVDthes_Thesaurus', $this->parameters['thesaurus_id'] );
        if ($domainObject != null) {
            return $domainObject;
        }
        return new KVDthes_Thesaurus($this->sessie,
                                     $this->parameters['thesaurus_id'],
                                     $this->parameters['thesaurus_naam'],
                                     $this->parameters['thesaurus_korte_naam'],
                                     $this->parameters['thesaurus_taal'] );
    }

    /**
     * create
     *
     * @param integer $id
     * @return KVDthes_Concept
     */
    public function create($id)
    {
        $conceptType = $this->getReturnType( );
        return call_user_func( $conceptType . '::create', $conceptType, $id, $this->sessie, $this->doLoadThesaurus( ) );
    }

    /**
     * loadMatches
     *
     * @param KVDthes_Matchable $concept
     * @return KVDthes_Matches
     */
    public function loadMatches(KVDthes_Matchable $concept)
    {
        $stmt = $this->conn->prepare( $this->getLoadMatchesStatement( ) );
        $stmt->bindValue(1, $concept->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            $tm = $this->sessie
                       ->getMapper($this->getDomainObjectClass($row->term_thesaurus_id));
            $concept->loadMatch(new KVDthes_Match($row->match_type, $tm->doLoad($row->term_id, $row)));
        }
        $concept->setLoadState(KVDthes_Matchable::LS_MATCH);
        return $concept;
    }

    /**
     * loadNotes
     *
     * Deze methode zal alle notes van een concept laden.
     *
     * @param  KVDthes_Concept $concept
     * @return KVDthes_Concept
     */
    public function loadNotes(KVDthes_Concept $concept)
    {
        $concept->setLoadState(KVDthes_Concept::LS_NOTES );
        return $concept;
        /*
         * FIXME
        $stmt = $this->conn->prepare( $this->getLoadNotesStatement( ) );
        $stmt->bindValue( 1, $concept->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
        if ($row = $stmt->fetch( PDO::FETCH_OBJ )) {
        }
        $notes = array ( 'scopeNote'    => $row->scope_note,
                         'sourceNote'   => $row->source_note,
                         'indexingNote' => $row->indexing_note,
                         'historyNote'  => $row->history_note );
        $termObj->loadNotes( $notes );
        return $termObj;
         */
    }

    /**
     * Voor een query uit en geef de resultaten terug als een collectie van domainobjects.
     * Het type van deze collectie kan meegegeven worden als parameter maar moet een subklasse
     * zijn van de KVDdom_DomainObjectCollection.
     *
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
			$domainObjects[$row->id] = $this->doLoad( $row->id , $row );
		}
		return new $collectiontype( $domainObjects );
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
     * @param   KVDthes_Concept $concept
     * @return  integer         Volgende te gebruiken index.
     */
    protected function bindValues( $stmt, $nextIndex, $concept )
    {
        $stmt->bindValue($nextIndex++, $concept->getTerm( ) , PDO::PARAM_STR );
        return $nextIndex;
    }

    /**
     * insert 
     * 
     * @param  KVDthes_Concept $concept 
     * @return KVDthes_Concept
     */
    public function insert( KVDthes_Concept $concept )
    {
        $sql = sprintf( 'INSERT INTO %s.concept (thesaurus_id, id, term ) VALUES ( %s, ?, ?)', 
                        $this->parameters['schema'], 
                        $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare(  $sql );
        $stmt->bindValue(1, $concept->getId(), PDO::PARAM_INT );
        $this->bindValues($stmt, 2, $concept);
        $stmt->execute( );

        //$this->insertNotes($concept);

        $this->insertMatches($concept);

        return $concept;
    }

    /**
     * @param  KVDthes_Concept $concept
     * @return KVDthes_Concept
     */
    public function update(KVDthes_Concept $concept) 
    {
        $sql = sprintf( 'UPDATE %s.concept SET 
                            term = ?
                         WHERE thesaurus_id = %s AND id = ?', 
                         $this->parameters['schema'], 
                         $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare($sql);
        $nextIndex = $this->bindValues($stmt, 1, $concept);
        $stmt->bindValue($nextIndex, $concept->getId(), PDO::PARAM_INT);
        $stmt->execute();

        //$this->deleteNotes($concept);
        //$this->insertNotes($concept);

        $this->deleteMatches($concept);
        $this->insertMatches($concept);

        return $concept;
    }

    /**
     * delete
     *
     * @param  KVDthes_Concept $concept
     * @return KVDthes_Concept
     */
    public function delete(KVDthes_Concept $concept)
    {
    	try {
            $sql = sprintf( 'DELETE FROM %s.concept WHERE thesaurus_id = %s AND id = ?',
                            $this->parameters['schema'],
                            $this->parameters['thesaurus_id'] );
        	$stmt = $this->conn->prepare( $sql );
        	$stmt->bindValue(1, $concept->getId( ), PDO::PARAM_INT);
        	$stmt->execute( );
        } catch (PDOException $e) {
            throw KVDdom_ExceptionConvertor::convert($e, $concept);
        }
        return $concept;
    }

    /**
     * deleteNotes
     *
     * @param  KVDthes_Concept $concept
     */
    protected function deleteNotes(KVDthes_Concept $concept)
    {
        $sql = sprintf( 'DELETE FROM %s.concept_notes WHERE thesaurus_id = %s AND concept_id = ?', 
                        $this->parameters['schema'], 
                        $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare( $sql );
        $stmt->bindValue(1, $concept->getId( ), PDO::PARAM_INT );
        $stmt->execute( );
    }
    
    /**
     * insertNotes 
     * 
     * @param  KVDthes_Concept $concept
     * @return void
     */
    protected function insertNotes(KVDthes_Concept $concept)
    {
        $sql = sprintf( 'INSERT INTO %s.notes 
                        (thesaurus_id, term_id, type, language, note) 
                        VALUES ( %s, ?, ?, ?)', 
                        $this->parameters['schema'], 
                        $this->parameters['thesaurus_id'] );
        if ( count( $concept->getNotes( ) ) > 0 ) {
            $stmt = $this->conn->prepare(  $sql );
            $stmt->bindValue(1, $concept->getId(), PDO::PARAM_INT );
            foreach ($concept->getNotes( ) as $note) {
                $stmt->bindValue(2, $note->getType() , PDO::PARAM_STR );
                $stmt->bindValue(3, $note->getLanguage()->getId( ), PDO::PARAM_STR);
                $stmt->bindValue(4, $note->getNote(), PDO::PARAM_STR);
                $stmt->execute();
            }
        }
    }

    /**
     * deleteMatches
     * 
     * @param  KVDthes_Concept $concept
     * @return void
     */
    private function deleteMatches(KVDthes_Concept $concept)
    {
        $sql = sprintf( 'DELETE FROM %s.match WHERE concept_thesaurus_id = %s AND concept_id = ?', 
                        $this->parameters['schema'], 
                        $this->parameters['thesaurus_id'] );
        $stmt = $this->conn->prepare ($sql );
        $stmt->bindValue (1, $concept->getId( ), PDO::PARAM_INT );
        $stmt->execute();
    }

    /**
     * insertMatches
     * 
     * @param  KVDthes_Concept $concept
     * @return void
     */
    private function insertMatches(KVDthes_Concept $concept )
    {
        $sql = sprintf( 
            'INSERT INTO %s.match(concept_thesaurus_id, concept_id, concept_match_type, term_match_type, term_thesaurus_id, term_id) 
             VALUES (%s, ?, ?, ?, ?, ?)', 
             $this->parameters['schema'], 
             $this->parameters['thesaurus_id'] );
        if ( count( $concept->getMatches() ) > 0 ) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(1, $concept->getId(), PDO::PARAM_INT );
            foreach ( $concept->getMatches() as $match ) {
                $stmt->bindValue(2, $match->getType(), PDO::PARAM_STR );
                $stmt->bindValue(3, $match->getInverseMatch(), PDO::PARAM_STR );
                $stmt->bindValue(4, $match->getMatchable( )->getThesaurus()->getId( ), PDO::PARAM_INT);
                $stmt->bindValue(5, $match->getMatchable( )->getId( ), PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

}
?>
