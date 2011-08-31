<?php
/**
 * @package    KVD.dom
 * @subpackage chunky
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Interface om een stuk uit een verzameling van objecten te benaderen.
 *
 * Deze interface kan gebruikt worden door een @link{KVDdom_LazyDomainObjectCollection} 
 * om objecten te laden uit een databron. Door deze interface te gebruiken, wordt de 
 * exacte databron geabstraheerd en verborgen gehouden voor de luie collection. Dit 
 * stelt ons in staat om een luie collection eenvoudig te mocken met een normale collection.
 * 
 * @package    KVD.dom
 * @subpackage chunky
 * @since      sep 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_Chunky_IQuery
{
    /**
     * Vraag de records op uit de chunk die momenteel actief is. 
     *
     * We gaan er van uit dat als er geen explicite chunk gevraagd is, de 
     * eerste chunk wordt gebruikt.  
     * 
     * @return array Een array van @link{KVDdom_DomainObject} objecten.
     */
    public function getDomainObjects( );

    /**
     * Welke chunk wordt momenteel behandeld?
     *
     * @return integer Het nummer van de chunk die momenteel actief is.
     */
    public function getChunk();

    /**
     * Hoeveel rijen telt een chunk?
     *
     * @return integer Het aantal rijen in een chunk.
     */
    public function getRowsPerChunk()

    /**
     * Hoeveel records zitten er opgeslagen in de query?
     * 
     * @return integer Het aantal records in de query.
     */
    public function getTotalRecordCount()

    /**
     * Het totale aantal chunks aanwezig in de query, op basis van het aantal 
     * rijen per chunk.
     *
     * @return integer Het aantal chunks.
     */
    public function getTotalChunksCount()

    /**
     * Wijzig de actieve chunk.
     * _
     * @param integer $chunk Het nummer van de chunk die nu actief moet worden.
     */
    public function setChunk ( $chunk );

    /**
     * Stel het aantal rijen per chunk in.
     *
     * @param integer $rowsPerPage
     */
    public function setRowsPerChunk ( $rowsPerChunk );
}
?>
