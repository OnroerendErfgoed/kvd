<?php
/**
 * @package    KVD.dom
 * @subpackage Chunky
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_Chunky_MockQueryTest 
 * 
 * @package    KVD.dom
 * @subpackage Chunky
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_Chunky_MockQueryTest extends PHPUnit_Framework_TestCase
{
    private $query;

    private $domObject;

    private $domObject2;

    private $domObject3;

    private $testArray;

    private $coll;

    public function setUp( )
    {

        $this->domObject = new KVDdom_SimpleTestDomainObject( 54321, 'Object 54321' );
        $this->domObject2 = new KVDdom_SimpleTestDomainObject( 9876, 'Object 9876' );
        $this->domObject3 = new KVDdom_SimpleTestDomainObject( 123456789, 'Object 123456789' );
        $this->testArray = array ( 54321      => $this->domObject,
                                   9876       => $this->domObject2,
                                   123456789  => $this->domObject3 );
        $this->coll = new KVDdom_DomainObjectCollection( $this->testArray );
        $this->query = new KVDdom_Chunky_MockQuery( $this->coll );
    }

    public function tearDown(  )
    {
        $this->query = null;
    }

    public function testImplementsInterface( )
    {
        $this->assertInstanceOf( 'KVDdom_Chunky_IQuery', $this->query );
    }

    public function testAllInOneChunk(  )
    {
        $this->query->setChunk( 1 );
        $this->query->setRowsPerChunk( 3 );
        $this->assertEquals( 3, $this->query->getTotalRecordCount( ) );
        $this->assertEquals( 1, $this->query->getTotalChunksCount( ) );
        $dom = $this->query->getDomainObjects( );
        $this->assertEquals( 3, count( $dom ) );
        $this->assertEquals( $this->domObject, $dom[0] );
    }

    public function testTwoChunks( )
    {
        $this->query->setChunk( 1 );
        $this->assertEquals( 1, $this->query->getChunk( ) );
        $this->query->setRowsPerChunk( 2 );
        $dom = $this->query->getDomainObjects( );
        $this->assertEquals( 3, $this->query->getTotalRecordCount( ) );
        $this->assertEquals( 2, $this->query->getTotalChunksCount( ) );
        $this->assertEquals( 2, count( $dom ) );
        $this->assertEquals( $this->domObject, $dom[0] );
        $this->query->setChunk( 2 );
        $dom = $this->query->getDomainObjects( );
        $this->assertEquals( 1, count( $dom ) );
        $this->assertEquals( $this->domObject3, $dom[0] );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testIllegalChunk( )
    {
        $this->query->setRowsPerChunk( 2 );
        $this->query->setChunk( 3 );
    }
}
?>
