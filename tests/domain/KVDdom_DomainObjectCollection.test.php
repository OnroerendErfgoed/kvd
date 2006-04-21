<?php

Mock::generate('GenericDomainObject');

class TestOfDomainObjectCollection extends UnitTestCase
{
    private $_DomainObjectCollection;

    private $_domObject;

    private $_domObject2;

    function setUp( )
    {
        $this->_domObject = new MockGenericDomainObject( $this);
        $this->_domObject->setReturnValue( 'getId' , '54321');
        $this->_domObject->setReturnValue( 'getClass' , 'GenericDomainObject');

        $this->_domObject2 = new MockGenericDomainObject( $this );
        $this->_domObject2->setReturnValue('getId', '9876');
        $this->_domObject2->setReturnValue('getClass', 'GenericDomainObject');

    }

    function tearDown( )
    {
        $this->_domObject->tally( );
        $this->_domObject2->tally( );
        $this->_domainObjectCollection = null;
        $this->_domObject = null;
        $this->_domObject2 = null;
    }

    function testOneDomainObject()
    {

        $collection = array (  $this->_domObject );
        $this->_domainObjectCollection = new KVDdom_DomainObjectCollection( $collection );
        $this->assertEqual ( $this->_domainObjectCollection->getTotalRecordCount( ), 1  );
        $this->_domainObjectCollection->seek( 0 );
        $this->assertNotNull ( $this->_domainObjectCollection->current( ) , 'Index 0 bevat geen element');
        $this->assertEqual ( $this->_domainObjectCollection->current( ) , $this->_domObject );
        foreach ($this->_domainObjectCollection as $domObj) {
            $this->assertNotNull($domObj);
        }
        $this->assertFalse ($this->_domainObjectCollection->valid());
        $this->_domainObjectCollection->rewind();
        $this->assertEqual ($this->_domainObjectCollection->current() , $this->_domObject );
        $this->assertEqual ($this->_domainObjectCollection->key() , 0 );
        $this->assertFalse ($this->_domainObjectCollection->next());
        $this->assertFalse ($this->_domainObjectCollection->current());
        $this->assertFalse ($this->_domainObjectCollection->next());
    }


    function testMultipleDomainObjects()
    {
        
        $domObject3 = new MockGenericDomainObject( $this );
        $domObject3->setReturnValue('getId', '123456789');
        $domObject3->setReturnValue('getClass', 'GenericDomainObject');
        $collection = array (  $this->_domObject , $this->_domObject2 , $domObject3 );
        $this->_domainObjectCollection = new KVDdom_DomainObjectCollection( $collection );
        $this->assertEqual ( $this->_domainObjectCollection->getTotalRecordCount( ), 3  );
        $this->_domainObjectCollection->seek( 1 );
        $this->assertNotNull ( $this->_domainObjectCollection->current( ) , 'Index 1 bevat geen element');
        $this->assertEqual ( $this->_domainObjectCollection->current( ) , $this->_domObject2 );
        foreach ($this->_domainObjectCollection as $domObj) {
            $this->assertNotNull($domObj);
        }
        $this->assertFalse ($this->_domainObjectCollection->valid());
        $this->_domainObjectCollection->rewind();
        $this->assertEqual ($this->_domainObjectCollection->current() , $this->_domObject );
        $this->assertEqual ($this->_domainObjectCollection->key() , 0 );
        $this->_domainObjectCollection->next( );
        $this->assertEqual ($this->_domainObjectCollection->current() , $this->_domObject2 );
        $this->assertEqual ($this->_domainObjectCollection->current() , $this->_domObject2);
        $this->_domainObjectCollection->next( );
        $this->assertEqual ($this->_domainObjectCollection->current() , $domObject3 );
        $this->assertFalse ($this->_domainObjectCollection->next());
    }

    function testLimitIterator( )
    {
        $domObject3 = new MockGenericDomainObject( $this );
        $domObject3->setReturnValue('getId', '123456789');
        $domObject3->setReturnValue('getClass', 'GenericDomainObject');
        $collection = array (  $this->_domObject , $this->_domObject2 , $domObject3 );
        $this->_domainObjectCollection = new KVDdom_DomainObjectCollection( $collection );
        $this->assertEqual ( $this->_domainObjectCollection->getTotalRecordCount( ), 3  );
        $limitIT = new LimitIterator (  $this->_domainObjectCollection, 1, 2);
        $limitIT->rewind( );
        $this->assertEqual ($limitIT->current() , $this->_domObject2 );
        $limitIT->next( );
        $this->assertEqual ($limitIT->current() , $domObject3 );
    }
}
?>