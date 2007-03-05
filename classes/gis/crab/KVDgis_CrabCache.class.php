<?php
/**
 * @package KVD.gis
 * @subpackage crab
 * @version $Id$
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDgis_CrabCache
 *
 * Class om calls van KVDgis_Crab1Gateway en KVDgis_Crab2Gateway te cachen.
 * @package KVD.gis
 * @subpackage crab
 * @since jan 2006
 * @copyright 2004-2006 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDgis_CrabCache
{

    /**
     * @var string
     */
    private $cacheDir;

    /**
     * @var array
     */
    private $expirationTimes;

    /**
     * @var array
     */
    private $loadedCaches;
    
    /**
     * @param string $cacheDir
     * @param array $expirationTimes
     */
    public function __construct( $cacheDir, $expirationTimes )
    {
        if ( !is_dir( $cacheDir) ) {
            throw new InvalidArgumentException ( 'De parameter $cacheDir van KVDgis_CrabCache is een directory die niet bestaat!');
        }
        if ( strrpos( $cacheDir , '/' ) !== strlen( $cacheDir) - 1  ) {
            $cacheDir .= '/';
        }
        $this->cacheDir = $cacheDir;
        if ( !isset( $expirationTimes['default'])) {
            throw new InvalidArgumentException ( 'De parameter $expirationTimes van KVDgis_CrabCache moet een sleutel default bevatten!');
        }
        $this->expirationTimes = $expirationTimes;
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
        return $this->cacheDir . $functionName . '#' . implode( '#',$parameters) . '.crbcache';
    } 

    /**
     * @param string $functionName
     * @return integer
     */
    private function getExpirationTime ( $functionName )
    {
        return isset( $this->expirationTimes[$functionName] ) ? $this->expirationTimes[$functionName] : $this->expirationTimes['default'];
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
        $cache = $this->checkCache ( $functionName , $parameters );
        return $cache->put( $buffer );
    }

    /**
     *
     * @param string cacheName      
     * @return mixed
     * @access public
     */
    public function cacheGet( $functionName, $parameters ) 
    {
        $cache = $this->checkCache ( $functionName , $parameters );
        return $cache->get();
    }

    /**
     * @param string $functionName
     * @param array $parameters
     * @return KVDutil_CacheFile
     */
    private function checkCache ( $functionName , $parameters ) 
    {
        $cacheName = $this->getCacheName( $functionName , $parameters );
        if ( isset( $this->loadedCaches[$cacheName]) ) {
            return $this->loadedCaches[$cacheName];
        } else {
            $cache = new KVDutil_CacheFile( $cacheName,$this->getExpirationTime( $functionName) );
            $this->loadedCaches[$cacheName] = $cache;
            return $cache;
        }
    }

    /**
     * @param string $functionName
     * @param array $parameters
     * @return void
     */
    public function cacheClear ( $functionName, $parameters)
    {
        $cache = $this->checkCache( $functionName , $parameters);
        $cache->remove( );
    }

    /**
     * @return void
     */
    public function clearAllCaches( ) 
    {
        $directory = new DirectoryIterator( $this->cacheDir );
        foreach( $directory as $file ) {
            if ( $file->isFile( ) ) {
                if ( strpos( $file->getFilename( ) , '.crbcache') != false )
                    @unlink ( $file->getPathname( ) );
            }
        }
    }

} // end of KVDgis_CrabCache
?>
