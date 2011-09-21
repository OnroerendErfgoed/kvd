<?php
/**
 * @package     KVD.thes
 * @subpacke    core
 * @version     $Id$
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_RelationsTest 
 * 
 * @package     KVD.thes
 * @subpacke    core
 * @since       2009
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
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
        $this->zone = new KVDthes_TestTerm( 0, $this->sessie, 'Zone');
        $this->gebied = new KVDthes_TestTerm( 1, $this->sessie, 'Gebied');
        $this->locatie = new KVDthes_TestTerm( 2, $this->sessie, 'Locatie');
        $this->relation = new KVDthes_Relation( KVDthes_Relation::REL_RT , $this->zone );
        $this->object = new KVDthes_Relations( );
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
        $res = $this->object->addRelation( $this->relation );
        $this->assertTrue( $res );
        $this->assertEquals( $this->object->count( ), 1);
        $res = $this->object->addRelation( $this->relation );
        $this->assertFalse( $res );
        $this->assertEquals( $this->object->count( ), 1);
    }

    public function testRemoveRelation()
    {
        $this->assertEquals( $this->object->count( ), 0);
        $this->object->addRelation( $this->relation );
        $this->assertEquals( $this->object->count( ), 1);
        $res = $this->object->removeRelation( $this->relation );
        $this->assertTrue( $res );
        $this->assertEquals( $this->object->count( ), 0);
        $res = $this->object->removeRelation( $this->relation );
        $this->assertFalse( $res );
        $this->assertEquals( $this->object->count( ), 0);
    }

    public function testGetIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertInstanceOf( 'KVDthes_RelationsIterator', $this->object->getIterator( ) );
        $this->assertEquals( count( $this->object->getIterator( ) ), 1);
    }

    public function testGetIteratorByType()
    {
        $this->object->addRelation( $this->relation );
        $it = $this->object->getIterator( KVDthes_Relation::REL_RT );
        $this->assertInstanceOf( 'KVDthes_RelationTypeIterator', $it);
        $this->assertEquals( 1, count( $it) );
        $this->assertEquals( $it, $this->object->getRTIterator(  ) );
    }

    /**
     * @todo Implement testGetNTIterator().
     */
    public function testGetNTIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertInstanceOf( 'KVDthes_RelationTypeIterator', $this->object->getNTIterator( ) );
        $this->assertEquals( count( $this->object->getNTIterator( ) ), 0);
    }

    public function testGetBTIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertInstanceOf( 'KVDthes_RelationTypeIterator', $this->object->getBTIterator( ) );
        $this->assertEquals( count( $this->object->getBTIterator( ) ), 0);
    }

    public function testGetUSEIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertInstanceOf( 'KVDthes_RelationTypeIterator', $this->object->getUSEIterator( ) );
        $this->assertEquals( count( $this->object->getUSEIterator( ) ), 0);
    }

    public function testGetUFIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertInstanceOf( 'KVDthes_RelationTypeIterator', $this->object->getUFIterator( ) );
        $this->assertEquals( count( $this->object->getUFIterator( ) ), 0);
    }

    public function testGetRTIterator() {
        $this->object->addRelation( $this->relation );
        $this->assertInstanceOf( 'KVDthes_RelationTypeIterator', $this->object->getRTIterator( ) );
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

    public function testSort( )
    {
        $rel2 = new KVDthes_Relation( KVDthes_Relation::REL_RT , $this->gebied );
        $rel3 = new KVDthes_Relation( KVDthes_Relation::REL_RT , $this->locatie );
        $this->object->addRelation( $this->relation );
        $this->object->addRelation( $rel2 );
        $this->object->addRelation( $rel3 );
        $this->object->sort( KVDthes_TermSorter::SORT_TERM );
        $coll = new KVDdom_DomainObjectCollection( array( $rel2, $rel3, $this->relation ) );
        $this->assertEquals( $coll, $this->object->getImmutableCollection( ) );


        $this->object->sort( KVDthes_TermSorter::SORT_SORTKEY );
        $this->assertEquals( $coll, $this->object->getImmutableCollection( ) );

        $this->object->sort( KVDthes_TermSorter::SORT_QUALTERM );
        $this->assertEquals( $coll, $this->object->getImmutableCollection( ) );

        $this->object->sort( KVDthes_TermSorter::SORT_ID );
        $coll = new KVDdom_DomainObjectCollection( array( $this->relation, $rel2, $rel3 ) );
        $this->assertEquals( $coll, $this->object->getImmutableCollection( ) );
    }
}
?>
