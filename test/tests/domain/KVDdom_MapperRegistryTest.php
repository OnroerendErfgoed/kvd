<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class genericDomainObject {
}

class genericDataMapper {
}

class genericDataMapperXml {
}

class genericDataMapperDb {
}

/**
 * KVDdom_MapperRegistryTest 
 * 
 * @package     KVD.dom
 * @since       1.4.1
 * @copyright   2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class KVDdom_MapperRegistryTest extends PHPUnit_Framework_TestCase
{
    public function setUp(  )
    {
        $this->mapper = new GenericDataMapper(  );
        $this->sessie = new stdClass( );
    }

    public function tearDown( )
    {
        $this->mapper = null;
    }

    public function testGetMapper()
    {
        $mapperFactory = $this->getMock('KVDdom_MapperFactory', array(), array( $this->sessie, array(  ) ) );
        $mapperFactory->expects( $this->once() )->method( 'createMapper' )->will( $this->returnValue( $this->mapper ) );

        $mapperRegistry = new KVDdom_MapperRegistry( $mapperFactory );
        $this->assertSame ($mapperRegistry->getMapper('genericDomainObject') , $this->mapper );
        $this->assertSame ($mapperRegistry->getMapper('genericDomainObject') , $this->mapper );
    }

    public function testGetDefaultMapper( )
    {
        $mapperConfig = array ( 'genericDomainObject' => array( 'mappers' => array( 'default' => 'xml', 'db' => '', 'xml' => '' ) ) );
        $mapperFactory = $this->getMock('KVDdom_MapperFactory', array(), array( $this->sessie, array(  ) ) );

        $mapperXml = new GenericDataMapperXml(  );
        $mapperFactory->expects( $this->once() )->method( 'createMapper' )->with( 'genericDomainObject', 'xml' )->will( $this->returnValue( $mapperXml ) );

        $mapperRegistry = new KVDdom_MapperRegistry( $mapperFactory, $mapperConfig );
        $this->assertSame ( $mapperXml, $mapperRegistry->getMapper('genericDomainObject') );
    }

    /**
     * Test of er een LogicException is indien er voor een bepaald domainobject 
     * meerdere mappers zijn, maar geen default mapper. 
     * 
     * @expectedException   LogicException
     */
    public function testOnbestaandeDefaultMapper(  )
    {
        $mapperConfig = array ( 'genericDomainObject' => array( 'mappers' => array( 'db' => '', 'xml' => '' ) ) );
        $mapperFactory = $this->getMock('KVDdom_MapperFactory', array(), array( $this->sessie, array(  ) ) );

        $mapperRegistry = new KVDdom_MapperRegistry( $mapperFactory, $mapperConfig );
        $mapperRegistry->getMapper( 'genericDomainObject' );
    }


    public function testGetOtherMapper( )
    {
        $mapperConfig = array ( 'genericDomainObject' => array( 'mappers' => array( 'default' => 'xml', 'db' => '', 'xml' => '' ) ) );
        $mapperFactory = $this->getMock('KVDdom_MapperFactory', array(), array( $this->sessie, array(  ) ) );

        $mapperDb = new GenericDataMapperDb(  );
        $mapperFactory->expects( $this->once() )->method( 'createMapper' )->with( 'genericDomainObject', 'db' )->will( $this->returnValue( $mapperDb ) );

        $mapperRegistry = new KVDdom_MapperRegistry( $mapperFactory, $mapperConfig );
        $mapperRegistry->setDefaultMapper( 'genericDomainObject', 'db' );
        $this->assertSame ( $mapperDb, $mapperRegistry->getMapper('genericDomainObject') );
    }

    /**
     * Testen dat we geen onbestaande mapper als default kunnen instellen.
     * 
     * @expectedException   LogicException
     */
    public function testSetOngeldigeDefaultMapper1( )
    {
        $mapperConfig = array ( 'genericDomainObject' => array( 'mappers' => array( 'default' => 'xml', 'db' => '', 'xml' => '' ) ) );
        $mapperFactory = $this->getMock('KVDdom_MapperFactory', array(), array( $this->sessie, array(  ) ) );

        $mapperRegistry = new KVDdom_MapperRegistry( $mapperFactory, $mapperConfig );
        $mapperRegistry->setDefaultMapper( 'genericDomainObject', 'soap' );
    }

    /**
     * Testen dat we geen default mapper kunnen instellen voor een class die 
     * maar 1 mapper heeft.
     * 
     * @expectedException   LogicException
     */
    public function testSetOngeldigeDefaultMapper2( )
    {
        $mapperConfig = array ( 'genericDomainObject' => array( 'mappers' => array( 'default' => 'xml', 'db' => '', 'xml' => '' ) ) );
        $mapperFactory = $this->getMock('KVDdom_MapperFactory', array(), array( $this->sessie, array(  ) ) );

        $mapperRegistry = new KVDdom_MapperRegistry( $mapperFactory, $mapperConfig );
        $mapperRegistry->setDefaultMapper( 'ericDomainObject', 'db' );
    }



}
?>
