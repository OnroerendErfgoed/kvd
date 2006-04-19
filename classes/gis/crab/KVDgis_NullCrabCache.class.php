<?php
/**
 * @package KVD.gis.crab
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * @package KVD.gis.crab
 * @since 1.0.0
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 */
class KVDgis_NullCrabCache extends KVDgis_CrabCache
{
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
