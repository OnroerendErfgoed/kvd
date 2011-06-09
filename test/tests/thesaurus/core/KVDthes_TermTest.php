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
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $this->object = new KVDthes_TestTerm( 507, $sessie, 'kapellen', $termType, 'klein erfgoed');
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
}
?>
