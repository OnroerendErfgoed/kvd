<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_DomainObjectCollectionPager.class.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
 */

/**
 * Pager om door collecties van DomainObjects te gaan.
 *
 * Volgt (grotendeels) de API van een PropelObjectPager.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_DomainObjectCollectionPager
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
     * @param KVDdom_DomainObjectCollection $collection
     * @param integer $page De startPagina.
     * @param integer $rowsPerPage Het aantal rijen per pagina.
     */
    public function __construct ( $collection , $page = 1 , $rowsPerPage = 25)
    {
        if ( $collection == null) {
            $collection = new KVDdom_DomainObjectCollection( array( ) );
        }
        $this->_collection = $collection;
        $this->page = $page;
        $this->rowsPerPage = $rowsPerPage;
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
        if ( $this->getPage() != $this->getFirstPage() ) {
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
        if ( $this->getPage() != $this->getLastPage() ) {
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
			} else {
					$this->totalPages = 0;
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
		$end = $this->getPage() - $range;
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
		$end = $this->getPage() + $range;
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
        return ($this->page - 1) * $this->rowsPerPage;    
    }

    /**
     * @return integer
     */
    public function getTotalRecordCount()
    {
        return $this->_collection->getTotalRecordCount();    
    }

    /**
     * @return LimitIterator
     */
    public function getResult()
    {
        return new LimitIterator ( $this->_collection , $this->calculateStart() , $this->rowsPerPage );    
    }
}
?>
