<?php

require_once ( 'PHPUnit/Framework.php' );

class genericDomainObject {
}

class genericDataMapper {
}

class genericDataMapperXml {
}

class genericDataMapperDb {
}

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





}
?>
