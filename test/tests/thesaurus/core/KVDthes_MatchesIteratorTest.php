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
 * KVDthes_MatchesIteratorTest 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_MatchesIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_MatchesIterator
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
            $testArray[] = $this->getTestMatch( $i );
        }
        $this->object = new KVDthes_MatchesIterator( $testArray );
    }

    private function getTestMatch( $id = null )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $term = new KVDthes_TestTerm( is_null( $id ) ? 0 : $id , $sessie, 'TestTerm');
        return new KVDthes_Match( KVDthes_Match::MATCH_RM , $term );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->object = null;
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
        $this->assertInstanceOf( 'KVDthes_Match', $curr );
        $this->assertEquals( '0', $curr->getMatchable( )->getId( ) );
    }

    public function testRewind() {
        $this->object->next( );
        $this->object->next( );
        $this->assertEquals( 2, $this->object->current( )->getMatchable( )->getId( ) );
        $this->object->rewind( );
        $this->assertEquals( 0, $this->object->current( )->getMatchable( )->getId( ) );
        $this->object->next( );
        $this->assertEquals( 1, $this->object->current( )->getMatchable( )->getId( ) );
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
