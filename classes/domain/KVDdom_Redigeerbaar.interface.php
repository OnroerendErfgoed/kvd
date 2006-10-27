<?php
/**
 * @package KVD.dom
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @version $Id$
 */
 
/**
 * KVDdom_Redigeerbaar 
 * 
 * @package KVD.dom
 * @since 27 okt 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_Redigeerbaar
{
   /**
     * Keur het domeinobject goed.
     */
    public function approve( );

    /**
     * Verwijder de geschiedenis van het object
     */
    public function verwijderGeschiedenis( );

}
?>
