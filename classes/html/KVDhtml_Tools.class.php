<?php
/**
 * @package KVD.html
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDhtml_Tools 
 * 
 * @package KVD.html
 * @since 16 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDhtml_Tools
{
    /**
     * out 
     *
     * Methode om output goed te escapen naar html. 
     * @param string $value 
     * @return string Opgekuiste string
     */
    public static function out( $value )
    {
        return htmlentities( $value , ENT_QUOTES , 'UTF-8' );
    }
}
?>
