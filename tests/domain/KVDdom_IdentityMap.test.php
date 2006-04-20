<?php

class GenericDomainObject
{
    public function getId( )
    {
        return 1;
    }

    public function getClass( )
    {
        return GenericDomainObject;
    }
}

Mock::generate('GenericDomainObject');

class TestOfGenericIdentityMap extends UnitTestCase
{
    private $_identityMap;

    private $_domObject;

    private $_domObject2;

    function setUp( )
    {
        $this->_identityMap = new KVDdom_GenericIdentityMap( );
        $this->_domObject = new MockGenericDomainObject( $this);
        $this->_domObject->setReturnValue( 'getId' , '54321');
        $this->_domObject->setReturnValue( 'getClass' , 'GenericDomainObject');
        $this->_domObject->expectAtLeastOnce( 'getId');

        $this->_domObject2 = new MockGenericDomainObject( $this );
        $this->_domObject2->setReturnValue('getId', '9876');
        $this->_domObject2->setReturnValue('getClass', 'GenericDomainObject');
    }

    function tearDown( )
    {
        $this->_domObject->tally( );
        $this->_domObject2->tally( );
        $this->_identityMap = null;
        $this->_domObject = null;
        $this->_domObject2 = null;
    }

    function testOneDomainObject()
    {
        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->assertNotNull ( $this->_identityMap->getDomainObject('GenericDomainObject' , 54321) );
        $returnObj = $this->_identityMap->getDomainObject ( 'GenericDomainObject', 54321);
        $this->assertReference ( $returnObj , $this->_domObject );
        $this->assertNull($this->_identityMap->getDomainObject('GenericDomainObject' , 152300));
        $this->assertNull($this->_identityMap->getDomainObject('VM_Melding' , 54321));
    }

    function testRemoveDomainObjects()
    {
        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->assertTrue ( $this->_identityMap->removeDomainObject ('GenericDomainObject' , 54321) , 'Object kon niet verwijderd worden!' );
        $this->assertNull ( $this->_identityMap->getDomainObject('GenericDomainObject' , 54321) , 'Object zit nog steeds in IdentityMap alhoewel het verwijderd werd!');
        $this->assertFalse ( $this->_identityMap->removeDomainObject ( 'GenericDomainObject' , 152300 ) );
        $this->assertFalse ( $this->_identityMap->removeDomainObject ( 'VM_Melding' , 54321 ) );
    }

    function testMultipleDomainObjects()
    {
        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->_identityMap->addDomainObject ( $this->_domObject2 );
        
        $domObject3 = new MockGenericDomainObject( $this );
        $domObject3->setReturnValue('getId', '123456789');
        $domObject3->setReturnValue('getClass', 'GenericDomainObject');
        $domObject3->expectAtLeastOnce('getId');
        $this->_identityMap->addDomainObject ( $domObject3 );
        
        $domObjects = $this->_identityMap->getDomainObjects ( 'GenericDomainObject' );
        $this->assertNotNull($domObjects);
        foreach ($domObjects as $domObject) {
            $this->assertNotNull($domObject->getId());
        }
        $this->assertReference ( $domObjects[54321] , $this->_domObject);
        $this->assertReference ( $domObjects[9876] , $this->_domObject2);
        $this->assertReference ( $domObjects[123456789] , $domObject3);
    }

    function testIterator()
    {

        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->_identityMap->addDomainObject ( $this->_domObject2 );

        $domObjectBis = new MockGenericDomainObject( $this );
        $domObjectBis->setReturnValue('getId', '123456789');
        $domObjectBis->setReturnValue('getClass', 'GenericDomainObjectBis');
        $domObjectBis->expectAtLeastOnce('getId');
        $this->_identityMap->addDomainObject ( $domObjectBis );

        foreach ($this->_identityMap as $map) {
            $this->assertNotNull($map);
        }
        $this->assertFalse ($this->_identityMap->valid());
        $this->_identityMap->rewind();
        $this->assertEqual ($this->_identityMap->current() , array ( 54321 => $this->_domObject , 9876 => $this->_domObject2) );
        $this->assertEqual ($this->_identityMap->next() , array ( 123456789 => $domObjectBis ) );
        $this->assertEqual ($this->_identityMap->current() , array ( 123456789 => $domObjectBis ) );
        $this->assertEqual ($this->_identityMap->key() , 'GenericDomainObjectBis');
        $this->assertFalse ($this->_identityMap->next());
    }

}
?>
