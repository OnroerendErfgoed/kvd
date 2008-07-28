<?php
/**
 * @package     KVD.dom
 * @subpackage  collection
 * @version     $Id$
 * @copyright   2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_ActieveDomainObjectCollection 
 * 
 * @package     KVD.dom
 * @subpackage  collection
 * @since       30 april 2007
 * @copyright   2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class OARdom_ActieveDomainObjectCollection extends KVDdom_DomainObjectCollectionDecorator
{
    /**
     * rewind 
     * 
     * @return void
     */
    public function rewind( )
    {
        parent::rewind( );
        if ( $this->valid( ) && !$this->current( )->isActief( ) ) {
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
        if ( $this->valid( ) && !$this->current( )->isActief( ) ) {
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
            if ( $dom->isActief( ) ) {
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
}
?>
