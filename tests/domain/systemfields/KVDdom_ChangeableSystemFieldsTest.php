<?php
/**
 * @package     KVD.dom
 * @subpackage  systemfields
 * @version     $Id$
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

require_once( 'PHPUnit/Framework.php' );

/**
 * KVDdom_ChangeableSystemFieldsTest 
 * 
 * @package     KVD.dom
 * @subpackage  systemfields
 * @since       $Id$
 * @copyright   2009-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_ChangeableSystemFieldsTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->aangemaakt = new DateTime( 'last week' );
        $this->bewerkt = new DateTime( 'yesterday' );
        $this->sf = new KVDdom_ChangeableSystemFields( 'systeem', $this->aangemaakt, 1, 'vandaeko', $this->bewerkt );
    }

    public function tearDown( )
    {
        $this->sf = null;
    }

    public function testGetters( )
    {
        $this->assertEquals( 'systeem', $this->sf->getAangemaaktDoor( ) );
        $this->assertEquals( $this->aangemaakt, $this->sf->getAangemaaktOp( ) );
        $this->assertEquals( 'vandaeko', $this->sf->getBewerktDoor( ) );
        $this->assertEquals( $this->bewerkt, $this->sf->getBewerktOp( ) );
        $this->assertEquals( 1, $this->sf->getVersie() );
    }

    public function testUpdate( )
    {
        $this->assertTrue( $this->sf->isBewerkt( ) );
        $this->assertEquals( $this->sf->getVersie( ), $this->sf->getTargetVersie(  ) );
        $this->sf->setUpdated( 'goessebr' );
        $this->assertTrue( $this->sf->isBewerkt( ) );
        $this->assertEquals( 'goessebr', $this->sf->getBewerktDoor( ) );
        $this->assertEquals( $this->sf->getVersie( )+1, $this->sf->getTargetVersie(  ) );
        $this->sf->setUpdated( );
        $this->assertTrue( $this->sf->isBewerkt( ) );
        $this->assertEquals( 'goessebr', $this->sf->getBewerktDoor( ) );
        $this->assertEquals( $this->sf->getVersie( )+1, $this->sf->getTargetVersie(  ) );
    }

    public function testNew( )
    {
        $sf = new KVDdom_ChangeableSystemFields( 'vandaeko' );
        $this->assertFalse( $sf->isBewerkt(  ) );
        $this->assertEquals( 'vandaeko', $sf->getAangemaaktDoor(  ) );
        $sf->setUpdated('goessebr' );
        $this->assertFalse( $sf->isBewerkt( ) );
        $this->assertEquals( 'goessebr', $sf->getBewerktDoor( ) );
        $this->assertEquals( $sf->getVersie( )+1, $sf->getTargetVersie(  ) );
    }

    public function testNull( )
    {
        $sf = KVDdom_ChangeableSystemFields::newNull( );
        $this->assertType( 'KVDdom_ChangeableSystemFields', $sf );
        $this->assertEquals( 'anoniem', $sf->getAangemaaktDoor(  ) );
        $this->assertEquals( 0, $sf->getVersie() );
        $this->assertFalse( $sf->isBewerkt( ) );
    }
}
?>
