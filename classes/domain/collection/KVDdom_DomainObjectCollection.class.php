<?php
/**
 * @package KVD.dom
 * @subpackage collection
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */

/**
 * KVDdom_DomainObjectCollection 
 * 
 * @package KVD.dom
 * @subpackage collection
 * @since 2005
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
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
     */
    public function __construct ( array $collection )
    {
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
     * @return  mixed    Het huidige {@link KVDdom_DomainObject} of false indien er geen huidig object is.
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
        return $this->current( );
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
     * Opgelet, deze functie zal ook de opdracht geven aan de sessie om alle objecten te wissen. 
     * Gebruik ze dus niet als je alleen maar objecten wilt loskoppelen.
     * @deprecated Functie hoort hier niet thuis en is zelfs sterker dan de clear
     * methode van een @link KVDdom_EditeerbareDomainObjectCollection.
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
        return array_key_exists( $domainObject->getId( ), $this->collection );
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
        return isset( $this->collection[$id] ) ? $this->collection[$id] : null;
    }

    /**
     * __toString 
     * 
     * @return string
     */
    public function __toString( )
    {
        return implode ( ', ' , $this->collection );
    }

    /**
     * toArray 
     * 
     * @return array
     */
    public function toArray( )
    {
        return $this->collection;
    }

    /**
     * getFirst 
     * 
     * Geef het eerste domainobject uit de collection of false indien er geen elementen zijn. Let op dit, herinitialiseert de collection ook
     * en zet de interne collectie pointer dus terug op het begin. Gebruik dit dus niet tijdens een loop.
     * @return  mixed   Het eerste {@link KVDdom_DomainObject} uit de collection of false indien het niet bestaat.
     */
    public function getFirst( )
    {
        $this->rewind( );
        return $this->current( );
    }
}
?>
