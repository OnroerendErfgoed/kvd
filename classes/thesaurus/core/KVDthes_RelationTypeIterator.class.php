<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @version    $Id$
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_RelationTypeIterator 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      maart 2007
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_RelationTypeIterator extends KVDthes_RelationsIterator
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
     * @param array $relations 
     * @param string $type 
     * @return void
     */
    public function __construct( array $relations , $type )
    {   
        parent::__construct( $relations );
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
        return count( array_filter( $this->relations, array ( $this, 'typeFilter' ) ) );
    }

    /**
     * typeFilter 
     * 
     * @param KVDthes_Relation $relation 
     * @return boolean
     */
    private function typeFilter( $relation )
    {
        return $relation->getType( ) === $this->type;
    }

}
?>
