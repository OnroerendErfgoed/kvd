<?php
/**
 * @package     KVD.dom
 * @subpackage  collection
 * @version     $Id$
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_LazyDomainObjectCollection 
 * 
 * Object om luie collecties van DomainObjects te beheren.
 * De collectie wordt niet meteen geladen, maar slechts in stukjes, wanneer dit nodig is. 
 * Zo kunnen grote collecties ook gebruikt worden zonder al te veel overhead.
 * @package     KVD.dom
 * @subpackage  collection
 * @since       2005
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
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
    protected $_chunkyQuery;

    /**
     * @var integer
     */
    protected $currentIndex = 0;

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
        if ( ! $this->valid()) {
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
        return !( $this->currentIndex >= $this->getTotalRecordCount( ) );
    }

    /**
     * @return KVDdom_DomainObject
     */
    public function seek ($index)
    {
        $this->currentIndex = ( $index < 0 || $index >= $this->getTotalRecordCount()) ? 0 : $index;
        return $this->current();
    }

    
    /**
     * fillChunk 
     *
     * Deze functie vult het volgende stuk van de collectie.
     * @return void
     */
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

    /**
     * setRowsPerChunk 
     * 
     * Stel in hoeveel rijen er geladen worden in 1 keer. Standaard wordt er gewerkt met blokken van 100 rijen.
     * Op sommige momenten ( zoals een rapport) kan het handig zijn de blokgrootte te verhogen.
     * @since   8 aug 2008
     * @param   integer     $rows 
     * @return  void
     */
    public function setRowsPerChunk( $rows )
    {
        $this->_chunkyQuery->setRowsPerChunk( $rows );
    }


    /**
     * hasDomainObject 
     * 
     * Methode werd overgenomen van de KVDdom_DomainObjectCollection zodat die daar efficienter gemaakt kan worden.
     * @since 8 nov 2007
     * @param KVDdom_DomainObject $domainObject 
     * @return boolean
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
     * Methode werd overgenomen van de KVDdom_DomainObjectCollection zodat die daar efficienter gemaakt kan worden.
     * @since 8 nov 2007
     * @param mixed $id Meestal een integer, soms een string.
     * @return void
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
    
    /**
     * isNull
     * @return boolean
     */
    public function isNull()
    {
        return false;
    }
    
    
    /**
     * newNull
     */
    public static function newNull()
    {
        return new KVDdom_NullLazyDomainObjectCollection();
    }
}


/**
 * KVDdom_NullLazyDomainObjectCollection 
 * 
 * Een null KVDdom_LazyDomainObjectCollection
 * @package     KVD.dom
 * @subpackage  collection
 * @since       2010
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Dieter Standaert <dieter.standaert@hp.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDdom_NullLazyDomainObjectCollection extends KVDdom_LazyDomainObjectCollection
{


    /**
     * __construct
     * 
     */
    public function __construct()
    {
        $this->_chunkyQuery = null;
        $this->currentIndex = 0;
    }

    /**
     * isNull
     * @return boolean
     */
    public function isNull()
    {
        return true;
    }

    /**
     * @return integer
     */
    public function getTotalRecordCount()
    {
        return 0;
    }

    /**
     * setRowsPerChunk 
     * 
     * Stel in hoeveel rijen er geladen worden in 1 keer. Standaard wordt er gewerkt met blokken van 100 rijen.
     * Op sommige momenten ( zoals een rapport) kan het handig zijn de blokgrootte te verhogen.
     * @since   8 aug 2008
     * @param   integer     $rows 
     * @return  void
     */
    public function setRowsPerChunk( $rows )
    {
        //
    }

}
?>
