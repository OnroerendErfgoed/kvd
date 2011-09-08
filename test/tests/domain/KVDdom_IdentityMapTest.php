<?php

class KVDdom_GenericIdentityMapTest extends PHPUnit_Framework_TestCase
{
    private $_identityMap;

    private $_domObject;

    private $_domObject2;

    function setUp( )
    {
        $this->_domObject = new KVDdom_SimpleTestDomainObject( 54321, 'Object 54321' );
        $this->_domObject2 = new KVDdom_SimpleTestDomainObject( 9876, 'Object 9876' );
        $this->_domObject3 = new KVDdom_SimpleTestDomainObject( 123456789, 'Object 123456789' );
        $this->_identityMap = new KVDdom_GenericIdentityMap( );
    }

    function tearDown( )
    {
        $this->_identityMap = null;
        $this->_domObject = null;
        $this->_domObject2 = null;
        $this->_domObject3 = null;
    }

    function testOneDomainObject()
    {
        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->assertNotNull ( $this->_identityMap->getDomainObject('KVDdom_SimpleTestDomainObject' , 54321) );
        $returnObj = $this->_identityMap->getDomainObject ( 'KVDdom_SimpleTestDomainObject', 54321);
        $this->assertSame ( $this->_domObject, $returnObj );
        $this->assertNull($this->_identityMap->getDomainObject('KVDdom_SimpleTestDomainObject' , 152300));
        $this->assertNull($this->_identityMap->getDomainObject('VM_Melding' , 54321));
    }

    function testRemoveDomainObjects()
    {
        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->assertTrue ( $this->_identityMap->removeDomainObject ('KVDdom_SimpleTestDomainObject' , 54321) , 'Object kon niet verwijderd worden!' );
        $this->assertNull ( $this->_identityMap->getDomainObject('KVDdom_SimpleTestDomainObject' , 54321) , 'Object zit nog steeds in IdentityMap alhoewel het verwijderd werd!');
        $this->assertFalse ( $this->_identityMap->removeDomainObject ( 'KVDdom_SimpleTestDomainObject' , 152300 ) );
        $this->assertFalse ( $this->_identityMap->removeDomainObject ( 'VM_Melding' , 54321 ) );
    }

    function testMultipleDomainObjects()
    {
        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->_identityMap->addDomainObject ( $this->_domObject2 );
        $this->_identityMap->addDomainObject ( $this->_domObject3 );
        
        $domObjects = $this->_identityMap->getDomainObjects ( 'KVDdom_SimpleTestDomainObject' );
        $this->assertNotNull($domObjects);
        foreach ($domObjects as $domObject) {
            $this->assertNotNull($domObject->getId());
        }
        $this->assertSame ( $domObjects[54321] , $this->_domObject);
        $this->assertSame ( $domObjects[9876] , $this->_domObject2);
        $this->assertSame ( $domObjects[123456789] , $this->_domObject3);

        $domObjects2 = $this->_identityMap->getDomainObjects( 'KVDdom_TestValueDomainObject');
        $this->assertNull( $domObjects2 );
    }

    function testIterator()
    {

        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->_identityMap->addDomainObject ( $this->_domObject2 );

        $domObjectBis = new KVDdom_TestValueDomainObject( 123456789, 'Object 123456789' );

        $this->_identityMap->addDomainObject ( $domObjectBis );

        foreach ($this->_identityMap as $map) {
            $this->assertNotNull($map);
        }
        $this->assertFalse ($this->_identityMap->valid());
        $this->_identityMap->rewind();
        $this->assertEquals ($this->_identityMap->current() , array ( 54321 => $this->_domObject , 9876 => $this->_domObject2) );
        $this->_identityMap->next();
        $this->assertEquals ($this->_identityMap->current() , array ( 123456789 => $domObjectBis ) );
        $this->assertEquals ($this->_identityMap->key() , 'KVDdom_TestValueDomainObject');
    }

    public function testCount( )
    {
        $this->_identityMap->addDomainObject ( $this->_domObject );
        $this->_identityMap->addDomainObject ( $this->_domObject2 );

        $this->assertEquals( 2, count( $this->_identityMap ) );

        $domObjectBis = new KVDdom_TestValueDomainObject( 123456789, 'Object 123456789' );

        $this->_identityMap->addDomainObject ( $domObjectBis );

        $this->assertEquals( 3, count( $this->_identityMap ) );

        $this->_identityMap->removeDomainObject ( 'KVDdom_TestValueDomainObject',123456789);

        $this->assertEquals( 2, count( $this->_identityMap ) );

        $this->_identityMap->removeDomainObject ( 'KVDdom_SimpleTestDomainObject',54321);
        $this->_identityMap->removeDomainObject ( 'KVDdom_SimpleTestDomainObject',9876);
        
        $this->assertEquals( 0, count( $this->_identityMap ) );
    }

}
?>
