<?php
class KVDthes_TermTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_Term
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
        $this->sessie = $this->getMock( 'KVDthes_Sessie' );
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $this->object = new KVDthes_TestTerm( 507, $this->sessie, 'kapellen', $termType, 'klein erfgoed');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->sessie = null;
        $this->object = null;
    }

    public function testLoadState() {
        $this->assertTrue( $this->object->isLoadState( KVDthes_Term::LS_TERM ) );
        $this->assertFalse( $this->object->isLoadState( KVDthes_Term::LS_REL ) );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->assertTrue( $this->object->isLoadState( KVDthes_Term::LS_REL ) );
    }

    public function testGetTerm() {
        $this->assertEquals( 'kapellen', $this->object->getTerm( ) );
    }

    public function testGetQualifier() {
        $this->assertEquals( 'klein erfgoed', $this->object->getQualifier( ) );
    }

    public function testGetQualifiedTerm( ) {
        $this->assertEquals( 'kapellen (klein erfgoed)', $this->object->getQualifiedTerm( ) );
    }

    public function testGetId() {
        $this->assertEquals( 507 , $this->object->getId( ) );
    }

    public function testGetLanguage() {
        $this->assertEquals( 'nl-BE', $this->object->getLanguage( ) );
    }

    public function testGetClass() {
        $this->assertEquals( 'KVDthes_TestTerm', $this->object->getClass( ) );
    }

    public function testGetOmschrijving() {
        $this->assertEquals( 'kapellen (klein erfgoed)', $this->object->getOmschrijving( ) );
    }

    public function test__toString() {
        $this->assertEquals( 'kapellen (klein erfgoed)', $this->object->__toString( ) );
    }

    public function testLoadRelation( )
    {
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->loadRelation( new KVDthes_Relation(KVDthes_Relation::REL_RT, $term2 ));
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $term2->setLoadState( KVDthes_Term::LS_REL );
        $this->assertEquals( 1, count( $this->object->getRelations( ) ) );
        $this->assertEquals( 1, count( $term2->getRelations( ) ) );
    }
}
?>
