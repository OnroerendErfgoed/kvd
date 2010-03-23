<?php
/**
 * @package KVD.thes
 * @subpackage Core
 * @version $Id: KVDthes_Sessie.class.php 332 2007-08-31 22:12:48Z vandaeko $
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_TestSessie 
 * Een basis sessie object om de domain classes te kunnen testen.
 * 
 * @package     KVD.dom
 * @subpackage  Test
 * @since       3 april 2007
 * @copyright   2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_TestSessie implements KVDdom_IWriteSessie
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
        $this->dirty = new KVDdom_GenericIdentityMap( );
        $this->new = new KVDdom_GenericIdentityMap( );
        $this->removed = new KVDdom_GenericIdentityMap( );
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
     * @return KVDdom_TestSessie
     */
    public static function getInstance( )
    {
        if ( self::$instance === null ) {
            self::$instance = new KVDdom_TestSessie( );
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
    public function registerClean( $do )
    {
        $this->identityMap->addDomainObject( $do );
    }

    /**
     * registerDirty 
     * 
     * @param KVDthes_Term $do 
     * @return void
     */
    public function registerDirty( $do )
    {
        $this->dirty->addDomainObject( $do );
    }

    /**
     * registerNew 
     * 
     * @param KVDthes_Term $do 
     * @return void
     */
    public function registerNew( $do )
    {
        $this->new->addDomainObject( $do );
    }

    /**
     * registerRemoved 
     * 
     * @param KVDthes_Term $do 
     * @return void
     */
    public function registerRemoved( $do )
    {
        $this->removed->addDomainObject( $do );
    }

    public function commit(  )
    {
        $this->dirty = new KVDdom_GenericIdentityMap( );
        $this->new = new KVDdom_GenericIdentityMap( );
        $this->removed = new KVDdom_GenericIdentityMap( );
    }

    public function getMapper( $domainObject )
    {
        return $this->mapperRegistry->getMapper( $domainObject );
    }

    public function getDatabaseConnection( $datamapper )
    {
        return null;
    }

    public function getLogger( )
    {
        return null;
    }

    public function getSqlLogger( )
    {
        return null;
    }

    public function getGebruiker( )
    {
        return new KVDdom_TestGebruiker( );
    }
}

class KVDdom_TestGebruiker implements KVDdom_Gebruiker
{
    public function getId( )
    {
        return 0;
    }

    public function getGebruikersNaam( )
    {
        return 'anoniem';
    }

    public function getClass( )
    {
        return get_class( $this );
    }

    public function getOmschrijving( )
    {
        return $this->getGebruikersNaam( );
    }

}
?>
