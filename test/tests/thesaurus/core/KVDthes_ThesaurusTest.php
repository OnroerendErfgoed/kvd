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
 * KVDthes_ThesaurusTest 
 * 
 * @package     KVD.thes
 * @subpacke    core
 * @since       3 mrt 2010
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_ThesaurusTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var    KVDthes_Thesaurus
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
        $this->object = new KVDthes_Thesaurus( $sessie, 1, 'Datering', 'DAT', 'nl-BE');
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

    public function testGetters( )
    {
        $this->assertEquals( 'Datering', $this->object->getNaam( ) );
        $this->assertEquals( 'DAT', $this->object->getKorteNaam( ) );
        $this->assertEquals( 1 , $this->object->getId( ) );
        $this->assertEquals( 'nl-BE', $this->object->getLanguage( ) );
    }

    public function testNoKorteNaamGivesNaam()
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $this->object = new KVDthes_Thesaurus( $sessie, 1, 'Datering');
        $this->assertEquals( 'Datering', $this->object->getKorteNaam( ) );
    }

    public function testGetClass() {
        $this->assertEquals( 'KVDthes_Thesaurus', $this->object->getClass( ) );
    }

    public function testGetOmschrijving() {
        $this->assertEquals( 'Datering', $this->object->getOmschrijving( ) );
    }

    public function test__toString() {
        $this->assertEquals( 'Datering', $this->object->__toString( ) );
    }

    public function testIsNotNull( ) 
    {
        $this->assertFalse( $this->object->isNull( ) );
    }

    public function testNullThesaurus( )
    {
        $nt = KVDthes_Thesaurus::newNull( );
        $this->assertTrue( $nt->isNull( ) );
    }
}
?>
