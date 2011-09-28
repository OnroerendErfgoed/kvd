<?php
/**
 * @package   KVD.agavi
 * @version   $Id$
 * @copyright 2004-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Class die messages uit het agavi framework logt 
 * met inbegrip van datum en tijd.
 *
 * @package   KVD.agavi
 * @since     1.0.0
 * @copyright 2004-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDag_DateTimeLayout extends Layout
{
    public function &format( $message )
    {
        $msg = sprintf( "[%s] %s", date( 'd-m-Y H:i:s'), $message->__toString( ));
        return $msg;
    }
}
?>
