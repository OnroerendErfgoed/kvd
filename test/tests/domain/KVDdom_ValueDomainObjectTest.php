<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
require_once ( 'PHPUnit/Framework.php' );

class KVDdom_ValueDomainObjectTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->do = new KVDdom_TestValueDomainObject( 1, 'Dit is een test.' );
    }

    public function tearDown( )
    {
        $this->do = null;
    }

    public function testExists( )
    {
        $this->assertType( 'KVDdom_TestValueDomainObject', $this->do );
    }

    public function testGetters( )
    {
        $this->assertEquals( 1, $this->do->getId( ) );
        $this->assertEquals( 'Dit is een test.', $this->do->getTitel( ) );
        $this->assertEquals( 'Dit is een test.', $this->do->getOmschrijving( ) );
    }

    public function testToString( )
    {
        $this->assertEquals( $this->do->getOmschrijving(  ), (string) $this->do );
    }

    public function testGetClass( )
    {
        $this->assertEquals( 'KVDdom_TestValueDomainObject', $this->do->getClass( ) );
    }
}
?>
