<?php
/**
 * @package KVD.dom
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_Gebruiker 
 * 
 * Alle gebruikers-objecten voor een applicatie moeten aan deze interface voldoen zodat de datamappers kunnen 
 * nagaan wie de gebruiker is die een update uitvoert.
 * @package KVD.dom
 * @since 2005
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_Gebruiker extends KVDdom_DomainObject {

    /**
     * getGebruikersNaam 
     * 
     * @return string
     */
    public function getGebruikersNaam();

}
?>
