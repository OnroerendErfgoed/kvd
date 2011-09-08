<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDdom_ChangeableDomainObjectTest extends PHPUnit_Framework_TestCase
{
    public function setUp( )
    {
        $sessie = KVDdom_TestSessie::getInstance( );
        if ( !KVDdom_TestSessie::isInitialized(  ) ) {
            $sessie->initialize(    array(  'dataMapperDirs'         => array( ),
                                            'dataMapperParameters'   => array( ) ) );
        }
        $ouders = new KVDdom_EditeerbareDomainObjectCollection( array( ), 'KVDdom_TestChangeableDomainObject' );
        $data = array ( 'naam' => 'Van Daele', 'voornaam' => 'Koen', 'ouders' => $ouders );
        $this->do = new KVDdom_TestChangeableDomainObject( 1, $sessie, $data );

        $ouders = new KVDdom_EditeerbareDomainObjectCollection( array( ), 'KVDdom_TestChangeableDomainObject' );
        $data = array ( 'naam' => 'Van Daele', 'voornaam' => 'Henk', 'ouders' => $ouders );
        $this->vader = new KVDdom_TestChangeableDomainObject( 2, $sessie, $data );

        $ouders = new KVDdom_EditeerbareDomainObjectCollection( array( ), 'KVDdom_TestChangeableDomainObject' );
        $data = array ( 'naam' => 'Janssens', 'voornaam' => 'Patricia', 'ouders' => $ouders );
        $this->moeder = new KVDdom_TestChangeableDomainObject( 3, $sessie, $data );

        $ouders = new KVDdom_EditeerbareDomainObjectCollection( array( 2 => $this->vader, 3 => $this->moeder), 'KVDdom_TestChangeableDomainObject' );
        $data = array ( 'naam' => 'Van Daele', 'voornaam' => 'Annelies', 'ouders' => $ouders );
        $this->annelies = new KVDdom_TestChangeableDomainObject( 4, $sessie, $data );
    }

    public function tearDown( )
    {
        $this->do = null;
    }

    public function testExists( )
    {
        $this->assertType( 'KVDdom_TestChangeableDomainObject', $this->do );
    }

    public function testGetters( )
    {
        $this->assertEquals( $this->do->getId( ), 1 );
        $this->assertEquals( $this->do->getNaam( ), 'Van Daele' );
        $this->assertEquals( $this->do->getVoornaam(  ), 'Koen' );
        $this->assertEquals( new KVDdom_DomainObjectCollection( array( ) ), $this->do->getOuders( ) );
        $this->assertEquals( $this->do->getOmschrijving(  ), 'Van Daele, Koen' );
    }

    public function testSetters( )
    {
        $this->do->setNaam( 'Meganck' );
        $this->do->setVoornaam( 'Leen' );
        $this->assertEquals( $this->do->getOmschrijving(  ), 'Meganck, Leen' );
    }

    public function testAdd( )
    {
        $this->do->addOuder( $this->vader );
        $this->assertEquals( 1, count( $this->do->getOuders( ) ) );
    }

    public function testRemove( )
    {
        $this->assertEquals( 0, count( $this->do->getOuders( ) ) );
        $this->do->addOuder( $this->vader );
        $this->assertEquals( 1, count( $this->do->getOuders( ) ) );
        $this->do->removeOuder( $this->vader );
        $this->assertEquals( 0, count( $this->do->getOuders( ) ) );
    }

    public function testClear( )
    {
        $this->assertEquals( 0, count( $this->do->getOuders( ) ) );
        $this->do->addOuder( $this->vader );
        $this->do->addOuder( $this->moeder );
        $this->assertEquals( 2, count( $this->do->getOuders( ) ) );
        $this->do->clearOuders( );
        $this->assertEquals( 0, count( $this->do->getOuders( ) ) );
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

    /**
     * @expectedException   KVDdom_Fields_Exception 
     */
    public function testInvalidPlural(  )
    {
        $this->do->addZus( $this->annelies );
    }


    public function testToString( )
    {
        $this->assertEquals( $this->do->getOmschrijving(  ), (string) $this->do );
    }

    public function testGetClass( )
    {
        $this->assertEquals( 'KVDdom_TestChangeableDomainObject', $this->do->getClass( ) );
    }
}
?>
