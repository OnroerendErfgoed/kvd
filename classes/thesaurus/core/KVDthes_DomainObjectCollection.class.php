<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @copyright  2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Dieter Standaert <dieter.standaert@hp.com>
 */

/**
 * KVDthes_DomainObjectCollection
 *
 * Een KVDdom_DomainObjectCollection met sort methode.
 *
 * @package    KVD.thes
 * @subpackage core
 * @since      14 april 2010
 * @copyright  2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Dieter Standaert <dieter.standaert@hp.com>
 */
class KVDthes_DomainObjectCollection extends KVDdom_DomainObjectCollection
{

    /**
     * sort
     *
     * Sorteer de relaties op een bepaalde manier, standaard
     * wordt er alfabetisch gesorteerd op de term.
     *
     * @param integer $sortMethod
     * @return void
     */
    public function sort( $sortMethod = KVDthes_TermSorter::SORT_TERM )
    {
        if ( $sortMethod > KVDthes_TermSorter::SORT_UNSORTED &&
             array_key_exists( $sortMethod, KVDthes_TermSorter::$methodMap) ) {
            usort( $this->collection,
                   array ( new KVDthes_TermSorter($sortMethod), "compareTerms" ) );
        }
    }



}

?>
