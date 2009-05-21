<?php
/**
 * @package KVD.thes
 * @subpackage Core
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_Term 
 * 
 * Deze class stelt een term in een thesaurus voor. Dit DomainObject is losjes gebasseerd op de 
 * zThes standaard maar heeft niet alle velden die in dat model aanwezig zijn.
 * @package KVD.thes
 * @subpackage Core
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_Term extends KVDdom_ChangeableDomainObject
{
    /**
     * Geeft aan dat de basis data voor de term werd geladen. 
     */
    CONST LS_TERM = 1;

    /**
     * Geeft aan dat de BT relaties voor deze term geladen werden. 
     */
    CONST LS_RELBT = 2;

    /**
     * Geeft aan dat de NT relaties voor deze term geladen werden. 
     */
    CONST LS_RELNT = 4;

    /**
     * Geeft aan dat de relaties van deze term werden geladen. 
     */
    CONST LS_REL = 8;

    /**
     * Geeft aan dat de Notes voor de term werden geladen.
     */
    CONST LS_NOTES = 16;

    /**
     * id 
     * 
     * @var integer
     */
    protected $id = 0;

    /**
     * term 
     * 
     * @var string
     */
	protected $term = 'Onbepaald';

    /**
     * type: 
     * 
     * @var     KVDthes_TermType
     */
    protected $type;

    /**
     * qualifier 
     * 
     * @var string
     */
    protected $qualifier = null;

    /**
     * language 
     * 
     * @var string
     */
    protected $language = 'nl-BE';

    /**
     * sortKey 
     * 
     * @var string
     */
    protected $sortKey = null;

    /**
     * relations 
     * 
     * @var KVDthes_Relations
     */
    protected $relations;

    /**
     * notes 
     * 
     * @var     array   Array van strings.
     */
    protected $notes = array(   'scopeNote'     => null,
                                'sourceNote'    => null,
                                'indexingNote'  => null,
                                'historyNote'   => null );

    /**
     * loadState 
     * 
     * @var integer
     */
    protected $loadState;

    /**
     * thesaurus 
     * 
     * @var KVDthes_Thesaurus
     */
    protected $thesaurus;

    /**
     * __construct 
     *
     * @param integer               $id
     * @param KVDdom_IWriteSessie   $sessie
     * @param string                $term 
     * @param string                $qualifier
     * @param string                $language
     * @param string                $sortKey
     * @param array                 $notes
     * @param KVDthes_Thesaurus     $thesaurus
     * @return void
     */
	public function __construct ( $id, KVDdom_IWriteSessie $sessie, $term, KVDthes_TermType $type = null, $qualifier = null, $language = 'nl-BE', $sortKey = null, array $notes = null, KVDthes_Thesaurus $thesaurus = null)
	{
        parent::__construct($id, $sessie);
		$this->term = $term;
        $this->type = is_null( $type ) ? KVDthes_TermType::newNull( ) : $type;
        $this->qualifier = $qualifier;
        $this->language = $language;
        $this->sortKey = $sortKey;
        if ( $notes != null ) {
            $this->loadNotes( $notes );
        }
        $this->thesaurus = ( $thesaurus != null ) ? $thesaurus : KVDthes_Thesaurus::newNull( );
        $this->relations = new KVDthes_Relations();
        $this->setLoadState( self::LS_TERM );
    }

    /**
     * markDirty 
     * 
     * @return void
     */
    protected function markDirty( )
    {
        $this->checkRelations( );
        $this->checkNotes( );
        parent::markDirty( );
    }

    /**
     * isLoadState 
     * 
     * @param integer $state Zie de LS_* constanten.
     * @return boolean
     */
    public function isLoadState( $state )
    {
        return ( bool ) ( $state & $this->loadState );
    }

    /**
     * checkRelations 
     * 
     * Check if the relations have been loaded yet, if not load them.
     * @return void
     */
    public function checkRelations( )
    {
        if ( !( $this->loadState & self::LS_REL ) ) {
            $this->_sessie->getMapper( $this->getClass( ) )->loadRelations( $this );
            $this->setLoadState( self::LS_REL );
        }
    }

    /**
     * checkScopeNote 
     * 
     * Check if the notes have been loaded yet, if not load them.
     * @return void
     */
    public function checkNotes( )
    {
        if ( !( $this->loadState & self::LS_NOTES ) ) {
            $this->_sessie->getMapper( $this->getClass( ) )->loadNotes( $this );
            $this->setLoadState( self::LS_NOTES );
        }
    }

    /**
     * setLoadState 
     * 
     * @param integer $state 
     * @return void
     */
    public function setLoadState( $state )
    {
        if ( !( $this->loadState & $state ) ) {
            $this->loadState += $state;
        }
    }

    /**
     * getTerm 
     * 
     * @return string
     */
	public function getTerm()
	{
		return $this->term;
	}


    /**
     * setTerm 
     * 
     * @since   7 apr 2009
     * @param   string $term 
     * @return  void
     */
    public function setTerm( $term )
    {
        $this->term = $term;
        $this->markDirty( );
    }

    /**
     * getType 
     * 
     * @since   16 apr 2009
     * @return  KVDthes_TermType
     */
    public function getType( )
    {
        return $this->type;
    }

    /**
     * setType 
     * 
     * @since   16 apr 2009
     * @param   KVDthes_TermType $type 
     * @return  void
     */
    public function setType( KVDthes_TermType $type )
    {
        $this->type = $type;
        $this->markDirty( );
    }

    /**
     * getQualifier 
     * 
     * Geef de qualifier terug. Een qualifier is extra informatie die de term uniek maakt binnen een thesaurus.
     * Stel bijvoorbeeld dat we twee termen hebben genaamd 'obelisken' waarbij de ene slaat op een grafmonument
     * en de andere op een object binnen een tuin dan zou de ene de qualifier 'grafmonument' en de andere de
     * qualifier 'tuinornament' kunnen hebben.
     * Binnen de ANSI/NISO Z39.19-2005 standaard vinden we volgende definitie: "A defining term, used in a controlled
     * vocabulary to distinguish homographs."
     * @return string
     */
    public function getQualifier( )
    {
        return $this->qualifier;
    }

    /**
     * setQualifier 
     * 
     * @since   7 apr 2009
     * @param   string $qual 
     * @return  void
     */
    public function setQualifier( $qual )
    {
        $this->qualifier = $qual;
        $this->markDirty( );
    }

    /**
     * getQualifiedTerm 
     * 
     * Geef een unieke weergave van een bepaalde term, dit bestaat uit de term gevolgd door de qualifier tussen
     * haakjes. bv. 'obelisken (grafmonument)'.
     * @return string
     */
    public function getQualifiedTerm( )
    {
        return $this->term . ( $this->qualifier !== null ? ' (' . $this->qualifier . ')' : '');
    }

    /**
     * getId 
     * 
     * @return integer
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * getSortKey 
     * 
     * Deze methode biedt de mogelijkheid om een term op een andere dan een puur alfabetische manier te sorteren.
     * @return string
     */
    public function getSortKey( )
    {
        return $this->sortKey === null ? substr( $this->term, 0, 45) : $this->sortKey;
    }

    /**
     * setSortKey 
     * 
     * @since   7 apr 2009
     * @param   string $sort 
     * @return  void
     */
    public function setSortKey( $sort )
    {
        $this->sortKey = $sort;
        $this->markDirty( );
    }

    /**
     * getScopeNote 
     * 
     * @return string
     */
    public function getScopeNote( )
    {
        $this->checkNotes( );
        return $this->notes['scopeNote'];
    }

    /**
     * setScopeNote 
     * 
     * @since   7 apr 2009
     * @param   string $note 
     * @return  void
     */
    public function setScopeNote( $note )
    {
        $this->checkNotes( );
        $this->notes['scopeNote'] = $note;
        $this->markDirty( );
    }

    /**
     * getSourceNote 
     * 
     * @return string
     */
    public function getSourceNote( )
    {
        $this->checkNotes( );
        return $this->notes['sourceNote'];
    }

    /**
     * setSourceNote 
     * 
     * @since   7 apr 2009
     * @param   string $note 
     * @return  void
     */
    public function setSourceNote( $note )
    {
        $this->checkNotes( );
        $this->notes['sourceNote'] = $note;
        $this->markDirty( );
    }

    /**
     * getIndexingNote 
     * 
     * @since   22 apr 2009
     * @return  string
     */
    public function getIndexingNote( )
    {
        $this->checkNotes( );
        return $this->notes['indexingNote'];
    }

    /**
     * setIndexingNote 
     * 
     * @since   22 apr 2009
     * @param   string $note 
     * @return  void
     */
    public function setIndexingNote( $note )
    {
        $this->checkNotes( );
        $this->notes['indexingNote'] = $note;
        $this->markDirty( );
    }

    /**
     * getHistoryNote 
     * 
     * @since   22 apr 2009
     * @return  string
     */
    public function getHistoryNote( )
    {
        $this->checkNotes( );
        return $this->notes['historyNote'];
    }

    /**
     * setHistoryNote 
     * 
     * @since   22 apr 2009
     * @param   string $note 
     * @return  void
     */
    public function setHistoryNote( $note )
    {
        $this->checkNotes( );
        $this->notes['historyNote'] = $note;
        $this->markDirty( );
    }

    /**
     * loadRelation 
     * 
     * @param KVDthes_Relation $relation 
     * @return void
     */
    public function loadRelation ( KVDthes_Relation $relation )
    {
        if ( $this->relations->addRelation( $relation ) ) {
            $relation->getTerm( )->loadRelation( new KVDthes_Relation( $relation->getInverseRelation( ) , $this ) );
        }
    }

    /**
     * clearRelations 
     * 
     * @param   string  $type   Een van de REL_ constanten uit de {@link KVDthes_Relation} class.
     * @return  void
     */
    public function clearRelations( $type = null)
    {
        $this->checkRelations( );
        $it = $this->relations->getIterator( $type );
        foreach ( $it as $relation ) {
            $this->removeRelation( $relation );
        }
    }

    /**
     * addRelation 
     * 
     * @param KVDthes_Relation $relation 
     * @return void
     */
    public function addRelation( KVDthes_Relation $relation )
    {

        $this->checkRelations( );
        if ( $this->relations->addRelation( $relation ) ) {
            //maak de inverse relatie aan
            $relation->getTerm( )->addRelation( new KVDthes_Relation( $relation->getInverseRelation( ) , $this ) );
            $this->markDirty( );
        }
    }

    /**
     * removeRelation 
     * 
     * @param   KVDthes_Relation $relation 
     * @return  void
     */
    public function removeRelation( KVDthes_Relation $relation )
    {
        $this->checkRelations( );
        if ( $this->relations->removeRelation( $relation ) ) {
            //verwijder de inverse relatie
            $relation->getTerm( )->removeRelation( new KVDthes_Relation( $relation->getInverseRelation( ), $this ) );
            $this->markDirty( );
        }
    }

    /**
     * getRelations 
     * 
     * @return KVDdom_DomainObjectCollection
     */
    public function getRelations( )
    {
        $this->checkRelations( );
        return $this->relations->getImmutableCollection( );
    }

    /**
     * hasRelations 
     * 
     * @since   18 apr 2009
     * @param   string      $type   Een constante uit {@link KVDthes_Relation} of null
     * @return  boolean     Geeft aan of een term relaties heeft in het algemeen of van een bepaald type.
     */
    public function hasRelations( $type = null )
    {
        $this->checkRelations( );
        return $this->relations->count( $type ) > 0;
    }


    /**
     * sortRelations 
     * 
     * @param   integer     $methode    Een van de sort-constanten uit KVDthes_Relations.
     * @return void
     */
    public function sortRelations( $methode )
    {
        $this->checkRelations( );
        $this->relations->sort( $methode );
    }

    /**
     * loadNotes
     * 
     * @param   array $n 
     * @return  void
     */
    public function loadNotes( array $notes ) {
        $this->notes = array_merge( $this->notes, $notes );
        $this->setLoadState( self::LS_NOTES );
    }

    /**
     * accept 
     * 
     * @param KVDthes_TreeVisitor $visitor 
     * @return boolean
     */
    public function accept( KVDthes_AbstractTreeVisitor $visitor )
    {
        $this->checkRelations( );
        $visitor->visit( $this );
        if ( $visitor->enterRelations( $this ) ) {
            $iterator = $visitor->getIterator( $this->relations );
            foreach ($iterator as $relation) {
                if( !$visitor->visitRelation( $relation ) ) {
                    break;
                }
            }
        }
		if ( $visitor->enterComposite( $this ) ) {
            $iterator = $visitor->getIterator( $this->relations );
            foreach ($iterator as $relation) {
			    if ( !$relation->getTerm( )->accept($visitor) ) {
                    break;
                }
            }
        }
        return $visitor->leaveComposite( $this );
    }

    /**
     * acceptSimple 
     * 
     * @param KVDthes_AbstractSimpleVisitor $visitor 
     * @return boolean
     */
    public function acceptSimple( KVDthes_AbstractSimpleVisitor $visitor )
    {
        $this->checkRelations( );
        $visitor->visit( $this );
		if ( $visitor->enterComposite( $this ) ) {
            $iterator = $visitor->getIterator( $this->relations );
            foreach ($iterator as $relation) {
                if( !$visitor->visitRelation( $relation ) ) {
                    break;
                }
            }
        }
        return $visitor->leaveComposite( $this );
    }

    /**
     * isPreferredTerm 
     * 
     * @return boolean
     */
    public function isPreferredTerm( )
    {
        return ( $this->getType( )->getId( ) === 'PT' || $this->getType( )->getId( ) === 'HR' || $this->getType( )->getId( ) == 'NL' );
    }

    /**
     * getPreferredTerm 
     * 
     * @return KVDthes_Term     Ofwel de preferred Term, ofwel deze term zelf.
     */
    public function getPreferredTerm( )
    {
        // If this is a preferred term we don't need to load the relations.
        if ( $this->isPreferredTerm( ) ) {
            return $this;
        }
        $this->checkRelations( );
        $it = $this->relations->getUSEIterator( );
        $it->rewind( );
        return $it->valid( ) ? $it->current( )->getTerm( ) : $this;
    }

    /**
     * setPreferredTerm 
     * 
     * @since   10 apr 2009
     * @param   KVDthes_Term    $term 
     * @return  void
     */
    public function setPreferredTerm( KVDthes_Term $term )
    {
        if ( $this->isPreferredTerm( ) && !$term->isNull( ) ) {
            throw new LogicException( 'You are trying to set a preferred term for a term which is already a preferred term.' );
        }
        $this->checkRelations( );
        $it = $this->relations->getUSEIterator( );
        $it->rewind( );
        if ( $it->valid( ) ) {
            $current = $it->current( );
            $this->removeRelation( $current );
        }
        // Indien de term een NullObject is wissen we de bestaande USE relation maar vervangen we die niet door iets nieuws.
        if ( !$term->isNull( ) ) {
            $this->addRelation( new KVDthes_Relation( KVDthes_Relation::REL_USE, $term ) );
        }
        $this->markDirty( );
    }

    /**
     * getNonPreferredTerms 
     * 
     * @return KVDthes_RelationsIterator
     */
    public function getNonPreferredTerms( )
    {
        $this->checkRelations( );
        $it = $this->relations->getUFIterator( );
        $arr = array( );
        foreach ( $it as $r ) {
            $arr[] = $r->getTerm( );
        }
        return new KVDdom_DomainObjectCollection( $arr );
    }

    /**
     * getRelatedTerms 
     * 
     * @return  KVDdom_DomainObjectCollection
     */
    public function getRelatedTerms( )
    {
        $this->checkRelations( );
        $it = $this->relations->getRTIterator( );
        $arr = array( );
        foreach ( $it as $r ) {
            $arr[] = $r->getTerm( );
        }
        return new KVDdom_DomainObjectCollection( $arr );
    }

    /**
     * getNarrowerTerms 
     * 
     * @return KVDthes_RelationsIterator
     */
    public function getNarrowerTerms( )
    {
        $this->checkRelations( );
        $it = $this->relations->getNTIterator( );
        $arr = array( );
        foreach ( $it as $r ) {
            $arr[] = $r->getTerm( );
        }
        return new KVDdom_DomainObjectCollection( $arr );
    }

    /**
     * getLanguage 
     * 
     * @return string
     */
    public function getLanguage( )
    {
        return $this->language;
    }

    /**
     * setLanguage 
     * 
     * @since   7 apr 2009
     * @param   string $lang 
     * @return  void
     */
    public function setLanguage( $lang )
    {
        $this->language = $lang;
        $this->markDirty( );
    }

    /**
     * hasNTRelations 
     * 
     * @return boolean
     */
    public function hasNTRelations( )
    {
        $this->checkRelations( );
        return count( $this->relations->getNTIterator( ) ) > 0;
    }

    
    /**
     * hasBTRelations 
     * 
     * @return boolean
     */
    public function hasBTRelations( )
    {
        $this->checkRelations( );
        return count( $this->relations->getBTIterator( ) ) > 0;
    }

    /**
     * hasBT 
     * 
     * @return boolean
     */
    public function hasBT( )
    {
        return $this->hasBTRelations();
    }

    /**
     * getBroaderTerm 
     * 
     * @return KVDthes_Term
     */
    public function getBroaderTerm( )
    {
        $this->checkRelations( );
        $it = $this->relations->getBTIterator( );
        $it->rewind( );
        return $it->valid( ) ? $it->current( )->getTerm( ) : new KVDthes_NullTerm( );
    }

    /**
     * setBroaderTerm 
     * 
     * @since   10 apr 2009
     * @param   KVDthes_Term    $term 
     * @return  void
     */
    public function setBroaderTerm( KVDthes_Term $term )
    {
        if ( !$this->isPreferredTerm( ) && !$term->isNull( ) ) {
            throw new LogicException( 'You are trying to set a Broader Term for a term which is not a preferred term.' );
        }
        $this->checkRelations( );
        $it = $this->relations->getBTIterator( );
        $it->rewind( );
        if ( $it->valid( ) ) {
            $current = $it->current( );
            $this->removeRelation( $current );
        }
        // Indien de term een NullObject is wissen we de bestaande BT relation maar vervangen we die niet door iets nieuws.
        if ( !$term->isNull( ) ) {
            $this->addRelation( new KVDthes_Relation( KVDthes_Relation::REL_BT, $term ) );
        }
        $this->markDirty( );
    }

    /**
     * getThesaurus 
     * 
     * @return KVDthes_Thesaurus
     */
    public function getThesaurus( )
    {
        return $this->thesaurus;
    }

    /**
     * remove 
     * 
     * @since   7 apr 2009
     * @return  void
     */
    public function remove( )
    {
        $this->markRemoved( );
    }

    /**
     * create 
     * 
     * @param   string                  $returnType
     * @param   integer                 $id 
     * @param   KVDdom_IWriteSessie     $sessie 
     * @param   KVDthes_Thesaurus       $thes 
     * @return void
     */
    public static function create($returnType, $id, KVDdom_IWriteSessie $sessie, KVDthes_Thesaurus $thes )
    {
        $t = new $returnType( $id, $sessie, 'Onbepaald', null, null, null, null, null, $thes);
        $t->markNew( );
        return $t;
    }

    /**
     * getClass 
     * 
     * @return string
     */
    public function getClass( )
    {
        return get_class( $this );
    }

    /**
     * getOmschrijving 
     * 
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->getQualifiedTerm( );
    }

    /**
     * __toString 
     * 
     * @return string
     */
    public function __toString( )
    {
        return $this->getOmschrijving( );
    }

    /**
     * isNull 
     * 
     * @return boolean
     */
    public function isNull()
    {
        return false;
    }

    /**
     * newNull 
     * 
     * @since   17 apr 2009    
     * @return  KVDthes_NullTerm
     */
    public static function newNull( )
    {
        return new KVDthes_NullTerm( );
    }
}

