<?php
class KVDthes_RelationsIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_RelationsIterator
     * @access protected
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $testArray = array( );
        for ( $i = 0; $i < 10; $i++ ) {
            $testArray[] = $this->getTestRelation( $i );
        }
        $this->object = new KVDthes_RelationsIterator( $testArray );
    }

    private function getTestRelation( $id = null )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $term = new KVDthes_TestTerm( is_null( $id ) ? 0 : $id , $sessie, 'TestTerm');
        return new KVDthes_Relation( KVDthes_Relation::REL_RT , $term );
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

    public function testNext() {
        $curr = $this->object->current( );
        $this->object->next( );
        $next = $this->object->current( );
        $this->assertNotEquals( $curr, $next );
    }

    public function testCurrent() {
        $curr = $this->object->current( );
        $this->assertNotNull( $curr );
        $this->assertType( 'KVDthes_Relation', $curr );
        $this->assertEquals( '0', $curr->getTerm( )->getId( ) );
    }

    public function testRewind() {
        $this->object->next( );
        $this->object->next( );
        $this->assertEquals( 2, $this->object->current( )->getTerm( )->getId( ) );
        $this->object->rewind( );
        $this->assertEquals( 0, $this->object->current( )->getTerm( )->getId( ) );
        $this->object->next( );
        $this->assertEquals( 1, $this->object->current( )->getTerm( )->getId( ) );
    }

    public function testKey() {
        $this->assertEquals( 0, $this->object->key( ) );
        $this->object->next( );
        $this->assertEquals( 1, $this->object->key( ) );
        $this->object->next( );
        $this->assertEquals( 2, $this->object->key( ) );
    }

    public function testValid() {
        $this->assertEquals( true, $this->object->valid( ) );
        $this->object->next( );
        $this->assertEquals( true, $this->object->valid( ) );
        while ( $this->object->valid( ) ) {
            $this->object->next( );
        }
        $this->assertEquals( 10, $this->object->key( ) );
    }

    public function testCount() {
        $this->assertEquals( 10, $this->object->count( ) );
        $this->assertEquals( 10, count( $this->object ) );
    }
}
?>
