<?php
/**
 * @package KVD.dom
 * @subpackage collection
 * @since 29 mei 2008
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_NotNullDomainObjectCollection
 *
 * Een decorator voor een KVDdom_DomainObjectCollection die alle NullObjecten zal weglaten.
 * @package KVD.dom
 * @subpackage collection
 * @since 29 mei 2008
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdom_NotNullDomainObjectCollection extends KVDdom_DomainObjectCollectionDecorator
{
    /**
     * rewind
     *
     * @return void
     */
    public function rewind( )
    {
        parent::rewind( );
        if ( $this->valid( ) && !$this->current( )->isNull( ) ) {
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
        $this->collection->next( );
        if ( $this->valid( ) && !$this->current( )->isNull( ) ) {
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
        $count = 0;
        foreach ( $this->collection as $dom ) {
            if ( !$dom->isNull( ) ) {
                $count++;
            }
        }
        return $count;
        //return count ( array_filter( $this->collection , array ( 'this' , 'actiefFilter' ) ) );
    }

    /**
     * getTotalRecordCount
     *
     * @return integer
     */
    public function getTotalRecordCount( )
    {
        return $this->count( );
    }

    /**
     * @return KVDdom_DomainObject
     * @throws Exception - Indien een ongeldige index gevraagd wordt.
     */
    public function seek ($index)
    {
        if ( $index < 0 || $index >= $this->count() ) {
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
     * __toString
     *
     * @return string
     */
    public function __toString( )
    {
        $tmp = array( );
        foreach ( $this->collection as $item ) {
            if ( !$item->isNull( ) ) {
                $tmp[$item->getId( )] = $item;
            }
        }
        return implode ( ', ' , $tmp );
    }

    /**
     * toArray
     *
     * @return array
     */
    public function toArray( )
    {
        $tmp = array( );
        foreach ( $this->collection as $item ) {
            if ( !$item->isNull( ) ) {
                $tmp[$item->getId( )] = $item;
            }
        }
        return $tmp;
    }
}
?>
