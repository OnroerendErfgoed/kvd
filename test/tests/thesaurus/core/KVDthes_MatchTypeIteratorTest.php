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
 * KVDthes_MatchTypeIteratorTest 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_MatchTypeIteratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    array
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
        $testArray[] = $this->getTestMatch( 0, KVDthes_Match::MATCH_BM );
        $testArray[] = $this->getTestMatch( 1, KVDthes_Match::MATCH_NM );
        $testArray[] = $this->getTestMatch( 2, KVDthes_Match::MATCH_NM );
        $testArray[] = $this->getTestMatch( 3, KVDthes_Match::MATCH_NM );
        $testArray[] = $this->getTestMatch( 4, KVDthes_Match::MATCH_RM );
        $testArray[] = $this->getTestMatch( 5, KVDthes_Match::MATCH_EM );
        $testArray[] = $this->getTestMatch( 6, KVDthes_Match::MATCH_RM );
        $testArray[] = $this->getTestMatch( 7, KVDthes_Match::MATCH_NM );
        $testArray[] = $this->getTestMatch( 8, KVDthes_Match::MATCH_RM );

        $this->objects[KVDthes_Match::MATCH_BM] = new KVDthes_MatchTypeIterator($testArray, KVDthes_Match::MATCH_BM);
        $this->objects[KVDthes_Match::MATCH_NM] = new KVDthes_MatchTypeIterator($testArray, KVDthes_Match::MATCH_NM);
        $this->objects[KVDthes_Match::MATCH_RM] = new KVDthes_MatchTypeIterator($testArray, KVDthes_Match::MATCH_RM);
        $this->objects[KVDthes_Match::MATCH_EM] = new KVDthes_MatchTypeIterator($testArray, KVDthes_Match::MATCH_EM);
        $this->objects[KVDthes_Match::MATCH_CM] = new KVDthes_MatchTypeIterator($testArray, KVDthes_Match::MATCH_CM);

    }

    private function getTestMatch($id = null, $type = KVDthes_Match::MATCH_RM )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $match = new KVDthes_TestTerm( is_null( $id ) ? 0 : $id, $sessie , 'TestTerm');
        return new KVDthes_Match($type , $match);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->objects = array();
    }

    public function testRewind() {
        $object = $this->objects[KVDthes_Match::MATCH_NM];
        $object->next( );
        $object->next( );
        $this->assertNotEquals(0, $object->key( ));
        $object->rewind( );
        $this->assertEquals(1, $object->key( ));
    }

    public function testNextRM() {
        $object = $this->objects[KVDthes_Match::MATCH_RM];
        $this->assertEquals(4, $object->key( ) );
        $object->next( );
        $this->assertEquals(6, $object->key( ));
        $object->next( );
        $this->assertEquals(8, $object->key( ));
        $object->next( );
        $this->assertFalse($object->valid( ));
    }

    public function testNextEM() {
        $object = $this->objects[KVDthes_Match::MATCH_EM];
        $this->assertEquals(5, $object->key( ) );
        $object->next( );
        $this->assertFalse($object->valid( ));
        $object->next( );
        $this->assertFalse($object->valid( ));
    }

    public function testCount() {
        $this->assertEquals(1, $this->objects[KVDthes_Match::MATCH_BM]->count() );
        $this->assertEquals(4, $this->objects[KVDthes_Match::MATCH_NM]->count() );
        $this->assertEquals(3, $this->objects[KVDthes_Match::MATCH_RM]->count() );
        $this->assertEquals(1, $this->objects[KVDthes_Match::MATCH_EM]->count() );
        $this->assertEquals(0, $this->objects[KVDthes_Match::MATCH_CM]->count() );
    }
}
?>
