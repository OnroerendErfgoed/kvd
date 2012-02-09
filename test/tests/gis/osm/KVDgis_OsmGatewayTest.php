<?php
class KVDgis_OsmGatewayTest extends PHPUnit_Framework_TestCase
{
    protected $parameters;

    public function setUp( )
    {
        $this->parameters = array( );
        if ( defined ( 'OSM_PROXY_HOST' ) && OSM_PROXY_HOST != '' ) {
            $this->parameters['proxy_host'] = OSM_PROXY_HOST;
            if ( defined( 'OSM_PROXY_PORT' ) && OSM_PROXY_PORT != '') {
                $this->parameters['proxy_port'] = OSM_PROXY_PORT;
            }
        }
        $this->gateway = new KVDgis_OsmGateway( $this->parameters );
    }

    public function tearDown( )
    {
        $this->gateway = null;
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testActiveCacheRequiresCacheDir()
    {
        $this->parameters['cache'] ['active'] = true;
        $this->gateway = new KVDgis_OsmGateway( $this->parameters );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testCacheDirMustBeWriteable( )
    {
        $this->parameters['cache'] ['active'] = true;
        $this->parameters['cache'] ['cache_dir'] = '/';
        $this->gateway = new KVDgis_OsmGateway( $this->parameters );
    }

    public function testMapnik( )
    {
        $z = 16;
        $x = 33360;
        $y = 21950;

        $res = $this->gateway->getMapnik( $z, $x, $y );
        $this->assertNotEmpty( $res );
    }

    public function testOsma( )
    {
        $z = 16;
        $x = 33360;
        $y = 21950;

        $res = $this->gateway->getOsma( $z, $x, $y );
        $this->assertNotEmpty( $res );
    }

    public function testMapnikWritesCache( )
    {
        $this->parameters['cache']['active'] = true;
        $this->parameters['cache']['cache_dir'] = __DIR__ . '/cache';
        mkdir( $this->parameters['cache']['cache_dir'] );
        $this->gateway = new KVDgis_OsmGateway( $this->parameters );

        $z = 16;
        $x = 33360;
        $y = 21950;

        $res = $this->gateway->getMapnik( $z, $x, $y );
        $this->assertNotEmpty( $res );

        $this->assertFileExists( $this->parameters['cache']['cache_dir'] . '/mapnik/16/33360/21950.png' );
        unlink( $this->parameters['cache']['cache_dir'] . '/mapnik/16/33360/21950.png');
        rmdir( $this->parameters['cache']['cache_dir'] . '/mapnik/16/33360' );
        rmdir( $this->parameters['cache']['cache_dir'] . '/mapnik/16' );
        rmdir( $this->parameters['cache']['cache_dir'] . '/mapnik' );
        rmdir( $this->parameters['cache']['cache_dir'] . '/osma' );
        rmdir( $this->parameters['cache']['cache_dir'] );
    }

    public function testMapnikReadsCache( )
    {

        $this->parameters['cache']['active'] = true;
        $this->parameters['cache']['cache_dir'] = __DIR__ . '/cache';
        mkdir( $this->parameters['cache']['cache_dir'] );
        $this->gateway = new KVDgis_OsmGateway( $this->parameters );

        $z = 16;
        $x = 33360;
        $y = 21950;

        $res = $this->gateway->getMapnik( $z, $x, $y );
        $this->assertNotEmpty( $res );

        $this->assertFileExists( $this->parameters['cache']['cache_dir'] . '/mapnik/16/33360/21950.png' );

        //Tweede request
        //Voorlopig geen idee hoe we kunnen checken dat de cache effectief werd 
        //aangesproken.
        $res = $this->gateway->getMapnik( $z, $x, $y );
        $this->assertNotEmpty( $res );

        unlink( $this->parameters['cache']['cache_dir'] . '/mapnik/16/33360/21950.png');
        rmdir( $this->parameters['cache']['cache_dir'] . '/mapnik/16/33360');
        rmdir( $this->parameters['cache']['cache_dir'] . '/mapnik/16' );
        rmdir( $this->parameters['cache']['cache_dir'] . '/mapnik' );
        rmdir( $this->parameters['cache']['cache_dir'] . '/osma' );
        rmdir( $this->parameters['cache']['cache_dir'] );
    }
}
?>
