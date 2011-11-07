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
 * Pager om door collecties van DomainObjects te gaan.
 *
 * Volgt (grotendeels) de API van een PropelObjectPager.
 * @package KVD.dom
 * @subpackage collection
 * @since 2006
 * @copyright 2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_DomainObjectCollectionPager implements Countable
{
    /**
     * @var KVDdom_DomainObjectCollection
     */
    private $_collection;

    /**
     * @var integer
     */
    private $page;

    /**
     * @var integer
     */
    private $totalPages;

    /**
     * @var integer
     */
    private $rowsPerPage;

    
    /**
     * @param KVDdom_DomainObjectCollection     $collection
     * @param integer                           $page           De startPagina.
     * @param integer                           $rowsPerPage    Het aantal rijen per pagina.
     */
    public function __construct ( $collection , $page = 1 , $rowsPerPage = 25)
    {
        // Indien het om een luie collectie gaat dan zorgen we er voor dat de pagina-groottes van de pager en de collectie gelijk zijn.
        if ( $collection instanceof KVDdom_LazyDomainObjectCollection ) {
            $collection->setRowsPerChunk( $rowsPerPage );
        }
        $this->_collection = $collection;
        $this->rowsPerPage = $rowsPerPage;
        $this->setPage( $page );
    }
    /**
     * @return integer
     */
    public function getFirstPage()
    {
        return 1;
    }

    /**
     * @return integer
     */
    public function getLastPage()
    {
        return $this->getTotalPages();    
    }

    /**
     * @return mixed Paginanummer of false indien er geen vorige pagina is.
     */
    public function getPrev()
    {
        if ( $this->getPage() > $this->getFirstPage() ) {
				$prev = $this->getPage() - 1;
		} else {
				$prev = false;
		}
		return $prev;
    }

    /**
     * @return mixed Paginanummer of false indien er geen volgende pagina is.
     */
    public function getNext()
    {
        if ( $this->getPage() < $this->getLastPage() ) {
				$next = $this->getPage() + 1;
		} else {
				$next = false;
		}
		return $next;
    }

    /**
     * @return integer Het totale aantal pagina's
     */
    public function getTotalPages()
    {
        if ( !isset ( $this->totalPages ) ) {
			if ( $this->rowsPerPage > 0) {
					$this->totalPages = ceil ( $this->getTotalRecordCount() / $this->rowsPerPage );
			}
            if ( $this->totalPages < 1 ) {
                $this->totalPages = 1;
            }
		}
		return $this->totalPages;    
    }

    /**
	 * @param integer $range
	 * @return array $links
	 */
    public function getPrevLinks( $range = 5 )
    {
		$start = $this->getPage() - 1;
		$end = $start - $range;
		$links = array();
		for ( $i=$start ; $i>$end ; $i-- ) {
			if ( $i < $this->getFirstPage() ) {
					break;
			}
			$links[] = $i;
		}
		return array_reverse($links);    
    }

    /**
	 * @param integer $range
	 * @return array $links
	 */
    public function getNextLinks( $range=5 )
    {
		$start = $this->getPage() + 1;
		$end = $start + $range;
		$links = array();
		for ( $i=$start ; $i<$end ; $i++ ) {
			if ( $i > $this->getLastPage() ) {
					break;
			}
			$links[] = $i;
		}
		return $links;    
    }

    /**
     * @return integer
     */
    public function getPage()
    {
        return $this->page;    
    }

    /**
     * @return integer
     */
    public function getRowsPerPage()
    {
        return $this->rowsPerPage;    
    }

    /**
     * @return integer
     */
    private function calculateStart()
    {
        $result = ($this->page - 1) * $this->rowsPerPage;

        if ( $result < 0 ) {
            throw new UnexpectedValueException ( "Start zou niet kleiner dan 0 mogen zijn, maar is $result.");
        }
        return $result;
    }

    /**
     * @return integer
     */
    public function getTotalRecordCount()
    {
        return $this->_collection->getTotalRecordCount();    
    }

    /**
     * count 
     * 
     * @since   29 aug 2008
     * @return  integer
     */
    public function count( )
    {
        return $this->_collection->getTotalRecordCount( );
    }

    /**
     * @return LimitIterator
     */
    public function getResult()
    {
        return new LimitIterator ( $this->_collection , $this->calculateStart() , $this->rowsPerPage );    
    }

    /**
     * @param integer $page
     */
    private function setPage( $page )
    {
        $page = ( int ) $page;
        if ( $page < $this->getFirstPage( ) ) {
            $page = $this->getFirstPage( );
        }
        if ( $page > $this->getLastPage( ) ) {
            $page = $this->getLastPage( );
        }
        $this->page = $page;
    }
}
?>
