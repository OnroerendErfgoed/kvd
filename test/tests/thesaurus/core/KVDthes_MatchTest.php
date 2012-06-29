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
 * KVDthes_MatchTest 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_MatchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_Match
     * @access protected
     */
    protected $object;

    /**
     * matchable
     * 
     * @var KVDthes_TestMatchable
     */
    protected $matchable;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $this->matchable = new KVDthes_TestTerm( 0, $sessie, 'TestTerm');
        $this->object = new KVDthes_Match( KVDthes_Match::MATCH_RM , $this->matchable );
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
        $this->matchable = null;
        $this->object = null;
    }

    /**
     * testMatchHasValidType
     * 
     * @expectedException InvalidArgumentException
     */
    public function testMatchHasValidType( )
    {
        $object = new KVDthes_Match( 'MATCH_INVALID', $this->matchable );
    }

    public function testGetType() {
        $this->assertEquals( $this->object->getType( ), KVDthes_Match::MATCH_RM );
    }

    public function testGetTerm() {

        $this->assertEquals( $this->object->getMatchable( ), $this->matchable );
    }

    public function testEquals() {
        $this->assertTrue( $this->object->equals( new KVDthes_Match( KVDthes_Match::MATCH_RM, $this->matchable ) ) );
    }

    public function testGetInverseMatch() {
        static $inverse = array ( KVDthes_Match::MATCH_BM => KVDthes_Match::MATCH_NM ,
                                  KVDthes_Match::MATCH_NM => KVDthes_Match::MATCH_BM ,
                                  KVDthes_Match::MATCH_RM => KVDthes_Match::MATCH_RM ,
                                  KVDthes_Match::MATCH_CM => KVDthes_Match::MATCH_CM ,
                                  KVDthes_Match::MATCH_EM => KVDthes_Match::MATCH_EM );
        foreach ( $inverse as $key => $value ) {
            $this->object = new KVDthes_Match($key, $this->matchable);
            $this->assertEquals( $this->object->getInverseMatch( ), $value);
        }
    }

    public function test__toString() {
        $this->assertEquals( $this->object->__toString( ), 'Match RM TestTerm' );
    }
}
?>
