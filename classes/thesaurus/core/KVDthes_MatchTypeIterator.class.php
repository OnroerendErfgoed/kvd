<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @version    $Id$
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_MatchTypeIterator 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_MatchTypeIterator extends KVDthes_MatchesIterator
{
    /**
     * type 
     * 
     * @var string
     */
    protected $type;

    /**
     * __construct 
     *
     * @param array $matches
     * @param string $type
     * @return void
     */
    public function __construct( array $matches , $type )
    {
        parent::__construct( $matches );
        $this->type = $type;
        $this->rewind( );
    }

    /**
     * rewind
     *
     * @return void
     */
    public function rewind( )
    {
        parent::rewind( );
        if ( $this->valid( ) && $this->current( )->getType( ) != $this->type ) {
            $this->next( );
        }
    }

    /**
     * next 
     * 
     * @return void
     */
    public function next( )
    {
        do {
            $this->index++;
        } while ( $this->valid( ) && $this->current( )->getType( ) != $this->type ) ;
    }

    /**
     * count 
     * 
     * @return integer
     */
    public function count( )
    {
        return count( array_filter( $this->matches, array ( $this, 'typeFilter' ) ) );
    }

    /**
     * typeFilter 
     * 
     * @param KVDthes_Match $match
     * @return boolean
     */
    private function typeFilter( $match )
    {
        return $match->getType( ) === $this->type;
    }

}
?>
