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

    public function testIsNull( )
    {
        $this->assertFalse( $this->object->isNull( ) );
        $niets = KVDthes_TestTerm::newNull( );
        $this->assertTrue( $niets->isNull( ) );
    }

    public function testGetTerm() {
        $this->assertEquals( 'kapellen', $this->object->getTerm( ) );
    }

    public function testSetTerm( )
    {
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setTerm( 'klootschieten' );
        $this->assertEquals( 'klootschieten', $this->object->getTerm( ) );
    }

    public function testGetQualifier() {
        $this->assertEquals( 'klein erfgoed', $this->object->getQualifier( ) );
    }

    public function testSetQualifier( )
    {
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setQualifier( 'groot erfgoed' );
        $this->assertEquals( 'groot erfgoed', $this->object->getQualifier( ) );
    }

    public function testGetQualifiedTerm( ) {
        $this->assertEquals( 'kapellen (klein erfgoed)', $this->object->getQualifiedTerm( ) );
    }

    public function testGetId() {
        $this->assertEquals( 507 , $this->object->getId( ) );
    }

    public function testSetType( )
    {
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setType( new KVDthes_TermType( 'ND', 'Non-Descriptor' ) );
        $this->assertEquals( 'ND', $this->object->getType()->getId( ) );
    }

    public function testGetLanguage() {
        $this->assertEquals( 'nl-BE', $this->object->getLanguage( ) );
    }

    public function testSetLanguage( )
    {
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setLanguage( 'en-US' );
        $this->assertEquals( 'en-US', $this->object->getLanguage( ) );
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

    public function testSetSortKey( )
    {
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setSortKey( 'aaa' );
        $this->assertEquals( 'aaa', $this->object->getSortKey( ) );
    }

    public function testHasBTRelations(  )
    {
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->assertFalse( $this->object->hasBTRelations( ) );
        $this->assertEquals( $this->object->hasBTRelations( ), $this->object->hasBT( ) );
    }

    public function testPreferredTerm( )
    {
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $term2->setLoadState( KVDthes_Term::LS_REL );
        $term2->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setType( new KVDthes_TermType( 'ND', 'Non Descriptor' ) );
        $this->object->setPreferredTerm( $term2  );
        $this->assertTrue( $term2->isPreferredTerm( ) );
        $this->assertEquals( $term2, $this->object->getPreferredTerm( ) );
        $this->assertEquals( $term2, $term2->getPreferredTerm( ) );
    }


    public function testAddRemoveRelation( )
    {
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $term2->setLoadState( KVDthes_Term::LS_REL );
        $term2->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setType( new KVDthes_TermType( 'ND', 'Non Descriptor' ) );
        $this->object->addRelation( new KVDthes_Relation( KVDthes_Relation::REL_USE, $term2 ) );
        $this->assertEquals( $term2, $this->object->getPreferredTerm( ) );
        $term2->removeRelation( new KVDthes_Relation( KVDthes_Relation::REL_UF, $this->object ) );
        $this->assertEquals( $term2, $term2->getPreferredTerm( ) );
    }

    public function testClearRelation( )
    {
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $term2->setLoadState( KVDthes_Term::LS_REL );
        $term2->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setType( new KVDthes_TermType( 'ND', 'Non Descriptor' ) );
        $this->object->addRelation( new KVDthes_Relation( KVDthes_Relation::REL_USE, $term2 ) );
        $this->assertEquals( 1, count( $this->object->getRelations( ) ) );
        $this->object->clearRelations( );
        $this->assertEquals( 0, count( $this->object->getRelations( ) ) );
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

    public function testNotes( )
    {
        $this->object->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->setLoadState( KVDthes_Term::LS_REL );
        $this->object->setScopeNote( 'SN' );
        $this->object->setHistoryNote( 'HN' );
        $this->object->setIndexingNote( 'IN' );
        $this->object->setSourceNote( 'SoN' );
        $this->assertEquals( 'SN', $this->object->getScopeNote( ) );
        $this->assertEquals( 'HN', $this->object->getHistoryNote( ) );
        $this->assertEquals( 'IN', $this->object->getIndexingNote( ) );
        $this->assertEquals( 'SoN', $this->object->getSourceNote( ) );
    }
}
?>
