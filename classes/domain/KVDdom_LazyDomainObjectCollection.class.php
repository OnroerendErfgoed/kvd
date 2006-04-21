<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Object om luie collecties van DomainObjects te beheren.
 *
 * De collectie wordt niet meteen geladen, maar slechts in stukjes, wanneer dit nodig is. Zo kunnen grote collecties ook gebruikt worden zonder al te veel overhead.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_LazyDomainObjectCollection extends KVDdom_DomainObjectCollection
{
    /**
     * Geeft aan dat een bepaald object nog niet geladen is.
     */
    const PLACEHOLDER = "TE LADEN";
    
    /**
     * @var KVDdom_ChunkyQuery
     */
    private $_chunkyQuery;

    /**
     * @var integer
     */
    private $currentIndex;

    /**
     * @param KVDdom_ChunkyQuery $chunkyQuery
     */
    public function __construct ( $chunkyQuery )
    {
        $this->_chunkyQuery = $chunkyQuery;
        if ($this->getTotalRecordCount() > 0 ) {
            $this->collection = array_fill (0, $this->getTotalRecordCount() , self::PLACEHOLDER);
        } 
    }
    
    /**
     * @return integer
     */
    public function getTotalRecordCount()
    {
        return $this->_chunkyQuery->getTotalRecordCount();    
    }
    
    /**
     * @return KVDdom_DomainObject
     */
    public function current()
    {
        if ( $this->currentIndex >= $this->getTotalRecordCount() ) {
            return false;    
        }
        if ($this->collection[$this->currentIndex] === self::PLACEHOLDER ) {
            $this->fillChunk();
        }
        return $this->collection[$this->currentIndex];
    }

    /**
     * @return integer
     */
    public function key()
    {
        return $this->currentIndex;   
    }
    
    /**
     * @return void
     */
    public function next()
    {
        $this->currentIndex++;
    }

    public function rewind()
    {
        $this->currentIndex=0;
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        if ( $this->currentIndex >= $this->getTotalRecordCount() ) {
            return false;    
        } else {
            return true;    
        }
    }

    /**
     * @return KVDdom_DomainObject
     */
    public function seek ($index)
    {
        if ( $index < 0 || $index >= $this->getTotalRecordCount()) {
            $index = 0;
        }
        $this->currentIndex = $index;
        return $this->current();
    }

    
    private function fillChunk()
    {
        $chunk = floor ( $this->currentIndex / $this->_chunkyQuery->getRowsPerChunk() ) + 1;
        $this->_chunkyQuery->setChunk ( $chunk );
        $domainObjects = $this->_chunkyQuery->getDomainObjects ();
        $i = ($chunk - 1) * $this->_chunkyQuery->getRowsPerChunk();
        foreach ( $domainObjects as $DomainObject ) {
            $this->collection[$i] = $DomainObject;
            $i++;
        }
    }
}
?>
