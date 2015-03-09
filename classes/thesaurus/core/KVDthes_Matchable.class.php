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
 * KVDthes_Matchable
 * 
 * Deze class stelt een term of concept voor dat kan gelinked worden aan een ander 
 * concept over de grezen van thesauri heen.
 *
 * @package    KVD.thes
 * @subpackage Core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDthes_Matchable extends KVDdom_ChangeableDomainObject
{

    /**
     * Geeft aan dat de matches voor dit concept geladen werden.
     */
    CONST LS_MATCH = 128;

    /**
     * loadState 
     * 
     * @var integer
     */
    protected $loadState;

    /**
     * De thesaurus of het concept scheme zoals gekend in SKOS.
     * 
     * @var KVDthes_Thesaurus
     */
    protected $thesaurus;

    /**
     * De match relaties
     *
     * @var KVDthes_Matches
     */
    protected $matches;

	/**
	 * __construct
	 *
	 * @param integer $id
	 * @param KVDdom_IWriteSessie $sessie
	 * @param KVDthes_Thesaurus $thesaurus
	 */
	public function __construct ( $id, KVDdom_IWriteSessie $sessie, KVDthes_Thesaurus $thesaurus = null)
	{
        parent::__construct($id, $sessie);
        $this->matches = new KVDthes_Matches();
        $this->thesaurus = ( $thesaurus != null ) ? $thesaurus : KVDthes_Thesaurus::newNull( );
    }

    /**
     * @return A URI for this matchable.
     */
    public function getUri()
    {
        return sprintf(static::BASE_URI, $this->id);
    }

    /**
     * markDirty
     *
     * @return void
     */
    protected function markDirty( )
    {
        $this->checkMatches( );
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
        return (bool) ( $state & $this->loadState );
    }

    /**
     * checkMatches
     *
     * Check if the matches have been loaded yet, if not load them.
     * @return void
     */
    public function checkMatches( )
    {
        if ( !( $this->loadState & self::LS_MATCH ) ) {
            $this->_sessie->getMapper($this->getClass())->loadMatches( $this );
            $this->setLoadState( self::LS_MATCH );
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
     * loadMatch
     *
     * @param KVDthes_Match $match
     */
    public function loadMatch( KVDthes_Match $match )
    {
        if ( $this->matches->addMatch( $match ) ) {
            $match->getMatchable( )->loadMatch( new KVDthes_Match( $match->getInverseMatch( ) , $this ) );
        }
    }

    /**
     * clearMatches
     *
     * @param string $type Een van de MATCH_ constanten uit de {@link KVDthes_Match} class.
     */
    public function clearMatches( $type = null)
    {
        $this->checkMatches( );
        $it = $this->matches->getIterator( $type );
        foreach ( $it as $match ) {
            $this->removeMatch( $match );
        }
    }

    /**
     * addMatch
     *
     * @param KVDthes_Match $match
     */
    public function addMatch( KVDthes_Match $match )
    {
        $this->checkMatches( );
        if ( $this->matches->addMatch( $match ) ) {
            //maak de inverse relatie aan
            $match->getMatchable( )->addMatch( new KVDthes_Match( $match->getInverseMatch( ) , $this ) );
            $this->markDirty( );
        }
    }

    /**
     * removeMatch
     *
     * @param KVDthes_Match $match
     */
    public function removeMatch( KVDthes_Match $match )
    {
        $this->checkMatches( );
        if ( $this->matches->removeMatch( $match ) ) {
            //verwijder de inverse relatie
            $match->getMatchable( )->removeMatch( new KVDthes_Match( $match->getInverseMatch( ), $this ) );
            $this->markDirty( );
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
     * @param  string  $type Een constante uit {@link KVDthes_Match} of null
     * @return boolean Geeft aan of een term matches heeft in het algemeen of van een bepaald type.
     */
    public function hasMatches( $type = null )
    {
        $this->checkMatches( );
        return $this->matches->count( $type ) > 0;
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
     * __toString
     *
     * @return string
     */
    public function __toString( )
    {
        return $this->getOmschrijving( );
    }

}
?>
