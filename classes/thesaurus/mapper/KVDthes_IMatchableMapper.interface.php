<?php
/**
 * @package     KVD.thes
 * @subpackage  mapper
 * @version     $Id$
 * @copyright   2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Een interface die aangeeft dat het domainobject dat bij deze mapper hoort 
 * matches kan hebben.
 * 
 * @package     KVD.thes
 * @subpackage  mapper
 * @since       1.6
 * @copyright   2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDthes_IMatchableMapper
{
    /**
     * loadMatches
     *
     * @param KVDthes_Matchable $matchable
     * @return KVDthes_Matches
     */
    public function loadMatches(KVDthes_Matchable $matchable);
}
