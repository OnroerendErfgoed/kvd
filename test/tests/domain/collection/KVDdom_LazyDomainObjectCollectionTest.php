<?php
/**
 * @package    KVD.dom
 * @subpackage collection
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_LazyDomainObjectCollectionTest 
 * 
 * @package    KVD.dom
 * @subpakcage collection
 * @since      1.5
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_LazyDomainObjectCollectionTest extends PHPUnit_Framework_TestCase
{
    
    private $coll;

    private $domObject;

    private $domObject2;

    private $domObject3;

    private $testArray;

    function setUp( )
    {
        $this->domObject = new KVDdom_SimpleTestDomainObject( 54321, 'Object 54321' );
        $this->domObject2 = new KVDdom_SimpleTestDomainObject( 9876, 'Object 9876' );
        $this->domObject3 = new KVDdom_SimpleTestDomainObject( 123456789, 'Object 123456789' );
        $this->testArray = array (    54321       => $this->domObject,
                                 9876        => $this->domObject2,
                                 123456789   => $this->domObject3 );
        $this->chunky = new KVDdom_Chunky_MockQuery( new KVDdom_DomainObjectCollection( $this->testArray ) );
        $this->coll = new KVDdom_LazyDomainObjectCollection( $this->chunky );
    }

    function tearDown( )
    {
        $this->coll = null;
        $this->domObject = null;
        $this->domObject2 = null;
        $this->domObject3 = null;
        $this->testArray = null;
    }

    public function testCount(  )
    {
        $this->assertEquals( 3, count( $this->coll ) );
    }

    public function testSeek(  )
    {
        $this->assertSame( $this->domObject2, $this->coll->seek( 1 ) );
    }

    public function testHasDomainObject( )
    {
        $this->assertTrue( $this->coll->hasDomainObject( $this->domObject2 ) );
        $temp = new KVDdom_SimpleTestDomainObject( 'KOEN', 'Koen Van Daele' );
        $this->assertFalse( $this->coll->hasDomainObject( $temp ) );
    }

    public function testGetDomainObjectWithId(  )
    {
        $this->assertSame( $this->domObject3, $this->coll->getDomainObjectWithId( 123456789 ) );
        $this->assertNull( $this->coll->getDomainObjectWithId( 'KOEN' ) );
    }

    public function testSetRowsPerChunk( )
    {
        $this->coll->setRowsPerChunk( 1 );
        $this->assertSame( $this->domObject3, $this->coll->getDomainObjectWithId( 123456789 ) );
    }

    public function testNull(  )
    {
        $this->assertFalse( $this->coll->isNull( ) );
        $nullcoll = KVDdom_LazyDomainObjectCollection::newNull( );
        $nullcoll->setRowsPerChunk( 3456 );
        $this->assertTrue( $nullcoll->isNull( ) );
        $this->assertEquals( 0, count( $nullcoll ) );
        $this->assertEquals( 0,$nullcoll->getTotalRecordCount( ) );
    }

    public function testRetainCurrent( )
    {
        $this->coll->setRowsPerChunk( 1 );
        $this->coll->setRetentionStrategy( KVDdom_LazyDomainObjectCollection::RETAIN_CURRENT );
        $this->assertSame( $this->domObject3, $this->coll->getDomainObjectWithId( 123456789 ) );
        $this->assertSame( $this->domObject, $this->coll->getDomainObjectWithId( 54321 ) );
        $this->assertSame( $this->domObject2, $this->coll->getDomainObjectWithId( 9876 ) );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidRetentionStrategy(  )
    {
        $this->coll->setRetentionStrategy( 345 );
    }

    public function testCurrectGivesFalseForUnexistingIndex(  )
    {
        $this->coll->seek( 2 );
        $this->coll->next( );
        $this->assertFalse( $this->coll->current(  ) );
    }
}
?>