/**
 * KVDthes_NullTerm 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_NullTerm extends KVDthes_Term
{
    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct( )
    {
        $this->id = 0;
        $this->term = 'Onbepaald';
        $this->relations = new KVDthes_Relations();
    }

    /**
     * setTerm 
     * 
     * @param string $term 
     * @return void
     */
    public function setTerm( $term )
    {
        return;
    }


    /**
     * addRelation 
     * 
     * @param KVDthes_Relation $relation 
     * @return void
     */
    public function addRelation ( KVDthes_Relation $relation )
    {
        return;
    }


    /**
     * accept 
     * 
     * @param KVDthes_TreeVisitor $visitor 
     * @return void
     */
    public function accept( KVDthes_AbstractTreeVisitor $visitor )
    {
        return true;
    }

    /**
     * isPreferredTerm 
     * 
     * @return boolean
     */
    public function isPreferredTerm( )
    {
        return false;
    }

    /**
     * getPreferredTerm 
     * 
     * @return KVDthes_NullTerm
     */
    public function getPreferredTerm( )
    {
        return new KVDthes_NullTerm( );
    }

    /**
     * isNull 
     * 
     * @return boolean
     */
    public function isNull()
    {
        return true;
    }
}

/**
 * KVDthes_TermType 
 * 
 * @package     KVD.thes
 * @subpackage  Core
 * @since       16 apr 2009
 * @copyright   2004-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_TermType extends KVDdom_ValueDomainObject
{
    /**
     * __construct 
     * 
     * @param   string  $id 
     * @param   string  $type 
     * @return  KVDthes_TermType
     */
    public function __construct( $id, $type = 'Onbepaald' )
    {
        $this->id = $id;
        $this->type=$type;
    }

    /**
     * getType 
     * 
     * @return  string
     */
    public function getType( )
    {
        return $this->type;
    }

    /**
     * getOmschrijving 
     * 
     * @return  string
     */
    public function getOmschrijving( )
    {
        return $this->type;
    }

    /**
     * newNull 
     * 
     * @return  KVDthes_TermType
     */
    static public function newNull( )
    {
        return new KVDthes_TermType( 'ND', 'Non Descriptor');
    }

    
}
?>
