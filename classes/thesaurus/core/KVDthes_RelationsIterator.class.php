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
 * KVDthes_RelationsIterator 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      19 maart 2007
 * @copyright  2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
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

?>
