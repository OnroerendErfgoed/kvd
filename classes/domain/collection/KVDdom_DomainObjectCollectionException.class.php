<?php
/**
 * @package KVD.dom
 * @subpackage collection
 * @since 17 nov 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_OngeldigTypeException 
 * 
 * @package KVD.dom
 * @subpackage collection
 * @since 17 nov 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_OngeldigTypeException extends Exception
{
    public function __construct( $msg, $type, $expectedType )
    {
        $this->message = '[Er werd een ' . $expectedType . ' verwacht, er werd een ' . $epxectedType . 'ontvangen.] ' . $msg;
    }
}
?>
