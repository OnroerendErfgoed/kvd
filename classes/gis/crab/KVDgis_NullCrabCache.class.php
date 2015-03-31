<?php
/**
 * @package    KVD.gis
 * @subpackage crab
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDgis_NullCrabCache
 *
 * Een implementatie van het Special Case pattern, zorgt er voor dat er niets gecached wordt.
 *
 * @package    KVD.gis
 * @subpackage crab
 * @since      jan 2006
 * @copyright  2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDgis_NullCrabCache extends KVDgis_CrabCache
{
    public function __construct ()
    {

    }

    /**
     *
     * @param string functionName
     * @param array parameters
     * @return string
     * @access public
     */
    public function getCacheName( $functionName,  $parameters )
    {
        return null;
    }

    /**
     *
     * @param string cacheName
     * @param string buffer
     * @return bool
     * @access public
     */
    public function cachePut( $functionName , $parameters ,  $buffer )
    {
        return false;
    }

    /**
     *
     * @param string cacheName
     * @return mixed
     * @access public
     */
    public function cacheGet( $functionName, $parameters )
    {
        return false;
    }

    /**
     * @param string $functionName
     * @param array $parameters
     * @return void
     */
    public function cacheClear ( $functionName, $parameters)
    {
    }

    /**
     * @return void
     */
    public function clearAllCaches( )
    {
    }

} // end of KVDgis_NullCrabCache
?>
