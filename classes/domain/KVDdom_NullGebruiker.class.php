<?php
/**
 * @package     KVD.dom
 * @subpackage  gebruiker
 * @version     $Id$
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_NullGebruiker 
 * 
 * @package     KVD.dom
 * @subpackage  gebruiker
 * @since       2007
 * @copyright   2004-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_NullGebruiker implements KVDdom_Gebruiker
{
    /**
     * getGebruikersNaam 
     * 
     * @return string
     */
    public function getGebruikersNaam( )
    {
        return 'anoniem';
    }

    /**
     * getOmschrijving 
     * 
     * @return string
     */
    public function getOmschrijving( )
    {
        return 'anoniem';
    }

    /**
     * getId 
     * 
     * @return null
     */
    public function getId( )
    {
        return null;
    }

    /**
     * getClass 
     * 
     * @return string
     */
    public function getClass( )
    {
        return 'KVDdom_NullGebruiker';
    }
}
?>
