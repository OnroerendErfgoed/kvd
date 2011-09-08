<?php

class KVDdom_DomainObjectCollectionTest extends PHPUnit_Framework_TestCase
{
    private $_DomainObjectCollection;

    private $_domObject;

    private $_domObject2;

    private $_domObject3;

    function setUp( )
    {
        $this->_domObject = new KVDdom_SimpleTestDomainObject( 54321, 'Object 54321' );
        $this->_domObject2 = new KVDdom_SimpleTestDomainObject( 9876, 'Object 9876' );
        $this->_domObject3 = new KVDdom_SimpleTestDomainObject( 123456789, 'Object 123456789' );
        $this->_testArray = array (    54321       => $this->_domObject,
                                 9876        => $this->_domObject2,
                                 123456789   => $this->_domObject3 );
        $this->_domainObjectCollection = new KVDdom_DomainObjectCollection( $this->_testArray );
    }

    function tearDown( )
    {
        $this->_domainObjectCollection = null;
        $this->_domObject = null;
        $this->_domObject2 = null;
        $this->_domObject3 = null;
    }

    function testOneDomainObject()
    {

        $collection = array (  54321 => $this->_domObject );
        $this->_domainObjectCollection = new KVDdom_DomainObjectCollection( $collection );
        $this->assertEquals ( 1, $this->_domainObjectCollection->getTotalRecordCount( ) );
        $this->_domainObjectCollection->seek( 0 );
        $this->assertNotNull ( $this->_domainObjectCollection->current( ) , 'Index 0 bevat geen element');
        $this->assertEquals ( $this->_domObject, $this->_domainObjectCollection->current( ) );
        foreach ($this->_domainObjectCollection as $domObj) {
            $this->assertNotNull($domObj);
        }
        $this->assertFalse ($this->_domainObjectCollection->valid());
        $this->_domainObjectCollection->rewind();
        $this->assertEquals ( $this->_domObject, $this->_domainObjectCollection->current() );
        $this->assertEquals ( 54321, $this->_domainObjectCollection->key() );
        $this->_domainObjectCollection->next();
        $this->assertFalse ($this->_domainObjectCollection->current());
    }


    function testMultipleDomainObjects()
    {
        
        $this->assertEquals ( 3, $this->_domainObjectCollection->getTotalRecordCount( ) );
        $this->_domainObjectCollection->seek( 1 );
        $this->assertNotNull ( $this->_domainObjectCollection->current( ) , 'Index 1 bevat geen element');
        $this->assertEquals ( $this->_domObject2, $this->_domainObjectCollection->current( ) );
        foreach ($this->_domainObjectCollection as $domObj) {
            $this->assertNotNull($domObj);
        }
        $this->assertFalse ($this->_domainObjectCollection->valid());
        $this->_domainObjectCollection->rewind();
        $this->assertEquals ($this->_domObject, $this->_domainObjectCollection->current() );
        $this->assertEquals (54321, $this->_domainObjectCollection->key() );
        $this->_domainObjectCollection->next( );
        $this->assertEquals ($this->_domObject2, $this->_domainObjectCollection->current() );
        $this->assertEquals ($this->_domObject2, $this->_domainObjectCollection->current() );
        $this->_domainObjectCollection->next( );
        $this->assertEquals ($this->_domObject3,  $this->_domainObjectCollection->current() );
    }

    function testLimitIterator( )
    {
        $this->assertEquals ( 3, $this->_domainObjectCollection->getTotalRecordCount( ) );
        $limitIT = new LimitIterator (  $this->_domainObjectCollection, 1, 2);
        $limitIT->rewind( );
        $this->assertEquals ($this->_domObject2, $limitIT->current() );
        $limitIT->next( );
        $this->assertEquals ($this->_domObject3, $limitIT->current() );
    }

    function testHasDomainObject( )
    {
        $this->assertTrue( $this->_domainObjectCollection->hasDomainObject( $this->_domObject3) );
        $this->assertTrue( $this->_domainObjectCollection->hasDomainObject( $this->_domObject2 ) );
        $this->assertTrue( $this->_domainObjectCollection->hasDomainObject( $this->_domObject ) );
    }

    function testHasDomainObjectKeepsPosition( )
    {
        $this->_domainObjectCollection->next( );
        $this->assertTrue( $this->_domainObjectCollection->hasDomainObject( $this->_domObject3) );
        $this->assertEquals ( 9876, $this->_domainObjectCollection->key( ) );
        $this->_domainObjectCollection->next( );
        $this->assertTrue( $this->_domainObjectCollection->hasDomainObject( $this->_domObject ) );
        $this->assertEquals ( 123456789, $this->_domainObjectCollection->key( ) );
    }

    function testGetDomainObjectWithId( )
    {
        $this->assertNotNull( $this->_domainObjectCollection->getDomainObjectWithId( 123456789 ) );
        $this->assertNull( $this->_domainObjectCollection->getDomainObjectWithId( 15 ) );
        $this->assertSame( $this->_domainObjectCollection->getDomainObjectWithId( 9876 ) , $this->_domObject2 );
    }

    function testgetDomainObjectWithIdKeepsPosition( )
    {
        $this->_domainObjectCollection->next( );
        $this->assertSame( $this->_domainObjectCollection->getDomainObjectWithId( 9876 ) , $this->_domObject2 );
        $this->assertEquals ( 9876, $this->_domainObjectCollection->key( ) );
        $this->assertSame( $this->_domainObjectCollection->getDomainObjectWithId( 54321 ) , $this->_domObject );
        $this->assertEquals ( 9876, $this->_domainObjectCollection->key( ) );
    }

    public function testToArray(  )
    {
        $this->assertEquals( $this->_testArray, $this->_domainObjectCollection->toArray( ) );
    }

    public function testToString(  )
    {
        $this->assertEquals( 'Object 54321, Object 9876, Object 123456789', $this->_domainObjectCollection->__toString( ) );
    }

}
?>
