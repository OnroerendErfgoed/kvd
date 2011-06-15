<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_DomainObjectCollectionTest extends PHPUnit_Framework_Testcase
{
    public function setUp( )
    {
        $this->sessie = $this->getMock( 'KVDthes_Sessie' );
        $this->zone = new KVDthes_TestTerm( 12, $this->sessie, 'Zone');
        $this->gebied = new KVDthes_TestTerm( 5, $this->sessie, 'Gebied');
        $this->locatie = new KVDthes_TestTerm( 567, $this->sessie, 'Locatie');
        $arr = array( $this->zone, $this->gebied, $this->locatie );
        $this->coll = new KVDthes_DomainObjectCollection( $arr );
    }

    public function tearDown( )
    {
       $this->sessie = null;
       $this->zone = null;
       $this->gebied = null;
       $this->locatie = null;
       $this->coll = null;
    }

    public function testSortId( )
    {
        $this->coll->sort( KVDthes_TermSorter::SORT_ID );
        $arr = $this->coll->toArray( );
        $this->assertEquals( $this->gebied, $arr[0] );
        $this->assertEquals( $this->zone, $arr[1] );
        $this->assertEquals( $this->locatie, $arr[2] );
    }

    public function testSortTerm( )
    {
        $this->coll->sort( KVDthes_TermSorter::SORT_TERM );
        $arr = $this->coll->toArray( );
        $this->assertEquals( $this->gebied, $arr[0] );
        $this->assertEquals( $this->locatie, $arr[1] );
        $this->assertEquals( $this->zone, $arr[2] );
    }

    public function testSortQualTerm( )
    {
        $this->coll->sort( KVDthes_TermSorter::SORT_QUALTERM );
        $arr = $this->coll->toArray( );
        $this->assertEquals( $this->gebied, $arr[0] );
        $this->assertEquals( $this->locatie, $arr[1] );
        $this->assertEquals( $this->zone, $arr[2] );
    }
}
?>
