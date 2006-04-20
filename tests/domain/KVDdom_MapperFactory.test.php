<?php

Mock::generate('KVDdom_Sessie');

class TestOfMapperFactory extends UnitTestCase
{
    private $_sessie;

    private $_mapperFactory;

    function setUp( )
    {
        $this->_sessie = new MockKVDdom_Sessie( $this );
        $mapperDirs = array ( '/', '/data', OEI_CLASSES_DIR . 'dm');
        $this->_mapperFactory = new KVDdom_MapperFactory (  $this->_sessie , $mapperDirs );
    }

    function tearDown( )
    {
        $this->_sessie = null;
        $this->_mapperFactory = null;
    }

    function testIlleggalDomainObjectName( )
    {
        try {
            $this->_mapperFactory->createMapper ( 'OnbestaandeClass' );
        } catch (Exception $e) {
            $pattern = "/Ongeldige DomainObject naam: OnbestaandeClass./i";
            $this->assertWantedPattern ( $pattern , $e->getMessage() );
        }
    }

    function testExisting()
    {
        $this->assertNotNull( $this->_mapperFactory->createMapper ( 'OEIdo_GebPersoon' ) );
    }

    function testNonExisting()
    {
        try {
            $this->_mapperFactory->createMapper ( 'OEIdo_GebVetteWorst' );
        } catch (Exception $e) {
            $pattern = "/Er werd geen mapper voor class OEIdo_GebVetteWorst gevonden./i";
            $this->assertWantedPattern ( $pattern , $e->getMessage() );
        }
    }
}
?>
