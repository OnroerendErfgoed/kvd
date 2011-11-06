<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDdom_ValueDomainObjectTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $this->do = new KVDdom_TestValueDomainObject( 1, 'Dit is een test.', array ( 'naam' => 'Van Daele',
                                                                                     'voornaam' => 'Henk' ) );
    }

    public function tearDown( )
    {
        $this->do = null;
    }

    public function testExists( )
    {
        $this->assertInstanceOf( 'KVDdom_TestValueDomainObject', $this->do );
    }

    public function testGetters( )
    {
        $this->assertEquals( 1, $this->do->getId( ) );
        $this->assertEquals( 'Dit is een test.', $this->do->getTitel( ) );
        $this->assertEquals( 'Dit is een test.', $this->do->getOmschrijving( ) );
        $this->assertEquals( 'Van Daele', $this->do->getNaam( ) );
        $this->assertEquals( 'Henk', $this->do->getVoornaam( ) );
    }

    public function testSetters( )
    {
        $this->do->setNaam( 'Van Daele' );
        $this->assertEquals( 'Van Daele', $this->do->getNaam( ) );
    }

    /**
     * testUnexistingProperty 
     * 
     * @expectedException   KVDdom_Fields_Exception
     */
    public function testUnexistingProperty( )
    {
        $this->do->setGeboorteplaats( 'Knokke-Heist' );
    }

    /**
     * testUnexistingProperty 
     * 
     * @expectedException   KVDdom_Exception
     */
    public function testUnexistingFunction(  )
    {
        $this->do->createGeboorteplaats( 'Knokke-Heist' );
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
