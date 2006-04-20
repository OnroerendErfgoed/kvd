<?php

Mock::generate('KVDdom_MapperFactory');

class genericDataMapper {
}

class TestOfMapperRegistry extends UnitTestCase
{

    function testGetMapper()
    {
        $mapperFactory = new MockKVDdom_MapperFactory( $this );
        $mapper = new GenericDataMapper( );
        $mapperFactory->setReturnReference('createMapper' , $mapper , array ('GenericDataMapper'));
        $mapperFactory->expectOnce('createMapper' , array('GenericDataMapper'));

        $mapperRegistry = new KVDdom_MapperRegistry( $mapperFactory );
        $this->assertReference ($mapperRegistry->getMapper('GenericDataMapper') , $mapper );
        $this->assertReference ($mapperRegistry->getMapper('GenericDataMapper') , $mapper );
        $mapperFactory->tally();
    }

}
?>
