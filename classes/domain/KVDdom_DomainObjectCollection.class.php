<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_DomainObjectCollection.class.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
 */

/**
 * Object om collecties van DomainObjects te beheren.
 *
 * Vooral van tel voor het partieel laden van data ipv het steeds volledig laden van alle objecten (limit e.d.)
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_DomainObjectCollection implements SeekableIterator
{
    /**
     * @var array De KVDdom_DomainObjects
     */
    protected $collection = array();

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

    /**
     * @return KVddom_DomainObject
     */
    public function next()
    {
        return next ( $this->collection );
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
        if ( $index < 0 || $index >= $this->getTotalRecordCount() - 1) {
            throw new Exception('Invalid seek position');    
        }
        $this->rewind();
        while ( $index > $this->key() ) {
            next ( $this->collection );    
        }
        return $this->current();
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->current() !== false;
    }
    
}
?>
