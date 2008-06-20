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
 * @package KVD.thes
 * @subpackage Core
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_Term implements KVDdom_DomainObject
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
     * Geeft aan dat de Scope Note voor de term werd geladen. 
     */
    CONST LS_SCOPENOTE = 16;

    /**
     * Geeft aan de Source Note voor de term werd geladen. 
     */
    CONST LS_SOURCENOTE = 32;

    /**
     * sessie 
     * 
     * @var KVDthes_ISessie
     */
    protected $sessie;

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
     * language 
     * 
     * @var string
     */
    protected $language = 'Nederlands';

    /**
     * relations 
     * 
     * @var KVDthes_Relations
     */
    protected $relations;

    /**
     * scopeNote 
     * 
     * @var string
     */
    protected $scopeNote = null;


    /**
     * scopeNote 
     * 
     * @var string
     */
    protected $sourceNote = null;

    /**
     * loadState 
     * 
     * @var integer
     */
    protected $loadState;

    /**
     * __construct 
     *
     * @param KVDthes_ISessie $sessie
     * @param integer $id
     * @param string $term 
     * @param string $language
     * @param string $scopeNote
     * @param string $sourceNote
     * @return void
     */
	public function __construct ( KVDdom_IReadSessie $sessie , $id , $term , $language = 'Nederlands', $scopeNote = null, $sourceNote = null)
	{
        $this->sessie = $sessie;
        $this->id = $id;
		$this->term = $term;
        $this->language = $language;
        if ( $scopeNote != null ) {
            $this->scopeNote = $scopeNote;
            $this->setLoadState( self::LS_SCOPENOTE );
        }
        if ( $sourceNote != null ) {
            $this->sourceNote = $sourceNote;
            $this->setLoadState( self::LS_SOURCENOTE );
        }
        $this->relations = new KVDthes_Relations();
        $this->setLoadState( self::LS_TERM );
	    $this->sessie->registerClean( $this );
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
            $this->sessie->getMapper( $this->getClass( ) )->loadRelations( $this );
            $this->setLoadState( self::LS_REL );
        }
    }

    /**
     * checkScopeNote 
     * 
     * Check if the ScopeNote has been loaded yet, if not load it.
     * @return void
     */
    public function checkScopeNote( )
    {
        if ( !( $this->loadState & self::LS_SCOPENOTE ) ) {
            $this->sessie->getMapper( $this->getClass( ) )->loadScopeNote( $this );
            $this->setLoadState( self::LS_SCOPENOTE );
        }
    }

    /**
     * checkSourceNote 
     * 
     * Check if the SourceNote has been loaded yet, if not load it.
     * @return void
     */
    public function checkSourceNote( )
    {
        if ( !( $this->loadState & self::LS_SOURCENOTE ) ) {
            $this->sessie->getMapper( $this->getClass( ) )->loadSourceNote( $this );
            $this->setLoadState( self::LS_SOURCENOTE );
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
     * getId 
     * 
     * @return integer
     */
    public function getId( )
    {
        return $this->id;
    }

    /**
     * getScopeNote 
     * 
     * @return string
     */
    public function getScopeNote( )
    {
        $this->checkScopeNote( );
        return $this->scopeNote;
    }

    /**
     * getSourceNote 
     * 
     * @return string
     */
    public function getSourceNote( )
    {
        $this->checkSourceNote( );
        return $this->sourceNote;
    }

    /**
     * addRelation 
     * 
     * @param KVDthes_Relation $relation 
     * @throws InvalidArgumentException
     * @return void
     */
    public function addRelation ( KVDthes_Relation $relation )
    {
        foreach ( $this->relations as $curRel ) {
            if ( $relation->equals( $curRel ) ) {
                return;
            }
        }
        $this->relations->addRelation( $relation );
        $relation->getTerm( )->addRelation( new KVDthes_Relation( $relation->getInverseRelation( ) , $this ) );
    }

    /**
     * addScopeNote 
     * 
     * @param string $sn 
     * @return void
     */
    public function addScopeNote( $sn ) {
        $this->scopeNote = $sn;
        $this->setLoadState( self::LS_SCOPENOTE );
    }

    /**
     * addSourceNote 
     * 
     * @param string $sn 
     * @return void
     */
    public function addSourceNote( $sn ) {
        $this->sourceNote = $sn;
        $this->setLoadState( self::LS_SOURCENOTE );
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
        $this->checkRelations( );
        $it = $this->relations->getUSEIterator( );
        $it->rewind( );
        return !$it->valid( );
    }

    /**
     * getPreferredTerm 
     * 
     * @return KVDthes_Term
     */
    public function getPreferredTerm( )
    {
        $this->checkRelations( );
        $it = $this->relations->getUSEIterator( );
        $it->rewind( );
        return $it->valid( ) ? $it->current( )->getTerm( ) : new KVDthes_NullTerm( );
    }

    /**
     * getNonPreferredTerms 
     * 
     * @return KVDthes_RelationsIterator
     */
    public function getNonPreferredTerms( )
    {
        $this->checkRelations( );
        return $this->relations->getUFIterator();
    }

    /**
     * getRelatedTerms 
     * 
     * @return KVDthes_RelationsIterator
     */
    public function getRelatedTerms( )
    {
        $this->checkRelations( );
        return $this->relations->getRTIterator( );
    }

    /**
     * getNarrowerTerms 
     * 
     * @return KVDthes_RelationsIterator
     */
    public function getNarrowerTerms( )
    {
        $this->checkRelations( );
        return $this->relations->getNTIterator( );
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
        return $this->term;
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
}

/**
 * KVDthes_NullTerm 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since i19 maart 2007
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
    public function accept( KVDthes_TreeVisitor $visitor )
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
}
?>
