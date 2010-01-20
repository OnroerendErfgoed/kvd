<?php
class KVDthes_RelationsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_Relations
     * @access protected
     */
    protected $object;

    protected $sessie;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $this->sessie = $this->getMock( 'KVDthes_Sessie' );
        $this->term = new KVDthes_TestTerm( 0, $this->sessie, 'TestTerm');
        $this->relation = new KVDthes_Relation( KVDthes_Relation::REL_RT , $this->term );
        $this->object = new KVDthes_Relations;
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

    /**
     * @todo Implement testAddRelation().
     */
    public function testAddRelation() {
        $this->assertEquals( $this->object->count( ), 0);
        $this->object->addRelation( $this->relation );
        $this->assertEquals( $this->object->count( ), 1);
    }

    public function testGetIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertType( 'KVDthes_RelationsIterator', $this->object->getIterator( ) );
        $this->assertEquals( count( $this->object->getIterator( ) ), 1);
    }

    /**
     * @todo Implement testGetNTIterator().
     */
    public function testGetNTIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertType( 'KVDthes_RelationTypeIterator', $this->object->getNTIterator( ) );
        $this->assertEquals( count( $this->object->getNTIterator( ) ), 0);
    }

    public function testGetBTIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertType( 'KVDthes_RelationTypeIterator', $this->object->getBTIterator( ) );
        $this->assertEquals( count( $this->object->getBTIterator( ) ), 0);
    }

    public function testGetUSEIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertType( 'KVDthes_RelationTypeIterator', $this->object->getUSEIterator( ) );
        $this->assertEquals( count( $this->object->getUSEIterator( ) ), 0);
    }

    public function testGetUFIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertType( 'KVDthes_RelationTypeIterator', $this->object->getUFIterator( ) );
        $this->assertEquals( count( $this->object->getUFIterator( ) ), 0);
    }

    public function testGetRTIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertType( 'KVDthes_RelationTypeIterator', $this->object->getRTIterator( ) );
        $this->assertEquals( count( $this->object->getRTIterator( ) ), 1);
    }

    public function testCount() {
        $this->assertEquals( $this->object->count( ), 0);
        $this->assertEquals( count( $this->object ), 0);
        $this->object->addRelation( $this->relation );
        $this->assertEquals( $this->object->count( ), 1);
        $this->assertEquals( count( $this->object ), 1);
    }

    public function testCountWithType() {
        $this->assertEquals( $this->object->count(KVDthes_Relation::REL_RT ), 0);
        $this->object->addRelation( $this->relation );
        $this->assertEquals( $this->object->count(KVDthes_Relation::REL_RT ), 1);
        $this->assertEquals( $this->object->count(KVDthes_Relation::REL_NT ), 0);
    }
}
?>
