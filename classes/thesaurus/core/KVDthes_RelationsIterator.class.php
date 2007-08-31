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
 * KVDthes_RelationsIterator 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since 19 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_RelationsIterator implements Iterator, Countable
{
    /**
     * index 
     * 
     * @var integer
     */
    protected $index = 0;

    /**
     * relations 
     * 
     * @var array
     */
    protected $relations;

    /**
     * __construct 
     * 
     * @param array $relations 
     * @return KVDthes
     */
    public function __construct ( array $relations )
    {
        $this->relations = $relations;
    }

    /**
     * next 
     * 
     * @return void
     */
    public function next( )
    {
        $this->index++;
    }

    /**
     * current 
     * 
     * @return KVDthes_Relation
     */
    public function current( )
    {
        return $this->relations[$this->index];
    }

    /**
     * rewind 
     * 
     * @return void
     */
    public function rewind( )
    {
        $this->index = 0;
    }

    /**
     * key 
     * 
     * @return integer
     */
    public function key( )
    {
        return $this->index;
    }

    /**
     * valid 
     * 
     * @return boolean
     */
    public function valid( )
    {
        return ( $this->index +1 <= count( $this->relations ) );
    }

    /**
     * count 
     * 
     * @return integer
     */
    public function count( )
    {
        return count( $this->relations );
    }

}

/**
 * KVDthes_RelationTypeIterator 
 * 
 * @package KVD.thes
 * @subpackage 
 * @since maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
        $this->index++;
        if ( $this->valid( ) && $this->current( )->getType( ) != $this->type ) {
            $this->next( );
        }
    }

    /**
     * count 
     * 
     * @return integer
     */
    public function count( )
    {
        return count( array_filter( $this->relations , array ( $this , 'typeFilter' ) ) );
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
