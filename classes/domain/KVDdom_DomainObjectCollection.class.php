<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Object om collecties van DomainObjects te beheren.
 *
 * Vooral van tel voor het partieel laden van data ipv het steeds volledig laden van alle objecten (limit e.d.)
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_DomainObjectCollection implements SeekableIterator, Countable
{
    /**
     * @var array De KVDdom_DomainObjects
     */
    protected $collection;

    /**
     * @param array $collection
     * @throws Exception - Indien collection geen array is.
     */
    public function __construct ( $collection )
    {
        if (!is_array($collection)) {
            throw new Exception ('Invalid collection!');
        }
        $this->collection = $collection;
    }

    /**
     * @return integer
     */
    public function getTotalRecordCount()
    {
        return count( $this->collection );
    }

    /**
     * @return integer
     */
    public function count( )
    {
        return count( $this->collection );
    }

    /**
     * @return KVDdom_DomainObject
     */
    public function current()
    {
        return current ( $this->collection );
    }

    /**
     * @return integer
     */
    public function key()
    {
        return key ( $this->collection );   
    }

    public function next()
    {
        next ( $this->collection );
    }

    public function rewind()
    {
        reset ( $this->collection );
    }

    /**
     * @return KVDdom_DomainObject
     * @throws Exception - Indien een ongeldige index gevraagd wordt.
     */
    public function seek ($index)
    {
        if ( $index < 0 || $index >= $this->getTotalRecordCount() ) {
            $index = 0; 
        }
        $this->rewind();
        $position = 0;
        while ( $position < $index && $this->valid( ) ) {
            $this->next( );
            $position++;
        }
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        if ( is_null( key( $this->collection)) ) {
            return false;
        } else {
            return true;
        }
    }
    
}
?>
