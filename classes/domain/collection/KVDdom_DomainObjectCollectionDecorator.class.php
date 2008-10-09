<?php
/**
 * @package KVD.dom
 * @subpackage collection
 * @version $Id$
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_DomainObjectCollectionDecorator 
 * 
 * @package KVD.dom
 * @subpackage collection
 * @since 30 april 2007
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_DomainObjectCollectionDecorator extends KVDdom_DomainObjectCollection
{
    /**
     * collection 
     * 
     * @var KVDdom_DomainObjectCollection
     */
    protected $collection;
    
    /**
     * __construct 
     * 
     * @param KVDdom_DomainObjectCollection $coll 
     * @return void
     */
    public function __construct ( KVDdom_DomainObjectCollection $coll )
    {
        $this->collection = $coll;
    }

    /**
     * getTotalRecordCount 
     * 
     * @deprecated
     * @return integer
     */
    public function getTotalRecordCount( )
    {
        return count( $this->collection );
    }

    /**
     * count 
     * 
     * @return integer
     */
    public function count( )
    {
        return count( $this->collection );
    }

    /**
     * current 
     * 
     * @return KVDdom_DomainObject
     */
    public function current()
    {
        return $this->collection->current( );
    }

    /**
     * key
     * 
     * @return integer
     */
    public function key()
    {
        return $this->collection->key();   
    }

    /**
     * next 
     * 
     * @return void
     */
    public function next()
    {
        $this->collection->next( );
    }

    /**
     * rewind 
     * 
     * @return void
     */
    public function rewind()
    {
        $this->collection->rewind( );
    }

    /**
     * @return KVDdom_DomainObject
     * @throws Exception - Indien een ongeldige index gevraagd wordt.
     */
    public function seek ($index)
    {
        return $this->collection->seek( $index );
    }

    /**
     * @return boolean
     */
    public function valid()
    {
        return $this->collection->valid( );
    }

    /**
     * clear 
     * 
     * @return void
     */
    public function clear( )
    {
        $this->collection->clear( );
    }

    /**
     * hasDomainObject 
     * 
     * @param KVDdom_DomainObject $domainObject 
     * @return boolean
     */
    public function hasDomainObject( $domainObject )
    {
        return $this->collection->hasDomainObject( $domainObject );
    }

    /**
     * getDomainObjectWithId 
     * 
     * @param integer $id
     * @return KVDdom_DomainObject
     */
    public function getDomainObjectWithId( $id )
    {
        return $this->collection->getDomainObjectWithId( $id );
    }

    /**
     * toArray 
     * 
     * @return array
     */
    public function toArray( )
    {
        return $this->collection->toArray( );
    }

    /**
     * getFirst 
     * 
     * @return  mixed   Het eerste {@link KVDdom_DomainObjectCollection} uit de collection of false indien de collection leeg is.
     */
    public function getFirst( )
    {
        $this->rewind( );
        return $this->current( );
    }
}
