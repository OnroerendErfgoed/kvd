<?php
/**
 * @package    KVD.thes
 * @subpackage Core
 * @version    $Id$
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_Concept 
 * 
 * Deze class stelt een concept, zoals gekend in SKOS voor. Is momenteel vooral 
 * bedoeld om te linken met externe thesauri.
 *
 * @package    KVD.thes
 * @subpackage Core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_Concept extends KVDthes_Matchable
{
    /**
     * Geeft aan dat de basis data voor het concept werd geladen. 
     */
    CONST LS_CONCEPT = 1;

    /**
     * Geeft aan dat de Notes voor de term werden geladen.
     */
    CONST LS_NOTES = 16;

    /**
     * Geeft aan dat de labels voor dit concept geladen werden.
     */
    CONST LS_LABEL = 32;

    /**
     * Geeft aan dat de matches voor dit concept geladen werden.
     */
    const LS_MATCHES = 64;

    /**
     * Identifier van het concept in het schema waartoe het behoort.
     * 
     * @var integer
     */
    protected $id = 0;

    /**
     * Omschrijving van het concept.
     * 
     * @var string
     */
	protected $term = 'Onbepaald';

    /**
     * notes 
     * 
     * @var     array   Array van strings.
     */
    protected $notes = array( 'scopeNote'     => null,
                              'definition'    => null,
                              'example'       => null,
                              'historyNote'   => null,
                              'editorialNote' => null,
                              'changeNote'    => null );

    /**
     * matches
     *
     * @var KVDthes_Matches
     */
    protected $matches;

    /**
     * __construct 
     *
     * @param integer             $id
     * @param KVDdom_IWriteSessie $sessie
     * @param string              $term
     * @param KVDthes_Thesaurus   $thesaurus
     * @param array               $labels
     * @param array               $notes
     */
	public function __construct ( $id, KVDdom_IWriteSessie $sessie, $term = null, KVDthes_Thesaurus $thesaurus, array $labels = null, array $notes = null)
	{
        parent::__construct($id, $sessie);
		$this->term = is_null( $term ) ? $id : $term;
        if ( $notes != null ) {
            $this->loadNotes( $notes );
        }
        $this->thesaurus = ( $thesaurus != null ) ? $thesaurus : KVDthes_Thesaurus::newNull( );
        $this->matches = new KVDthes_Matches();
        $this->setLoadState( self::LS_CONCEPT );
    }

    /**
     * markDirty
     *
     * @return void
     */
    protected function markDirty( )
    {
        $this->checkNotes( );
        parent::markDirty( );
    }

    /**
     * checkNotes
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
     * @param string $term
     */
    public function setTerm( $term )
    {
        $this->term = $term;
        $this->markDirty( );
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
     * getNote
     *
     * @param  $type string If not set, defaults to scopeNote
     * @return string
     */
    public function getNote( $type = 'scopeNote' )
    {
        $this->checkNotes( );
        if ( !isset($this->notes[$type]) ) {
            throw new LogicException(sprintf('Unkown type of note: %s.', $type));
        }
        return $this->notes[$type];
    }

    /**
     * setNote
     *
     * @param string $type
     * @param string $note 
     */
    public function setNote( $note, $type = 'scopeNote' )
    {
        $this->checkNotes( );
        if ( !isset($this->notes[$type]) ) {
            throw new LogicException(sprintf('Unkown type of note: %s.', $type));
        }
        $this->notes[$type] = $note;
        $this->markDirty( );
    }

    /**
     * loadNotes
     *
     * @param array $nots
     */
    public function loadNotes( array $notes ) {
        $this->notes = array_merge( $this->notes, $notes );
        $this->setLoadState( self::LS_NOTES );
    }

    /**
     * checkMatches
     * 
     * Check if the matches have been loaded yet, if not load them.
     * @return void
     */
    public function checkMatches( )
    {
        if ( !( $this->loadState & self::LS_MATCHES ) ) {
            $this->_sessie->getMapper( $this->getClass( ) )->loadMatches( $this );
            $this->setLoadState( self::LS_MATCHES );
        }
    }

    /**
     * getMatches
     * 
     * @return KVDdom_DomainObjectCollection
     */
    public function getMatches( )
    {
        $this->checkMatches( );
        return $this->matches->getImmutableCollection( );
    }

    /**
     * hasMatches
     * 
     * @param  string  $type   Een constante uit {@link KVDthes_Match} of null
     * @return boolean Geeft aan of een term matches heeft in het algemeen of van een bepaald type.
     */
    public function hasMatches($type = null)
    {
        $this->checkMatches( );
        return $this->matches->count( $type ) > 0;
    }

    /**
     * loadMatch
     * 
     * @param KVDthes_Match $match 
     * @return void
     */
    public function loadMatch (KVDthes_Match $match)
    {
        if ( $this->matches->addMatch($match) ) {
            $match->getMatchable()->loadMatch(new KVDthes_Match($match->getInverseMatch( ), $this));
        }
    }


    /**
     * clearMatches
     *
     * @param  string $type Een van de MATCH_ constanten uit de 
     *                      {@link KVDthes_Match} class.
     * @return void
     */
    public function clearMatches( $type = null)
    {
        $this->checkMatches( );
        $it = $this->matches->getIterator( $type );
        foreach ( $it as $match ) {
            $this->removeMatch( match );
        }
    }

    /**
     * addMatch
     *
     * @param KVDthes_Match $match
     * @return void
     */
    public function addMatch(KVDthes_Match $match)
    {
        $this->checkMatches( );
        if ( $this->matches->addMatch($match) ) {
            //maak de inverse match aan
            $match->getMatchable( )
                  ->addMatch(new KVDthes_Match($match->getInverseMatch( ), $this) );
            $this->markDirty( );
        }
    }

    /**
     * removeMatches
     *
     * @param  KVDthes_Match $match
     * @return void
     */
    public function removeMatch(KVDthes_Match $match)
    {
        $this->checkMatches( );
        if ( $this->matches->removematch($match) ) {
            //verwijder de inverse relatie
            $match->getMatchable( )
                  ->removeMatch(new KVDthes_Match($match->getInverseMatch( ), $this) );
            $this->markDirty( );
        }
    }





    /**
     * Remove the concept
     *
     */
    public function remove( )
    {
        $this->markRemoved( );
    }

    /**
     * create
     *
     * @param  string              $returnType
     * @param  integer             $id
     * @param  KVDdom_IWriteSessie $sessie
     * @param  KVDthes_Thesaurus   $thes
     * @return KVDthes_Concept
     */
    public static function create($returnType, $id, KVDdom_IWriteSessie $sessie, KVDthes_Thesaurus $thes )
    {
        $c = new $returnType( $id, $sessie, null, $thes);
        $c->markNew( );
        return $c;
    }

    /**
     * getOmschrijving
     *
     * @return string
     */
    public function getOmschrijving( )
    {
        return $this->getTerm( );
    }

    /**
     * getClass
     *
     * @return string
     */
    public function getClass( )
    {
        return get_class($this);
    }

}
?>
