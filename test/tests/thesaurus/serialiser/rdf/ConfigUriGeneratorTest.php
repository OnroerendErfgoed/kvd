<?php
/**
 * @package    KVD.thes
 * @subpackage serialiser
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Test voor de KVDthes_Serialiser_Rdf_ConfigUriGenerator
 * 
 * @package    KVD.thes
 * @subpackage serialiser
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_Serialiser_Rdf_ConfigUriGeneratorTest extends PHPUnit_Framework_TestCase
{
    protected $gen;

    public function setUp( )
    {
        $config = array( 'uri_templates' => 
                    array( '1' => 
                        array( 'term'      => 'http://id.vioe.be/thesaurus/test/%d',
                               'thesaurus' => 'http://id.vioe.be/thesaurus/test/' ) ) );
        $this->gen = new KVDthes_Serialiser_Rdf_ConfigUriGenerator( $config );
    }

    public function tearDown( )
    {
        $this->gen = null;
    }

    public function testGenerateTermUri( )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $thes = new KVDthes_Thesaurus( $sessie, 1, 'Test' );
        $term = new KVDthes_TestTerm( 507, 
                                      $sessie, 
                                      'kapellen', 
                                      $termType, 
                                      'klein erfgoed',
                                      'nl-BE',
                                      'kape',
                                      array( ),
                                      $thes);

        $uri = $this->gen->generateTermUri( $term );

        $this->assertEquals( 'http://id.vioe.be/thesaurus/test/507',
                             $uri );
    }

    public function testGenerateThesaurusUri( )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $thes = new KVDthes_Thesaurus( $sessie, 1, 'Test' );

        $uri = $this->gen->generateThesaurusUri( $thes );

        $this->assertEquals( 'http://id.vioe.be/thesaurus/test/',
                             $uri );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGenerateThermUriForUnknownThesaurus( )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $termType = new KVDthes_TermType( 'PT', 'voorkeursterm' );
        $thes = new KVDthes_Thesaurus( $sessie, 2, 'Verkest' );
        $term = new KVDthes_TestTerm( 507, 
                                      $sessie, 
                                      'kapellen', 
                                      $termType, 
                                      'klein erfgoed',
                                      'nl-BE',
                                      'kape',
                                      array( ),
                                      $thes);

        $uri = $this->gen->generateTermUri( $term );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGenerateThesaurusUriForUnknownThesaurus( )
    {
        $sessie = $this->getMock( 'KVDthes_Sessie' );
        $thes = new KVDthes_Thesaurus( $sessie, 2, 'Verkest' );

        $uri = $this->gen->generateThesaurusUri( $thes );
    }
}
?>
