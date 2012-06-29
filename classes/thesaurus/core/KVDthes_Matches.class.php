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
 * KVDthes_Matches
 * 
 * @package    KVD.thes
 * @subpackage Core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Matches implements IteratorAggregate, Countable
{

    /**
     * matches
     * 
     * @var array
     */
    protected $matches;

    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        $this->matches = array( );
    }

    /**
     * addMatch
     * 
     * @param  KVDthes_Match $match
     * @return boolean       True indien de match werd toegevoegd, false indien ze al aanwezig was.
     */
    public function addMatch(KVDthes_Match $match)
    {
        if ( !$this->hasMatch( $match ) ) {
            $this->matches[] = $match;
            return true;
        }
        return false;
    }

    /**
     * removeMatch
     * 
     * @param  KVDthes_Match $match
     * @return boolean       True indien de match werd verwijderd, false indien ze niet aanwezig was en dus niet verwijderd kon worden.
     */
    public function removeMatch(KVDthes_Match $match)
    {
        if ( ( $key = array_search ($match, $this->matches) ) !== false ) {
            unset( $this->matches[$key] );
            //array herindexeren zodat de iterator blijft werken.
            $this->matches = array_values( $this->matches );
            return true;
        }
        return false;
    }

    /**
     * hasMatch
     * 
     * @param  KVDthes_Match $match
     * @return boolean
     */
    public function hasMatch(KVDthes_Match $match) 
    {
        return in_array($match, $this->matches);
    }

    /**
     * getImmutableCollection 
     * 
     * @return  KVDdom_DomainObjectCollection
     */
    public function getImmutableCollection( )
    {
        return new KVDdom_DomainObjectCollection($this->matches);
    }

    /**
     * getIterator 
     * 
     * @param  $type   Type van matches of null om alle matches te krijgen.
     * @return KVDthes_MatchesIterator
     */
    public function getIterator( $type = null)
    {
        if ( $type == null ) {
            return new KVDthes_MatchesIterator( $this->matches );
        } else {
            return new KVDthes_MatchTypeIterator( $this->matches, $type );
        }
    }

    /**
     * Hoeveel matches zijn er?
     *
     * @param  $type   Een type constante uit {@link KVDthes_Match} of null.
     * @return integer Het totaal aantal matches of indien er een type werd opgegeven, het aantal matches van dit type.
     */
    public function count( $type = null )
    {
        if ( $type === null ) {
            return count( $this->matches );
        } else {
            $it = new KVDthes_MatchTypeIterator( $this->matches, $type );
            return count( $it );
        }
    }

}
?>
