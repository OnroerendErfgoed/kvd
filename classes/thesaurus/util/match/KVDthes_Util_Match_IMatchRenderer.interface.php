<?php
/**
 * @package    KVD.thes
 * @subpackage util
 * @version    $Id$
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Een interface voor het renderen van een relatie met een externe thesaurus.
 *
 * @package    KVD.thes
 * @subpackage util
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDthes_Util_Match_IMatchRenderer
{
    /**
     * getHtml
     *
     * Geef een html link terug indien link true is en anders gewoon tekst. 
     * Maar wel altijd al ge-escaped.
     *
     * @param KVDthes_Match $match
     * @param boolean $link
     * @return string
     */
    public function getHtml( KVDthes_Match $match, $link = true);
}
