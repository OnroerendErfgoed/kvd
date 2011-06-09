<?php
Mock::generate( 'KVDdom_SqlLogger' );
Mock::generate( 'DatabaseManager' );
class TestofSessie extends UnitTestCase
{
    private $sessie;

    private $gebruikerId;

    private $databaseManager;

    private $sqlLogger;

    private $config;
    
    function setUp( )
    {
        $this->gebruikerId = 0;
        $this->databaseManager = new MockDatabaseManager( );
        $this->sqlLogger = new MockKVDdom_SqlLogger( );
        $this->config = array (  'gebruikerClass' => 'MockGebruiker' ,
                                 'dataMapperDirs' => array() );
        $this->sessie = new KVDdom_Sessie ( $this->gebruikerId , $this->databaseManager , $this->config , $this->sqlLogger);
    }

    function tearDown( )
    {
        $this->sessie = null;
    }

    function testNoDataMapperDirs( )
    {
        $config = $this->config;
        unset( $config['dataMapperDirs'] );
        try {
            $sessie = new KVDdom_Sessie ( $this->gebruikerId , $this->databaseManager , $config , $this->sqlLogger);
            $this->fail( );
        } catch ( InvalidArgumentException $e ) {
            $this->pass( );
        }
    }

    function testNoGebruikerClass( )
    {
        $config = $this->config;
        unset( $config['gebruikerClass'] );
        try {
            $sessie = new KVDdom_Sessie ( $this->gebruikerId , $this->databaseManager , $config , $this->sqlLogger);
            $this->fail( );
        } catch ( InvalidArgumentException $e ) {
            $this->pass( );
        }
    }

    function testNoGatewayRegistry( )
    {
        $config = $this->config;
        try {
            $sessie = new KVDdom_Sessie( $this->gebruikerId, $this->databaseManager, $config, $this->sqlLogger );
            $sessie->getGateway( 'test' );
            $this->fail( );
        } catch ( LogicException $e ) {
            $this->pass( );
        }
    }
    
    function testIdentityMap()
    {
        $this->assertNotNull ($this->sessie->getIdentityMap());
        $this->assertIsA ($this->sessie->getIdentityMap(), 'KVDdom_GenericIdentityMap');
    }
/*
    function testInsertDomainObject( )
    {
        $locatie = new MockVM_Locatie ( $this );
        $AE = new MockkzlCAI_AdministratieveEenheid ();
        $AE->setReturnValue('getId', '0');
        $locatie->setReturnValue('getId', '54321');
        $locatie->setReturnValue('getClass', 'VM_Locatie');
        $locatie->setReturnReference( 'getAdministratieveEenheid', $AE);
        $locatie->expectAtLeastOnce('getId');
        $sessie->registerNew ( $locatie );
        $results = $sessie->commit();
        $this->assertEqual ($results['insert'] , 1);
        $this->assertEqual ($results['update'] , 0);
        $this->assertEqual ($results['delete'] , 0);
    }
*/
}
?>
