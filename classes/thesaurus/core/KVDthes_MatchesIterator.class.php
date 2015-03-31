<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_MatchesIterator
 *
 * @package    KVD.thes
 * @subpackage core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_MatchesIterator implements Iterator, Countable
{
    /**
     * index
     *
     * @var integer
     */
    protected $index = 0;

    /**
     * matches
     *
     * @var array
     */
    protected $matches;

    /**
     * __construct
     *
     * @param array $matches
     */
    public function __construct ( array $matches )
    {
        $this->matches = $matches;
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
     * @return KVDthes_Match
     */
    public function current( )
    {
        return $this->matches[$this->index];
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
        return ( $this->index +1 <= count( $this->matches ) );
    }

    /**
     * count
     *
     * @return integer
     */
    public function count( )
    {
        return count( $this->matches );
    }

}
?>
