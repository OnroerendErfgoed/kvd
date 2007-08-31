<?php
/**
 * @package KVD.thes
 * @subpackage Core
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_Sessie 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since 3 april 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Sessie implements KVDthes_ISessie
{
    /**
     * map 
     * 
     * @var KVDdom_GenericIdentityMap
     */
    private $identityMap;

    /**
     * @var KVDthes_Sessie 
     */
    private static $instance = null;

    private static $initialized = false;

    private $mapperRegistry;

    /**
     * __construct 
     * 
     * @return void
     */
    public function initialize( $config )
    {
        if ( self::$initialized ) {
            throw new LogicException ( 'Sessie was al geinitialiseerd.' );
        }
        $this->identityMap = new KVDdom_GenericIdentityMap( );
        $this->mapperRegistry = new KVDdom_MapperRegistry( new KVDdom_MapperFactory( $this , $config['dataMapperDirs'] , $config['dataMapperParameters'] ) );
        self::$initialized = true;
    }

    /**
     * getIdentityMap 
     * 
     * @return KVDdom_GenericIdentityMap
     */
    public function getIdentityMap( )
    {
        return $this->identityMap;
    }

    /**
     * getInstance 
     * 
     * @return KVDthes_Sessie
     */
    public static function getInstance( )
    {
        if ( self::$instance === null ) {
            self::$instance = new KVDthes_Sessie( );
        }
        return self::$instance;
    }

    public static function isInitialized( )
    {
        return self::$initialized;
    }

    /**
     * registerClean 
     * 
     * @param KVDthes_Term $term 
     * @return void
     */
    public function registerClean( KVDdom_DomainObject $do )
    {
        $this->identityMap->addDomainObject( $do );
    }

    public function getMapper( $domainObject )
    {
        return $this->mapperRegistry->getMapper( $domainObject );
    }
}
?>
