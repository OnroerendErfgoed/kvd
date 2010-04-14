<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDthes_DomainObjectCollection extends KVDdom_DomainObjectCollection
{


    /**
     * sort 
     * 
     * Sorteer de relaties op een bepaalde manier, standaard wordt er alfabetisch gesorteerd op de term.
     * @param integer $sortMethod 
     * @return void
     */
    public function sort( $sortMethod = KVDthes_TermSorter::SORT_TERM )
    {
        if ( $sortMethod > KVDthes_TermSorter::SORT_UNSORTED && array_key_exists( $sortMethod, KVDthes_TermSorter::$methodMap) ) {
            usort( $this->relations, array ( new KVDthes_TermSorter($sortMethod), "compareTerms" ) );
        }
    }



}



?>


