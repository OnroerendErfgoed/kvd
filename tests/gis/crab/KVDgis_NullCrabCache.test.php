<?php

require_once ( GISMAP . '/crab/KVDgis_NullCrabCache.class.php');

class TestOfNullCrabCache extends UnitTestCase
{

    private $_testCache;
    
    public function setUp()
    {
        $this->_testCache = new KVDgis_NullCrabCache ();
    }

    public function tearDown()
    {
        $this->_testCache = null;
    }

    public function testGetCacheName( )
    {
        $functionName = 'testFunctie';
        $parameters = array( 'p1',2);
        $result = $this->_testCache->getCacheName( $functionName,$parameters);
        $this->assertNull( $result );
    }

    public function testCachePut( )
    {
        $functionName = 'testFunctie';
        $parameters = array ( 'p1' , 2 );
        $testBuffer = 'Dit is een test voor KVDgis_CrabCache.';
        $result = $this->_testCache->cachePut( $functionName,$parameters,$testBuffer);
        $this->assertFalse( $result );
    }

    public function testCacheGet( )
    {
        $functionName = 'testFunctie';
        $parameters = array ( 'p1' , 2 );
        $result = $this->_testCache->cacheGet( $functionName,$parameters);
        $this->assertFalse( $result );
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
