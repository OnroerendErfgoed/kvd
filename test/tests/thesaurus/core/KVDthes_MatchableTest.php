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
 * KVDthes_MatchableTest 
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      1.6
 * @copyright  2012 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_MatchableTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->sessie = $this->getMock( 'KVDthes_Sessie' );
        $this->object = $this->getMockForAbstractClass('KVDthes_Matchable',
                                                       array(1, $this->sessie));
    }

    public function testLoadState() 
    {
        $this->assertFalse( $this->object->isLoadState( KVDthes_Matchable::LS_MATCH ) );
        $this->object->setLoadState( KVDthes_Matchable::LS_MATCH );
        $this->assertTrue( $this->object->isLoadState( KVDthes_Matchable::LS_MATCH ) );
    }

    public function testCheckMatches( )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $mapper = $this->getMockForAbstractClass(
            'KVDthes_DbMapper',
            array(),
            'KVDthes_TestMapper',
            false, false, true,
            array( 'loadMatches' )
        );
        $mapper->expects($this->once())
               ->method('loadMatches');
        $sessie->expects($this->once())
               ->method('getMapper')
               ->with('KVDthes_TestTerm')
               ->will($this->returnValue($mapper));
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term = new KVDthes_TestTerm( 508, $sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $matches = $term->getMatches();
        $this->assertTrue($term->isLoadState(KVDthes_Matchable::LS_MATCH));
    }

    public function testAddRemoveMatch( )
    {
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->setLoadState( KVDthes_Matchable::LS_MATCH );
        $term2->setLoadState( KVDthes_Term::LS_REL );
        $term2->setLoadState( KVDthes_Term::LS_MATCH );
        $term2->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->addMatch( new KVDthes_Match( KVDthes_Match::MATCH_RM, $term2 ) );
        $this->assertEquals(1, count($this->object->getMatches()));
        $term2->removeMatch( new KVDthes_Match( KVDthes_Match::MATCH_RM, $this->object ) );
        $this->assertEquals(0, count($this->object->getMatches()));
    }

    public function testClearMatch( )
    {

        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->setLoadState( KVDthes_Matchable::LS_MATCH );
        $term2->setLoadState( KVDthes_Term::LS_REL );
        $term2->setLoadState( KVDthes_Term::LS_MATCH );
        $term2->setLoadState( KVDthes_Term::LS_NOTES );
        $this->object->addMatch( new KVDthes_Match( KVDthes_Match::MATCH_RM, $term2 ) );
        $this->assertEquals(1, count($this->object->getMatches()));
        $this->object->clearMatches();
        $this->assertEquals(0, count($this->object->getMatches()));

    }

    public function testLoadMatch( )
    {
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->loadMatch( new KVDthes_Match(KVDthes_Match::MATCH_RM, $term2 ));
        $this->object->setLoadState( KVDthes_Matchable::LS_MATCH );
        $term2->setLoadState( KVDthes_Matchable::LS_MATCH );
        $this->assertEquals( 1, count( $this->object->getMatches( ) ) );
        $this->assertEquals( 1, count( $term2->getMatches( ) ) );
    }

    public function testHasMatches( )
    {
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $term2 = new KVDthes_TestTerm( 508, $this->sessie, 'kapellen', $termType, 'bouwkundig erfgoed' );
        $this->object->loadMatch( new KVDthes_Match(KVDthes_Match::MATCH_RM, $term2 ));
        $this->object->setLoadState( KVDthes_Matchable::LS_MATCH );
        $term2->setLoadState( KVDthes_Matchable::LS_MATCH );
        $this->assertTrue($this->object->hasMatches());
        $this->assertTrue($this->object->hasMatches(KVDthes_Match::MATCH_RM));
        $this->assertFalse($this->object->hasMatches(KVDthes_Match::MATCH_EM));
    }
}
