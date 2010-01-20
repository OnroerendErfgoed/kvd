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
        $this->object = new KVDthes_TestTerm( $sessie, 0, 'TestTerm', 'Uniek', 'Nederlands' );
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
     * @todo Implement testIsLoadState().
     */
    public function testIsLoadState() {
        $this->assertTrue( $this->object->isLoadState( KVDthes_Term::LS_TERM ) );
        $this->assertFalse( $this->object->isLoadState( KVDthes_Term::LS_REL ) );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->assertTrue( $this->object->isLoadState( KVDthes_Term::LS_REL ) );
    }

    /**
     * @todo Implement testCheckRelations().
     */
    public function testCheckRelations() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCheckScopeNote().
     */
    public function testCheckScopeNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testCheckSourceNote().
     */
    public function testCheckSourceNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testSetLoadState().
     */
    public function testSetLoadState() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    public function testGetTerm() {
        $this->assertEquals( 'TestTerm', $this->object->getTerm( ) );
    }

    public function testGetQualifier() {
        $this->assertEquals( 'Uniek', $this->object->getQualifier( ) );
    }

    public function testGetQualifiedTerm( ) {
        $this->assertEquals( 'TestTerm (Uniek)', $this->object->getQualifiedTerm( ) );
    }

    public function testGetId() {
        $this->assertEquals( 0 , $this->object->getId( ) );
    }

    /**
     * @todo Implement testGetScopeNote().
     */
    public function testGetScopeNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetSourceNote().
     */
    public function testGetSourceNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAddRelation().
     */
    public function testAddRelation() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAddScopeNote().
     */
    public function testAddScopeNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAddSourceNote().
     */
    public function testAddSourceNote() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAccept().
     */
    public function testAccept() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testAcceptSimple().
     */
    public function testAcceptSimple() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testIsPreferredTerm().
     */
    public function testIsPreferredTerm() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetPreferredTerm().
     */
    public function testGetPreferredTerm() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetNonPreferredTerms().
     */
    public function testGetNonPreferredTerms() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetRelatedTerms().
     */
    public function testGetRelatedTerms() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetLanguage().
     */
    public function testGetLanguage() {
        $this->assertEquals( 'Nederlands', $this->object->getLanguage( ) );
    }

    /**
     * @todo Implement testHasNTRelations().
     */
    public function testHasNTRelations() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testHasBTRelations().
     */
    public function testHasBTRelations() {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @todo Implement testGetClass().
     */
    public function testGetClass() {
        $this->assertEquals( 'KVDthes_TestTerm', $this->object->getClass( ) );
    }

    /**
     * @todo Implement testGetOmschrijving().
     */
    public function testGetOmschrijving() {
        $this->assertEquals( 'TestTerm (Uniek)', $this->object->getOmschrijving( ) );
    }

    /**
     * @todo Implement test__toString().
     */
    public function test__toString() {
        $this->assertEquals( 'TestTerm (Uniek)', $this->object->__toString( ) );
    }
}
?>
