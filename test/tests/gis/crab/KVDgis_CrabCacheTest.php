<?php

class KVDgis_CrabCacheTest extends PHPUnit_Framework_TestCase
{

    private $_testCache;
    
    public function setUp()
    {
        $expirationTimes = array (  'testFunctie' => 100,
                                    'default' => 50);
        $this->_testCache = new KVDgis_CrabCache ( '/tmp/',$expirationTimes);
    }

    public function tearDown()
    {
        $this->_testCache = null;
    }

    /**
     * testCacheDirMoetBestaan 
     * 
     * @expectedException   InvalidArgumentException
     * @return void
     */
    public function testCacheDirMoetBestaan( )
    {
        $testCache = new KVDgis_CrabCache( '/bestaat/niet', array(  ) );
    }

    /**
     * testDefaultExpirationTimeMoetAanwezigZijn 
     * 
     * @expectedException   InvalidArgumentException
     * @return void
     */
    public function testDefaultExpirationTimeMoetAanwezigZijn( )
    {
        $expirationTimes = array (  'testFunctie' => 100 );

        $this->_testCache = new KVDgis_CrabCache ( '/tmp/',$expirationTimes);
    }

    public function testGetCacheName( )
    {
        $functionName = 'testFunctie';
        $parameters = array( 'p1',2);
        $result = $this->_testCache->getCacheName( $functionName,$parameters);
        $this->assertNotNull( $result );
        $this->assertType( 'string', $result );
        $this->assertEquals( $result , '/tmp/testFunctie#p1#2.crbcache');
    }

    public function testCachePut( )
    {
        $functionName = 'testFunctie';
        $parameters = array ( 'p1' , 2 );
        $testBuffer = 'Dit is een test voor KVDgis_CrabCache.';
        $result = $this->_testCache->cachePut( $functionName,$parameters,$testBuffer);
        $this->assertTrue( $result );
    }

    public function testCacheGet( )
    {
        $functionName = 'testFunctie';
        $parameters = array ( 'p1' , 2 );
        $result = $this->_testCache->cacheGet( $functionName,$parameters);
        $this->assertNotNull( $result );
        $this->assertType('string', $result );
    }

    public function testCacheClear( )
    {
        $functionName = 'testFunctie';
        $parameters = array ( 'p1' , 2 );
        $this->_testCache->cacheClear( $functionName,$parameters);

        $result = $this->_testCache->cacheGet( $functionName,$parameters);
        $this->assertFalse( $result );
    }
}

?>
