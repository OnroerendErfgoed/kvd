<?php
/**
 * @package   KVD.util
 * @version   $Id$
 * @copyright 2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Utility functies om te werken met het http protocol.
 *
 * @package   KVD.util
 * @since     1.6
 * @copyright 2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_HttpToolkit
{
    /**
     * Vorm een gewone uri om naar een uri zonder schema.
     *
     * Bijvoorbeeld: https:// of http:// wordt //
     *
     * @param string $uri
     * @return string
     */
    public static function schemelessUri( $uri )
    {
        return substr($uri,strpos($uri,'//'));
    }
}
