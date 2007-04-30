<?php
/**
 * @package KVD.dom
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */

/**
 * KVDdom_DomainObjectCollection 
 * 
 * @package KVD.dom
 * @subpackage 
 * @since 2005
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
     * @deprecated Gebruik gewoon count
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

    /**
     * next 
     * 
     * @return void
     */
    public function next()
    {
        next ( $this->collection );
    }

    /**
     * rewind 
     * 
     * @return void
     */
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
        return !is_null( key( $this->collection ) );
    }

    /**
     * Verwijder alle objecten uit de collectie.
     */
    public function clear( )
    {
       foreach ( $this->collection as $object ) {
           $object->remove( );
       }
       $this->collection = array( );
    }

    /**
     * hasDomainObject 
     * 
     * @since 31 okt 2006
     * @param KVDdom_DomainObject $domainObject 
     * @return boolean True indien het object gevonden werd.
     */
    public function hasDomainObject( $domainObject )
    {
        $currentIndex = $this->key( );
        $this->rewind( );
        $found = false;
        while ( $this->valid( ) ) {
            if ( $domainObject->getId( ) === $this->current( )->getId( ) ) {
                $found = true;
                break;
            }
            $this->next( );
        }
        $this->seek( $currentIndex );
        return $found;
    }

    /**
     * getDomainObjectWithId 
     * 
     * @since 06 nov 2006
     * @param integer $id Zal normaal een integer zijn, eventueel een string.
     * @return mixed Het domainObject of null indien het niet gevonden werd.
     */
    public function getDomainObjectWithId ( $id )
    {
        $return = null;
        $currentIndex = $this->key( );
        $this->rewind( );
        while ( $this->valid( ) ) {
            if ( $this->current( )->getId( ) === $id ) {
               $return = $this->current( ); 
               break;
            }
            $this->next( );
        }
        $this->seek ( $currentIndex );
        return $return;
    }

    
    
}
?>
