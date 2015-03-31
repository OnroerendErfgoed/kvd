<?php
/**
 * @package     KVD.util
 * @subpackage  Date
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDutil_DateTime
 *
 * @package     KVD.util
 * @subpackage  Date
 * @since       25 aug 2008
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDutil_DateTime extends DateTime
{
    const FORMAT = 'd-m-Y H:i';

    /**
     * __toString
     *
     * @return string
     */
    public function __toString( )
    {
        return (string) parent::format( self::FORMAT );
    }

    /**
     * newNull
     *
     * @return  KVDutil_NullDateTime
     */
    public static function newNull( )
    {
        return new KVDutil_NullDateTime( );
    }

    /**
     * isNull
     *
     * @return boolean
     */
    public function isNull( )
    {
        return false;
    }
}

/**
 * KVDutil_NullDateTime
 *
 * @package     KVD.util
 * @subpackage  date
 * @since       25 aug 2008
 * @copyright   2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDutil_NullDateTime extends KVDutil_DateTime
{
    /**
     * isNull
     *
     * @return  boolean
     */
    public function isNull( )
    {
        return true;
    }

    /**
     * format
     *
     * @return  null
     */
    public function format( $format = parent::FORMAT )
    {
        return null;
    }

    /**
     * __toString
     *
     * @return  null
     */
    public function __toString( )
    {
        return '';
    }
}

