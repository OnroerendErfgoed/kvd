<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @version    $Id$
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * Unit test voor KVDthes_EditeerbareDomainObjectCollection
 * 
 * @package    KVD.thes
 * @subpackage core
 * @since      15 jun 2011
 * @copyright  2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDthes_EditeerbareDomainObjectCollectionTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->sessie = $this->getMock( 'KVDthes_Sessie' );
        $this->zone = new KVDthes_TestTerm( 12, $this->sessie, 'Zone');
        $this->gebied = new KVDthes_TestTerm( 5, $this->sessie, 'Gebied');
        $this->locatie = new KVDthes_TestTerm( 567, $this->sessie, 'Locatie');
        $arr = array( 12 => $this->zone, 5 => $this->gebied, 567 => $this->locatie );
        $this->coll = new KVDthes_TestEditeerbareDomainObjectCollection( $arr );
    }

    public function tearDown( )
    {
       $this->sessie = null;
       $this->zone = null;
       $this->gebied = null;
       $this->locatie = null;
       $this->coll = null;
    }

    public function testGetImmutableCollection( )
    {
        $coll2 = $this->coll->getImmutableCollection( );
        $this->assertType( 'KVDthes_DomainObjectCollection', $coll2 );
        $this->assertTrue( $coll2->hasDomainObject( $this->zone ) );
        $this->assertTrue( $coll2->hasDomainObject( $this->gebied ) );
        $this->assertTrue( $coll2->hasDomainObject( $this->locatie ) );
    }
}
?>
