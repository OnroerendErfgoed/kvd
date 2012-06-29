<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @version    $Id$
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDthes_MatchesTest 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_MatchesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_Matches
     * @access protected
     */
    protected $object;

    /**
     * sessie
     *
     * @var KVDthes_Sessie
     */
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
        $this->match = new KVDthes_Match( KVDthes_Match::MATCH_RM , $this->zone );
        $this->object = new KVDthes_Matches( );
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
        $this->zone = null;
        $this->gebied = null;
        $this->locatie = null;
        $this->match = null;
        $this->object = null;
    }

    public function testAddMatch() {
        $this->assertEquals( $this->object->count( ), 0);
        $res = $this->object->addMatch( $this->match );
        $this->assertTrue( $res );
        $this->assertEquals( $this->object->count( ), 1);
        $res = $this->object->addMatch( $this->match );
        $this->assertFalse( $res );
        $this->assertEquals( $this->object->count( ), 1);
    }

    public function testRemoveMatch()
    {
        $this->assertEquals( $this->object->count( ), 0);
        $this->object->addMatch( $this->match );
        $this->assertEquals( $this->object->count( ), 1);
        $res = $this->object->removeMatch( $this->match );
        $this->assertTrue( $res );
        $this->assertEquals( $this->object->count( ), 0);
        $res = $this->object->removeMatch( $this->match );
        $this->assertFalse( $res );
        $this->assertEquals( $this->object->count( ), 0);
    }

    public function testGetIterator() {
        $this->object->addMatch( $this->match );
        $this->assertInstanceOf( 'KVDthes_MatchesIterator', $this->object->getIterator( ) );
        $this->assertEquals( count( $this->object->getIterator( ) ), 1);
    }

    public function testGetIteratorByType()
    {
        $this->object->addMatch( $this->match );
        $it = $this->object->getIterator( KVDthes_Match::MATCH_RM );
        $this->assertInstanceOf( 'KVDthes_MatchTypeIterator', $it);
        $this->assertEquals( 1, count( $it) );
    }

    public function testCount() {
        $this->assertEquals( $this->object->count( ), 0);
        $this->assertEquals( count( $this->object ), 0);
        $this->object->addMatch( $this->match );
        $this->assertEquals( $this->object->count( ), 1);
        $this->assertEquals( count( $this->object ), 1);
    }

    public function testCountWithType() {
        $this->assertEquals( $this->object->count(KVDthes_Match::MATCH_RM ), 0);
        $this->object->addMatch( $this->match );
        $this->assertEquals( $this->object->count(KVDthes_Match::MATCH_RM ), 1);
        $this->assertEquals( $this->object->count(KVDthes_Match::MATCH_NM ), 0);
    }

    public function testGetImmutableCollection()
    {
        $coll = $this->object->getImmutableCollection();
        $this->assertInstanceOf( 'KVDdom_DomainObjectCollection', $coll );
        $this->assertEquals(0, count($coll) );
        $this->object->addMatch( $this->match );
        $this->assertEquals(0, count($coll) );
    }

}
?>
