<?php
require_once 'PHPUnit/Framework.php';

class KVDthes_RelationTypeIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    array
     * @access protected
     */
    protected $objects;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $testArray = array( );
        $testArray[] = $this->getTestRelation( 0, KVDthes_Relation::REL_BT );
        $testArray[] = $this->getTestRelation( 1, KVDthes_Relation::REL_NT );
        $testArray[] = $this->getTestRelation( 2, KVDthes_Relation::REL_NT );
        $testArray[] = $this->getTestRelation( 3, KVDthes_Relation::REL_NT );
        $testArray[] = $this->getTestRelation( 4, KVDthes_Relation::REL_RT );
        $testArray[] = $this->getTestRelation( 5, KVDthes_Relation::REL_UF );
        $testArray[] = $this->getTestRelation( 6, KVDthes_Relation::REL_RT );
        $testArray[] = $this->getTestRelation( 7, KVDthes_Relation::REL_NT );
        $testArray[] = $this->getTestRelation( 8, KVDthes_Relation::REL_RT );

        $this->objects[KVDthes_Relation::REL_BT] = new KVDthes_RelationTypeIterator( $testArray , KVDthes_Relation::REL_BT );
        $this->objects[KVDthes_Relation::REL_NT] = new KVDthes_RelationTypeIterator( $testArray , KVDthes_Relation::REL_NT );
        $this->objects[KVDthes_Relation::REL_RT] = new KVDthes_RelationTypeIterator( $testArray , KVDthes_Relation::REL_RT );
        $this->objects[KVDthes_Relation::REL_UF] = new KVDthes_RelationTypeIterator( $testArray , KVDthes_Relation::REL_UF );
        $this->objects[KVDthes_Relation::REL_USE] = new KVDthes_RelationTypeIterator( $testArray , KVDthes_Relation::REL_USE );

    }

    private function getTestRelation( $id = null , $type = KVDthes_Relation::REL_RT )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $term = new KVDthes_TestTerm( is_null( $id ) ? 0 : $id, $sessie , 'TestTerm');
        return new KVDthes_Relation( $type , $term );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    public function testRewind() {
        $object = $this->objects[KVDthes_Relation::REL_NT];
        $object->next( );
        $object->next( );
        $this->assertNotEquals(0, $object->key( ));
        $object->rewind( );
        $this->assertEquals(1, $object->key( ));
    }

    public function testNextRT() {
        $object = $this->objects[KVDthes_Relation::REL_RT];
        $this->assertEquals(4, $object->key( ) );
        $object->next( );
        $this->assertEquals(6, $object->key( ));
        $object->next( );
        $this->assertEquals(8, $object->key( ));
        $object->next( );
        $this->assertFalse($object->valid( ));
    }

    public function testNextUF() {
        $object = $this->objects[KVDthes_Relation::REL_UF];
        $this->assertEquals(5, $object->key( ) );
        $object->next( );
        $this->assertFalse($object->valid( ));
        $object->next( );
        $this->assertFalse($object->valid( ));
    }

    public function testCount() {
        $this->assertEquals( 1, $this->objects[KVDthes_Relation::REL_BT]->count( ) );
        $this->assertEquals( 4, $this->objects[KVDthes_Relation::REL_NT]->count( ) );
        $this->assertEquals( 3, $this->objects[KVDthes_Relation::REL_RT]->count( ) );
        $this->assertEquals( 1, $this->objects[KVDthes_Relation::REL_UF]->count( ) );
        $this->assertEquals( 0, $this->objects[KVDthes_Relation::REL_USE]->count( ) );
    }
}
?>
