<?php
/**
 * @package   KVD.database
 * @version   $Id$
 * @copyright 2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Uitbreiding van de Jargon PagedQuery die alle nodige links berekent.
 *
 * Volgt (grotendeels) de API van een PropelObjectPager.
 *
 * @package   KVD.database
 * @since     1.0.0
 * @copyright 2006-2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdb_JargonPagedQuery extends PagedQuery
{
    /**
     * @var integer
     */
    private $totalPages;

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
            if ( $this->max > 0) {
                $this->totalPages = ceil ( $this->getTotalRecordCount() / $this->max );
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

}
?>
