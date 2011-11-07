<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_DomainObjectCollectionPagerTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->domObject = new KVDdom_SimpleTestDomainObject( 54321, 'Object 54321' );
        $this->domObject2 = new KVDdom_SimpleTestDomainObject( 9876, 'Object 9876' );
        $this->domObject3 = new KVDdom_SimpleTestDomainObject( 123456789, 'Object 123456789' );
        $this->testArray = array ( 54321     => $this->domObject,
                                   9876      => $this->domObject2,
                                   123456789 => $this->domObject3 );
        $this->coll = new KVDdom_DomainObjectCollection( $this->testArray );
        $this->pager = new KVDdom_DomainObjectCollectionPager( $this->coll, 1, 2);
    }

    public function tearDown( )
    {
        $this->domObject = null;
        $this->domObject2 = null;
        $this->domObject3 = null;
        $this->testArray = null;
        $this->coll = null;
    }

    public function testGetFirstPage( )
    {
        $this->assertEquals( 1, $this->pager->getFirstPage( ) );
    }

    public function testGetLastPage( )
    {
        $this->assertEquals( 2, $this->pager->getLastPage( ) );
    }

    public function testGetNext( )
    {
        $this->assertEquals( 2, $this->pager->getNext( ) );
        $this->pager = new KVDdom_DomainObjectCollectionPager( $this->coll, 2, 2);
        $this->assertEquals( false, $this->pager->getNext( ) );
    }

    public function testGetPrev( )
    {
        $this->assertEquals( false, $this->pager->getPrev( ) );
        $this->pager = new KVDdom_DomainObjectCollectionPager( $this->coll, 2, 2);
        $this->assertEquals( 1, $this->pager->getPrev( ) );
    }

    public function testGetTotalPages()
    {
        $this->assertEquals( 2, $this->pager->getTotalPages( ) );
    }

    public function testGetPage( )
    {
        $this->assertEquals( 1, $this->pager->getPage( ) );
    }

    public function testGetRowsPerPage( )
    {
        $this->assertEquals( 2, $this->pager->getRowsPerPage( ) );
    }

    public function testGetNextLinks( )
    {
        $this->pager = new KVDdom_DomainObjectCollectionPager( $this->coll, 1, 1);
        $next = $this->pager->getNextLinks( );
        $this->assertEquals( 2, count( $next ) );
        $this->assertEquals( 2, $next[0] );
        $this->assertEquals( 3, $next[1] );
    }

    public function testGetPrevLinks( )
    {
        $this->pager = new KVDdom_DomainObjectCollectionPager( $this->coll, 3, 1);
        $prev = $this->pager->getPrevLinks( );
        $this->assertEquals( 2, count( $prev ) );
        $this->assertEquals( 1, $prev[0] );
        $this->assertEquals( 2, $prev[1] );
    }

    public function testCount( )
    {
        $this->assertEquals( 3, count( $this->pager ) );
        $this->assertEquals( 3, $this->pager->getTotalRecordCount( ) );
    }

    public function testGetResult( )
    {
        $res = $this->pager->getResult( );
        $this->assertInstanceOf( 'Iterator', $res );
        $arr = iterator_to_array( $res );
        $this->assertSame( $this->domObject, $arr[54321] );
        $this->assertSame( $this->domObject2, $arr[9876] );
    }

    public function testEmptyCollection( )
    {
        $coll = new KVDdom_DomainObjectCollection( array(  ) );
        $pager = new KVDdom_DomainObjectCollectionPager( $coll );
        $this->assertEquals( 0, count( $pager ) );
        $this->assertEquals( 1, $pager->getTotalPages( ) );
        $this->assertEquals( 1, $pager->getFirstPage( ) );
        $this->assertEquals( 1, $pager->getLastPage( ) );
        $this->assertEquals( array( ), iterator_to_array( $pager->getResult( ) ) );
    }
}
?>
